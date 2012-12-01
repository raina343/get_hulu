<?php
function recordshow($HuluID){
	header( 'Content-type: text/html; charset=utf-8' );
	include("includes/config.php");
	$dbQuery = "SELECT Value FROM Settings WHERE Setting='OutputPath'";
	$result = mysql_query($dbQuery) or die (mysql_error());
	$row = mysql_fetch_array($result);
	$outputpath = $row['Value'];
	$dbQuery = "SELECT Value FROM Settings WHERE Setting='Subtitles'";
	$result = mysql_query($dbQuery) or die (mysql_error());
	$row = mysql_fetch_array($result);
	if ($row['Value']=="true"){
		$subtitles = "--subtitles";
	}
	//echo "Output = ".$outputpath;
	//Record the program
	echo "<PRE>";
	$returnarray = array();
	$descriptorspec = array(0 => array("pipe", "r"),1 => array("pipe", "w"),2 => array("file", "/tmp/error-output.txt", "a"));
	$cwd = $outputpath."/";
	$env = array('some_option' => 'aeiou');
	$cmd = 'get_flash_videos '.$subtitles.' "http://www.hulu.com/watch/'.$HuluID.'" 2>&1';
	$process = proc_open($cmd, $descriptorspec, $pipes, $cwd, $env);
	flush();
	if (is_resource($process)) {
		while ($s = fgets($pipes[1])) {
		        print $s;
		        array_push($returnarray,$s);
		        flush();
		}
		$return_value = proc_close($process);
		if ($return_value=="0"){ //If it exits cleanly
			$returns = explode ("bytes to",end($returnarray));
			$savedfile = trim($returns[1]);
			$savedfile2 = substr($savedfile,0,-4);
		}else { //if not 
			echo "<BR>Saved Failed";
			exit;
		}
	}
	$cmd2 = "ffmpeg -i \"".trim($returns[1])."\" -acodec copy -vcodec copy \"".$savedfile2.".mp4\" 2>&1";
//Convert the program to mp4
        $descriptorspec2 = array(0 => array("pipe", "r"),1 => array("pipe", "w"),2 => array("file", "/tmp/error-output.txt", "a"));
        $cwd2 = $outputpath."/";
        $env2 = array('some_option' => 'aeiou');
        $process2 = proc_open($cmd2, $descriptorspec2, $pipes2, $cwd2, $env2);
        flush();
        if (is_resource($process2)) {
                while ($s2 = fgets($pipes2[1])){
                        print $s2;
                        flush();
                }
                $return_value2 = proc_close($process2);
                if ($return_value2=="0"){
			echo "<br><strong>clearing original flv</strong><br>";
			unlink ($outputpath."/".$savedfile);
			$dbQuery22 = "INSERT INTO DownloadHistory(HuluID,DownloadTime,DownloadURL) VALUES ('".$HuluID."','".date("Y-m-d H:i:s")."','http://www.hulu.com/watch/$HuluID')";
                        $result22 = mysql_query($dbQuery22) or die (mysql_error());
                }else {
                        echo "<BR><strong>Saved Failed</strong><br>";
                        exit;
                }
        }
}
