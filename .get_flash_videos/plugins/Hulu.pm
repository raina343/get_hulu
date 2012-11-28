# (C) 2009 Andrej Stepanchuk
# (C) 2009 Howard chu
# (C) 2009 ZakFlash / MonsieurVideo
# License: GPLv3

package FlashVideo::Site::Hulu;

use strict;
use FlashVideo::JSON;
use FlashVideo::URLFinder;
use FlashVideo::Utils;

use Carp;
use Data::Dumper;
use Digest::MD5 qw(md5_hex);
use Digest::HMAC_MD5 qw(hmac_md5_hex);
use Digest::SHA qw(sha1_hex);
use Encode;
use File::Path;
use File::Spec;
use HTML::Entities;
use HTTP::Headers;
use IO::Socket;
use POSIX qw(ceil strftime);
use Storable qw(nstore retrieve);
use URI::Escape;
use WWW::Mechanize::Link;

use constant HULU_PLAYER_1 => 'http://www.hulu.com/site-player/player.swf';
use constant HULU_PLAYER_2 => 'http://www.hulu.com/player.swf';

our @update_urls = (
  'http://gitorious.org/get-flash-videos-plugins/gfv-plugins/blobs/raw/release/Hulu.pm'
);

sub find_video {
  my ($self, $browser, $embed_url, $prefs) = @_;

  die "Must have XML::Simple installed to download from Hulu"
    unless eval { require XML::Simple };

  die "Must have Crypt::Rijndael installed to download from Hulu"
    unless eval { require Crypt::Rijndael };

  my $data_dir = get_user_config_dir() . "/hulu";
  File::Path::mkpath($data_dir) unless -d $data_dir;

  # Check whether Hulu login is enabled. If an account has Hulu Plus too,
  # then higher quality streams will be downloaded.
  my $token;

  if (login_enabled()) {
    my $account = $prefs->account(
      "hulu",
      "Enter Hulu username and password"
    );

    if (!$account->username or !$account->password) {
      die "Hulu login enabled but no username or password " .
          "supplied in ~/.netrc file";
    }

    debug "Got Hulu username (" . $account->username . ") and password, " .
          "checking token.";

    $token = check_token($browser, $account->username, $account->password);
  }

  # Sometimes we get redirected..
  $browser->allow_redirects;
  $browser->get($browser->response->header("Location")) if $browser->response->code =~ /30\d/;

  my $page_url = $browser->uri->as_string;

  my ($cid, $eid);
  if ($browser->content =~ m{huluim[.]com/[^/]+/(\d{4,})[?]size}) {
    $cid = $1;
    info "Found Hulu CID: $cid";
  }
  if ($browser->content =~ m{<link rel="video_src" href="http://www.hulu.com/embed.html[?]eid=?([A-Za-z0-9_-]{22})" />}
    or $browser->content =~ m{<link rel="media:video" href="http://www.hulu.com/embed.html[?]eid=?([A-Za-z0-9_-]{22})" />}
    or $browser->content =~ m{video_src":\s*"http://www.hulu.com/embed.html[?]eid=?([A-Za-z0-9_-]{22})"}
    or $browser->content =~ m{videoEmbedId = "([A-Za-z0-9_-]{22})"}
    )
  {
    $eid = $1;
    info "Found Hulu EID: $eid";
  }
  if (!$cid && !$eid) {
    die "Couldn't get Hulu CID or EID";
  }

  # For SWF verification
  my %swf_data = get_player($data_dir, $browser);

  # Get programme XML
  my $sidurl = "http://r.hulu.com/videos?" . ($cid ? "content_id=$cid" : "eid=$eid");
  $browser->get($sidurl);

  my $data = eval { XML::Simple::XMLin($browser->content) };
  if ($@) {
    if ($cid && $eid)
    {
      # Try the other one
      $sidurl = "http://r.hulu.com/videos?eid=$eid";
      $browser->get($sidurl);
      $data = eval { XML::Simple::XMLin($browser->content) };
      if ($@) {
        die "Couldn't parse Hulu XML: $@";
      }
    }
  }

  my $pid = $data->{video}->{pid};

  die "Couldn't get Hulu encrypted PID" unless $pid;

  my $output;
  my $outname;
  my $title = $data->{video}->{title};

  if ($data->{video}->{"media-type"} eq "TV") {
    my $show_name      = $data->{video}->{show}->{name};
    my $season         = $data->{video}->{"season-number"}->{content};
    my $episode_number = $data->{video}->{"episode-number"}->{content};

    $outname = sprintf '%s-S%02dE%02d-%s', $show_name, $season, $episode_number, $title;
  }
  else {
    $outname = $title;
  }

  $output = title_to_filename($outname);

  debug "Found Hulu encrypted PID: $pid";

  # Keys to decrypt the PID can be obtained (in theory) via gnash or
  # decswf.
  $pid = decrypt_id($pid);

  debug "Decrypted Hulu PID: '$pid'";

  # Get subtitles/captions if necessary.
  if ($prefs->subtitles && $data->{video}->{"has-captions"}->{content} eq "true") {
    # Two stage process for subtitles
    my $captions_url = "http://www.hulu.com/captions?content_id=$cid";
    $browser->get($captions_url);

    my $data = eval { XML::Simple::XMLin($browser->content) };
    if ($@) {
      die "Couldn't parse Hulu subtitle location XML: $@";
    }

    if (!$data->{en}) {
      die "Can't find Hulu subtitles - unexpected location XML structure";
    }

    $browser->get($data->{en});

    my $subs = $browser->content;
    my $file = title_to_filename($outname, "srt");

    info "Hulu subtitles file: $file";

    sami_subtitles_to_srt($subs, $file);
  }

  my $auth = md5_hex($pid . read_keys()->{player});
  debug "Auth: $auth";

  # From bluecop's XBMC Hulu plugin
  my $ts1 = time;

  my %hulu_video_params = (
    video_id => $cid,
    v        => '888324234',
    ts       => $ts1,
    np       => '1',
    vp       => '1',
    enable_fa => 1,
    device_id => get_guid(),
    pp       => 'Desktop',
    dp_id    => 'Hulu',
    region   => 'US',
    ep       => '1',
    language => 'en',
  );

  if ($token) {
    $hulu_video_params{token} = $token;
  }

  my $bcs1 = "";

  foreach my $item (sort keys %hulu_video_params) {
    $bcs1 .= $item . $hulu_video_params{$item};
  }

  my $bcs = hmac_md5_hex($bcs1,
    "f6daaa397d51f568dd068709b0ce8e93293e078f7dfc3b40dd8c32d36d2b3ce1");

  my $smil_file_url = "http://s.hulu.com/select?" .
    (join "&", map { "$_=$hulu_video_params{$_}" } sort keys %hulu_video_params) .
    "&bcs=$bcs";

  debug "Hulu SMIL URL: $smil_file_url";

  $browser->get($smil_file_url);
  debug "Encrypted XML: '" . $browser->content . "'";

  my $content = decrypt_smil($browser->content);

  # Read SMIL XML data
  my $data = eval { XML::Simple::XMLin($content) };
  if ($@) {
    die "Couldn't parse Hulu SMIL: $@";
  }

  # Access XML data
  debug "SMIL output: " . Dumper($data->{body}->{switch});

  my $vref = $data->{body}->{switch}[1]->{video};
  my @vids = ref($vref) eq 'ARRAY' ? @$vref : ($vref);
  my %ref =  %{ $data->{body}->{switch}[1]->{ref  } };

  my @cdnPrefs = ();

  @cdnPrefs = split /,/, $ref{'tp:cdnPrefs'} if defined($ref{'tp:cdnPrefs'});
  @cdnPrefs = split /,/, $ref{'tp:geoPrefs'} if defined($ref{'tp:geoPrefs'});

  debug "CdnPrefs: " . join(' ', @cdnPrefs);

  push @cdnPrefs, ''; # Last option empty string matches any

  my $quality = 0; # Always go for the best TODO XXX

  my @qtypes = ('p011', 'p010', 'H264 Medium', 'H264 650K', 'H264 400K', 'High', 'Medium', 'Low', 'H264');
  my $qtext;

  my $rtmpurl;
  my ($stream, $server, $token, $cdn);
  for my $q1 ($quality .. $#qtypes) {
    $qtext = $qtypes[$q1];

    for my $cdnPref (@cdnPrefs) {
      for my $vid (@vids) {
        next if (5 <= $q1 && $q1 <= 7 && $vid->{profile} =~ /H264/);
        #if ($vid->{profile} =~ /$qtext/ && $vid->{cdn} !~ /limelight/) {
        if ((!$cdnPref || $cdnPref eq $vid->{cdn}) && $vid->{profile} =~ /$qtext/) {
          $stream = $vid->{stream};
          $server = $vid->{server};
          $token = $vid->{token};
          $cdn = $vid->{cdn};
          last if defined($stream) and defined($server);
        }
      }
      last if defined($stream) and defined($server);
    }

    if (defined($stream) and defined($server)) {
      if ($q1 != $quality) {
        info "Using quality $qtext" if ($q1 != $quality);
      }
      elsif ($qtext =~ /p01[10]/) {
        info "Using HD stream ($qtext)";
      }

      last;
    }
  }

  die "Couldn't get RTMP url" unless $stream and $server;
  debug "RTMP CDN: $cdn Server: $server; Stream: $stream; Token: $token";

  (my $app = $server) =~ s@^rtmpe?://[^/]+/@@;

  return {
    flv => $output,
    rtmp => "$server?$token",
    app => "$app?$token",
    playpath => $stream,
    pageUrl => $page_url,
    %swf_data
  };

}

# Converts SAMI subtitles/captions to SRT (SubRip) format.
sub sami_subtitles_to_srt {
  my ($subtitle_data, $dest_filename) = @_;

  # Convert SAMI to SRT (SubRip)
  # SRT:
  #1
  #00:01:22,490 --> 00:01:26,494
  #Next round!
  #
  #2
  #00:01:33,710 --> 00:01:37,714
  #Now that we've moved to paradise, there's nothing to eat.
  #
  # SAMI:
  # timestamps are in milliseconds
  #   <Sync Start="18664">
  #      <P Class="ENCC">I got a call from an old friend,
  #      <br />heard Scylla was</P>
  #    </Sync>
  #    <Sync Start="20864">
  #      <P Class="ENCC">in play and he wants to know</P>
  #    </Sync>
  #    <Sync Start="21864">
  #      <P Class="ENCC">if he can get in on it.
  #      <br />I've set up a safe house</P>
  #    </Sync>
  #    <Sync Start="23598">
  #      <P Class="ENCC">where you can hear him out.</P>
  #    </Sync>
  #    <Sync Start="25031">
  #      <P Class="ENCC">Do you have a pen?
  #      <br />Just a minute.</P>
  #    </Sync>
  #    <Sync Start="26431">
  #      <P Class="ENCC">1917 Piermont.</P>
  #    </Sync>
  
  # TT:
  #<p begin="0:01:12.400" end="0:01:13.880">Thinking.</p>
  #<p begin="00:01:01.88" id="p15" end="00:01:04.80"><span tts:color="cyan">You're thinking of Hamburger Hill...<br /></span>Since we left...</p>
  #<p begin="00:00:18.48" id="p0" end="00:00:20.52">APPLAUSE AND CHEERING</p>

  $subtitle_data =~ s/[\r\n]//g; # flatten

  my @lines = split /<Sync\s/i, $subtitle_data;
  shift @lines; # Skip headers

  my @subtitles;
  my $count = 0;

  my $last_proper_sub_end_time = '';

  for (@lines) {
    my ($begin, $sub);
    # Remove span elements
    s|<\/?span.*?>| |g;
    
    # replace "&amp;" with "&"
    s|&amp;|&|g;

    # replace "&nbsp;" with " "
    s{&(?:nbsp|#160);}{ }g;

    # Start="2284698"><P Class="ENCC">I won't have to drink it<br />in this crappy warehouse.</P></Sync>
    #($begin, $sub) = ($1, $2) if m{.*Start="(.+?)".+<P.+?>(.+?)<\/p>.*?<\/Sync>}i;

    ($begin, $sub) = ($1, $2) if m{[^>]*Start="(.+?)"[^>]*>(.*?)<\/Sync>}i;
    if (/^\s*Encrypted="true"\s*/i) {
      $sub = decrypt_smil($sub);
      $sub =~ s@&amp;@&@g;
      $sub =~ s@(?:</?span[^>]*>|&nbsp;|&#160;)@ @g;
      $sub = decode_utf8($sub);
    }

    # Do some tidying up.
    # Note only <P> tags are removed--<i> tags are left in place since VLC
    # and others support this for formatting.
    $sub =~ s{</?P[^>]*?>}{}g;  # remove <P Class="ENCC"> and similar

    # VLC is very sensitive to tag case.
    $sub =~ s{<(/)?([BI])>}{"<$1" . lc($2) . ">"}eg;
    
    decode_entities($sub); # in void context, this works in place

    if ($begin >= 0) {
      # Convert milliseconds into HH:MM:ss,mmm format
      my $seconds = int( $begin / 1000.0 );
      my $ms = $begin - ( $seconds * 1000.0 );
      $begin = sprintf("%02d:%02d:%02d,%03d", (gmtime($seconds))[2,1,0], $ms );

      # Don't strip simple HTML like <i></i> - VLC and other players
      # support basic subtitle styling, see:
      # http://git.videolan.org/?p=vlc.git;a=blob;f=modules/codec/subtitles/subsdec.c

      # Leading/trailing spaces
      $sub =~ s/^\s*(.*?)\s*$/\1/;

      # strip multispaces
      $sub =~ s/\s{2,}/ /g;

      # Replace <br /> (and similar) with \n. VLC handles \n in SubRip files
      # fine. For <br> it is case and slash sensitive.
      $sub =~ s|<br ?\/? ?>|\n|ig;

      # Line breaks at the start of subs cause broken SRT files
      $sub =~ s/^\n+//;

      if ($count and !$subtitles[$count - 1]->{end}) {
        $subtitles[$count - 1]->{end} = $begin;
      }

      # SAMI subtitles are a bit crap. Only a start time is specified for
      # each subtitle. No end time is specified, so the subtitle is displayed
      # until the next subtitle is ready to be shown. This means that if
      # subtitles aren't meant to be shown for part of the video, a dummy
      # subtitle (usually just a space) has to be inserted.
      if (!$sub or $sub =~ /^\s+$/) {
        if ($count) {
          $last_proper_sub_end_time = $subtitles[$count - 1]->{end};
        }

        # Gap in subtitles.
        next; # this is not a meaningful subtitle
      }

      push @subtitles, {
        start => $begin,
        text  => $sub,
      };

      $count++;
    }
  }

  # Ensure the end time for the last subtitle is correct.
  $subtitles[$count - 1]->{end} = $last_proper_sub_end_time;

  # Write subtitles
  if (-s $dest_filename) {
    info "Subtitle file $dest_filename already exists and has stuff in it - not overwriting";
    return;
  }

  open my $subtitle_fh, ">", $dest_filename or die "Can't open $dest_filename: $!";

  # Subtitles are in UTF-8. In future, might want to remove some characters
  # like U+266A ("eighth note"). But perhaps not since VLC handles them
  # fine if it reads SRT files as UTF-8.
  #
  # Set filehandle to UTF-8 to avoid "wide character in print" warnings.
  # Note this does *not* double-encode data as UTF-8 (verify with hexdump).
  # As per the documentation for binmode: ":utf8 just marks the data as
  # UTF-8 without further checking". This will cause mojibake if Hulu mix
  # ISO-8859-1/Latin1 and UTF-8 and in the same file though.
  binmode $subtitle_fh, ':utf8';

  # Write each sub to file
  $count = 1;
  foreach my $subtitle (@subtitles) {
    print $subtitle_fh "$count\n$subtitle->{start} --> $subtitle->{end}\n" .
                       "$subtitle->{text}\n\n";
    $count++;
  }

  close $subtitle_fh;
}

sub get_player {
  my ($data_dir, $browser) = @_;

  my $headers = HTTP::Headers->new(
    'User-Agent'    => 'Mozilla/5.0 (compatible; MSIE 7.0; Windows NT 6.0)',
    'Accept'        => '*/*',
  );

  my $player = "$data_dir/player.swf";
  my $info   = "$data_dir/player.info";

  my $swf_data;

  my @playerurls = (HULU_PLAYER_1, HULU_PLAYER_2);
  my $fetched_ok = 0;

  if (-f $info) {
    $swf_data = retrieve($info); 
  }

  if ($swf_data->{timestamp}) {
    $headers->header("If-Modified-Since", $swf_data->{timestamp});
  }

  foreach my $playerurl (@playerurls) {
    debug "-> GET ".$playerurl;
    my $req = HTTP::Request->new('GET', $playerurl, $headers);
    my $res = $browser->request($req);
    debug "<- ".$res->code." ".$res->header("Content-Type");

    if ($res->code >= 400) {
      debug "Couldn't fetch HULU SWF player \"$playerurl\" -- ".$res->code." response";
      next;
    }

    if ($res->code != 304) {
      my $new_timestamp = $res->header("Last-Modified");
      debug "Newer Hulu SWF retrieved ($new_timestamp)";
      my $content = $res->content;

      # Check for compressed SWF
      return if substr($content, 0, 3) ne "CWS";

      # Cache player
      open my $player_fh, '>', $player or die "Can't open $player: $!";
      binmode $player_fh, ':raw'; # just treat this as bytes
      print $player_fh $content; 
      close $player_fh;

      # Get hash of the SWF
      my %swf_data = swfhash_data($content, $playerurl);

      $swf_data{timestamp} = $new_timestamp;

      # Update cached data
      nstore(\%swf_data, $info);

      delete $swf_data{timestamp};

      return %swf_data;
    }
    $fetched_ok = 1;
    last;
  }

  error "Error: Could not fetch HULU SWF player, using cached data"
    if !$fetched_ok;
  
  debug "Returning cached Hulu data";
  
  delete $swf_data->{timestamp};

  return %$swf_data;
}

sub decrypt_id {
  my ($encrypted_pid, $retry) = @_;

  my @keys = @{ read_keys()->{pid} };

  my @data = split /~/, $encrypted_pid;

  return $encrypted_pid if $data[1] eq '';

  # strip off server session encryption
  my $cipher = Crypt::Rijndael->new(pack("H*", $data[1]));
  my $tmp = $cipher->decrypt(pack("H*", $data[0]));

  debug "Session stripped PID: " . unpack("H*", $tmp);

  foreach my $key (@keys) {
    my $cipher = Crypt::Rijndael->new(pack("H*", $key));
    my $unencrypted_pid = $cipher->decrypt($tmp);

    debug "Using key $key";

    if ($unencrypted_pid =~ /[0-9A-Za-z_-]{32}/) {
      return $unencrypted_pid;
    }
  }

  # Couldn't obtain the PID, try to download new keys and try this again.
  if ($retry) {
    info "Tried to download new keys, but either they couldn't be downloaded " .
         "or don't work - try again later";
  }

  info "Couldn't obtain Hulu PID, the keys might have changed";
  info "Trying to obtain new keys...";
  download_keys();
  
  return decrypt_id($encrypted_pid, 1);
}

# Also used to decrypt subtitles
sub decrypt_smil {
  my $encrypted_smil = shift;
  my $encrypted_data = pack("H*", $encrypted_smil);

  my @xml_decrypt_keys = @{ read_keys()->{smil} };

  foreach my $key (@xml_decrypt_keys) {
    debug "XML decrypt key: $key->[0], IV: $key->[1]";

    my $smil = "";
    my $ecb = Crypt::Rijndael->new(pack("H*", @{$key}[0]));
    my $unaes = $ecb->decrypt($encrypted_data);

    # Ok, do some funny xor
    my $xorkey = pack("Z*", @{$key}[1]);
    $xorkey = substr($xorkey, 0, 16);

    for (my $i = 0; $i < ceil(length($encrypted_smil) / 32); $i++) {
      my $res = $xorkey ^ substr($unaes, $i*16, 16);
      $xorkey = substr($encrypted_data, $i*16, 16);

      $smil = "$smil$res";
    }

    # Remove padding
    my $lastchar = ord(substr($smil, -1));
    if (substr($smil, -$lastchar) == chr($lastchar) x $lastchar) {
      substr($smil, -$lastchar) = "";
    }

    if ($smil =~ /^(?:<smil|\s*<.+?>.*<\/.+?>)/i) { # Fix for transcripts
      return $smil;
    }
  }
  
  return 0;
}

sub read_keys {
  my $keys_file = get_user_config_dir() . "/hulu/keys";

  my $keys = eval { retrieve($keys_file) };
  if (!$@ and @$keys) {
    debug "Using Hulu keys from $keys_file";
    return $keys;
  }

  # Return hardcoded keys for now.
  my $keys = {
    pid => [
      '6fe8131ca9b01ba011e9b0f5bc08c1c9ebaf65f039e1592d53a30def7fced26c',
      'd3802c10649503a60619b709d1278ffff84c1856dfd4097541d55c6740442d8b',
      'c402fb2f70c89a0df112c5e38583f9202a96c6de3fa1aa3da6849bb317a983b3',
      'e1a28374f5562768c061f22394a556a75860f132432415d67768e0c112c31495',
      'd3802c10649503a60619b709d1278efef84c1856dfd4097541d55c6740442d8b'
    ],
    smil => [
      ['4878B22E76379B55C962B18DDBC188D82299F8F52E3E698D0FAF29A40ED64B21', 'WA7hap7AGUkevuth'],
      ['246DB3463FC56FDBAD60148057CB9055A647C13C02C64A5ED4A68F81AE991BF5', 'vyf8PvpfXZPjc7B1'],
      ['8CE8829F908C2DFAB8B3407A551CB58EBC19B07F535651A37EBC30DEC33F76A2', 'O3r9EAcyEeWlm5yV'],
      ['852AEA267B737642F4AE37F5ADDF7BD93921B65FE0209E47217987468602F337', 'qZRiIfTjIGi3MuJA'],
      ['76A9FDA209D4C9DCDFDDD909623D1937F665D0270F4D3F5CA81AD2731996792F', 'd9af949851afde8c'],
      ['1F0FF021B7A04B96B4AB84CCFD7480DFA7A972C120554A25970F49B6BADD2F4F', 'tqo8cxuvpqc7irjw'],
      ['3484509D6B0B4816A6CFACB117A7F3C842268DF89FCC414F821B291B84B0CA71', 'SUxSFjNUavzKIWSh'],
      ['B7F67F4B985240FAB70FF1911FCBB48170F2C86645C0491F9B45DACFC188113F', 'uBFEvpZ00HobdcEo'],
      ['40A757F83B2348A7B5F7F41790FDFFA02F72FC8FFD844BA6B28FD5DFD8CFC82F', 'NnemTiVU0UA5jVl0'],
      ['d6dac049cc944519806ab9a1b5e29ccfe3e74dabb4fa42598a45c35d20abdd28', '27b9bedf75ccA2eC'],
    ],
    player => "yumUsWUfrAPraRaNe2ru2exAXEfaP6Nugubepreb68REt7daS79fase9haqar9sa",
#    v => "810006400",
#    v => "435984533",
#    v => "850037518", # in newer hsc (June 2010) but now unused by the player?
     v => "713434170&np=1" # in new playback.swf (June 2010).. is accompanied by &np=1
  };

  return $keys;
}

sub login_enabled {
  return -f File::Spec->catfile(get_hulu_plugin_dir(), "login");
}

sub get_hulu_plugin_dir {
  return File::Spec->catfile(get_user_config_dir(), "hulu");
}

sub check_token {
  my ($browser, $username, $password) = @_;

  # Get previously saved authentication data.
  my $auth_file = File::Spec->catfile(get_hulu_plugin_dir(), "auth");

  if (!-s $auth_file) {
    debug "Hulu auth file doesn't exist, getting auth info from Hulu";

    return download_hulu_token($browser, $username, $password);
  }

  my $auth_data = retrieve($auth_file);

  # Check whether the token is still valid.
  my $expiry_time = $auth_data->{'token-expires-at'}->{content};
  my $now         = strftime("%Y-%m-%dT%H:%M:%SZ", gmtime);

  if ($now gt $expiry_time) {
    debug "Hulu token expired at $expiry_time, retrieving auth info again";

    return download_hulu_token($browser);
  }

  debug "Using cached Hulu token (expires at $expiry_time)";

  return $auth_data->{token};
}

sub download_hulu_token {
  my ($browser, $username, $password) = @_;

  my $parameters = {
    login    => $username,
    password => $password,
    nonce    => get_hulu_nonce($browser),
  };

  my $content = do_hulu_api_request($browser, 'authenticate', $parameters, 1);

  debug "Got authentication data from Hulu:\n$content";

  my $auth_details = from_xml($content);

  my $auth_file = File::Spec->catfile(get_hulu_plugin_dir(), "auth");

  nstore($auth_details, $auth_file);

  debug "Stored auth details in $auth_file";

  return $auth_details->{token};
}

sub get_hulu_nonce {
  my $browser = shift;

  my $content = do_hulu_api_request($browser, 'nonce', {}, 1);

  if ($content =~ m{<nonce>(.+?)</nonce>}) {
    return $1;
  }

  croak "Couldn't extract nonce from Hulu response";
}

sub do_hulu_api_request {
  my ($browser, $action, $parameters, $secure) = @_;

  $browser = $browser->clone;

  my $url;

  if ($secure) {
    $url = "https://secure.hulu.com/api/1.0/$action";
  }
  else {
    $url = "http://www.hulu.com/api/1.0/$action";
  }

  $parameters->{app} = 'f8aa99ec5c28937cf3177087d149a96b5a5efeeb';
  $parameters->{sig} = api_signature($action, $parameters);

  $browser->agent('Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)');
  $browser->add_header(Referer => 'http://download.hulu.com/huludesktop.swf?ver=0.1.0');

  $browser->post(
    $url,
    [
      %$parameters,
    ],
  );

  if (!$browser->success) {
    croak "Couldn't POST to $url: " . $browser->response->status_line;
  }

  return $browser->content;
}

sub api_signature {
  my ($action, $parameters) = @_;

  my $secret = 'mTGPli7doNEpGfaVB9fquWfuAis';

  my $parameters_list = join '', map { $_ . $parameters->{$_} }
                                 sort keys %$parameters;

  my $data = $secret . $action . $parameters_list;

  return sha1_hex($data);
}

sub get_guid {
  my @guid_chars = (0 .. 9, 'A' .. 'F');

  return join '',
         map { $guid_chars[ int rand($#guid_chars) ] }
         (0 .. 31);
}

sub search {
  my ($self, $search) = @_;

  my $browser = FlashVideo::Mechanize->new;

  # Why does mech make it so hard to submit a form with some extra fields
  # which aren't in the form? Annoying. Never mind, it's quicker like this
  # anyway (one request less).
  my $search = uri_escape($search);
  $browser->get("http://www.hulu.com/feed/search?query=$search&sort_by=relevance&st=0");

  if (!$browser->success) {
    debug "Couldn't do Hulu RSS search: " . $browser->response->status_line;
    return;
  }

  my $latest_episode_link;
  my @links;
  my @results;

  # First get links by parsing Hulu search RSS feed so that
  # we can get the duration of videos as well as the title and URL.
  my $rss = from_xml($browser->content);
  
  # Need to get search HTML to identify the latest episode.
  $browser->back();
  $browser->get("http://www.hulu.com/search?query=$search&st=0&sort_by=pub_date");

  my @mech_links = $browser->find_all_links(url_regex => qr'watch/\d+/\w+');

  if (!$browser->success) {
    info "Couldn't do Hulu search: " . $browser->response->status_line .
         ", details of most recent episode will not be available";
    return;
  }

  my $latest_episode_id = get_latest_episode_video_id($browser->content);

  @links = get_search_links_with_duration($rss, \@mech_links,
                                          $latest_episode_id);

  # If that didn't work, just use Mechanize. As an aside, it would be
  # really cool if find_all_links() supported a "HTML neighbourhood extra
  # info callback" for each link. (In other words, it would be called for
  # each link and would be given the parsed page with the current element
  # set to a link. The callback could then look for nearby <span> or other
  # elements, and then the callback's return value could influence whether
  # the link was returned.)
  @links = @mech_links if !@links;

  my %seen;

  foreach my $link (@links) {
    my $text = $link->text;

    chomp(my $name = $link->text);

    my $details = { name => decode_entities($name), url => $link->url_abs->as_string };

    if ($link->[50]) {
      $details->{description} = $link->[50];
    }

    if ($text =~ /latest episode/i) {
      $latest_episode_link = $details; 
    }
    else {
      push @results, $details unless $seen{ convert_hulu_link($details->{url}) }++;
    }
  }

  # People are probably more interested in the most recent episode, so put
  # it first in the list.
  if ($latest_episode_link) {
    unshift @results, $latest_episode_link;
  }

  return @results;
}

# Hulu list the same show multiple times with different URLs in search
# results :( For example:
#
#   http://www.hulu.com/watch/98266/homer-the-whopper
#   http://www.hulu.com/watch/98266/the-simpsons-homer-the-whopper
#
# Strip everything after the number.
sub convert_hulu_link {
  my $link = shift;

  if ($link =~ m{(http://www\.hulu\.com/watch/\d+)}) {
    $link = $1;
  }

  return $link;
}

sub get_latest_episode_video_id {
  my $content = shift;

  # Need to process JSON to figure out what the latest episode is.
  if ($content =~ /var v = new VideoList\((.*?)\);v\./) {
    my $video_json = from_json("[$1]");

    return unless ref($video_json) eq 'ARRAY';

    foreach my $item (@$video_json) { 
      next unless ref($item) eq 'HASH';

      my %videos = %{ $item };
      
      foreach my $video (keys %videos) {
        my $details = $videos{$video};

        next unless ref($details) eq 'HASH';

        if ($details->{link_html} =~ /Latest Episode/i) {
          if ($details->{link_url} =~ m{watch/(\d+)/}) {
            return $1;
          }
        }
      }
    }
  }

  return;
}

# Using find_all_links() is easy, but with this we can't get the duration
# of the videos, because that's in a <span> outside the <a> :(
sub get_search_links_with_duration {
  my ($rss, $mech_links, $latest_episode) = @_;

  my @links;

  debug "RSS output: " . Dumper($rss);

  foreach my $search_result (@{ $rss->{channel}->{item} }) {
    # Fix the title - convert from things like:
    #   The Simpsons - s22 | e1 - Elementary School Musical
    # to:
    #   The Simpsons-S22E01-Elementary School Musical
    (my $title = $search_result->{title}) =~ s/\bs(\d+) \| e(\d+)/sprintf "S%02dE%02d", $1, $2/e;

    # Get the duration and actual description out of the full description.
    my ($duration, $description);
    my $full_description = $search_result->{description};
    my $ishplus = $full_description =~ /Hulu Plus subscription required/i;
    my $hpmsg = "h+,preview";

    if ($full_description =~ /Duration: (\d+:\d+(?::\d\d)?)/) {
      $duration = $1;
      my $hpsfx = $ishplus ? ",$hpmsg" : "";
      $title .= " ($duration$hpsfx)";
    }
    elsif ($ishplus) {
      $title .= " ($hpmsg)";
    }

    if ($full_description =~ /<\/a><p>(.*?)<\/p>/) { # frail
      $description = $1;
    }

    if ($latest_episode and $search_result->{link} =~ /watch\/\Q$latest_episode\E/) {
      $title .= " (Latest episode)";
    }

    # Only include the main part of the URL, not the RSS details too.
    my $hulu_url = (split /#/, $search_result->{link})[0];

    # This is an abuse of WWW::Mechanize::Link, but oh well...
    my $link = WWW::Mechanize::Link->new({
      url  => $hulu_url,
      text => $title,
    });

    # Evil
    $link->[50] = decode_entities($description);

    push @links, $link;
  }

  return @links;
}

# search test
if (!caller) {
  my @search_results = __PACKAGE__->search('FlashForward');

  if ($ENV{DEBUG}) {
    use Data::Dumper;
    print Dumper \@search_results;
  }

  die "Failed - no search results" unless @search_results;
}

=head1 NAME

Hulu.pm - Hulu plugin for get_flash_videos.

=head2 DETAILS

This plugin allows get_flash_videos
(L<http://code.google.com/p/get-flash-videos/>) to download videos from
Hulu.

=head2 Hulu Plus support

The plugin has very limited support for Hulu Plus. To use this, you need a
Hulu Plus subscription. You must configure your Hulu account details in
your F<~/.netrc> file, for example:

  machine hulu login your_hulu_username password your_hulu_password

Once you've done this, you must also actually enable logging in to Hulu by
simply creating a file called F<login> in your F<~/.get_flash_videos/hulu>
directory. The content or length of the file is irrelevant; the mere
presence of the file enables logging in. You can quickly set this up using
C<touch>:

  mkdir -p ~/.get_flash_videos/hulu
  touch ~/.get_flash_videos/hulu/login

(Yes, having to enable this separately rather than just the presence of
account details in F<~/.netrc> is annoying, but the
C<FlashVideo::VideoPreferences::Account> part of get_flash_videos prompts
for account details if they don't exist in F<~/.netrc>.)

Not all videos on Hulu are available in HD. If a video you've selected
B<is> available in HD, C<get_flash_videos> will automatically download the
HD version. You'll see output like this:

  Using HD stream (p011)

=cut

1;
