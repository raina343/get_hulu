<?
header( 'Content-type: text/html; charset=utf-8' );
include ("includes/header.php");
include ("includes/config.php");
$dbQuery = "SELECT * FROM ProgramData WHERE id='".$_GET['id']."'";
$result = mysql_query($dbQuery) or die (mysql_error());
$row = mysql_fetch_array($result);
$dbQueryA = "SELECT HuluID FROM DownloadHistory WHERE HuluID='".$row['HuluID']."'";
//echo $dbQueryA;
$resultA = mysql_query($dbQueryA) or die (mysql_error());
$rowsA = mysql_num_rows($resultA);
if ($rowsA>0){
	print '<P>This show ('.$row['HuluID'].') has already been downloaded, and cannot be downloaded again</p>';
	exit;
}


print '<strong><p>Please leave this page open until the recording completes</p></strong>';
print '<div class="action"><ul class="action"><li class="action"><a class="action" onclick="window.close()" title="Close">Close</a></li></ul></div><p>Recording The Following Programmes</p><ul>';
if ($_GET['quick']=="true"){
	$Huluid = explode ("/",$_GET['id']);
	$Huluid = end($Huluid);
	$QueryURL = "http://www.hulu.com/api/2.0/info/video.json?_user_pgid=1&_content_pgid=67&_device_id=1&type=video&id=".$Huluid;
	$handle = fopen($QueryURL, "rb");
        $contents = stream_get_contents($handle);
        fclose($handle);
        $results = json_decode($contents);
	$row = array();
	$row['show_name'] = addslashes($results->show->name);
	$row['season_number'] = addslashes($results->season_number);
	$row['episode_number'] = addslashes($results->episode_number);
	$row['title'] = addslashes($results->title);
	print $row['show_name'].' - '.$row['title'];
}else {
	print '<li>'.$row['show_name'].' - '.$row['title'].'</li>';
	$Huluid = $row['HuluID'];
}
//exit;
print '</ul><br />';
echo "<PRE>";
ob_start();
ob_implicit_flush(true);
ob_end_flush();
//$command = 'get_flash_videos http://www.hulu.com/watch/'.$Huluid.' --filename="'.$row['show_name'].'-S'.$row['season_number'].'E'.$row['episode_number'].' '.$row['title'].'.flv" 2>&1';
$command = 'get_flash_videos http://www.hulu.com/watch/'.$Huluid.' --subtitles 2>&1';
//echo $command;
system($command,$returnvar);
ob_flush();
//echo $returnvar;
if ($returnvar =="0"){
	$dbQuery2 = "INSERT INTO DownloadHistory(HuluID,DownloadTime,DownloadURL) VALUES ('".$Huluid."','".date("Y-m-d H:i:s")."','http://www.hulu.com/watch/$Huluid')";
	$result2 = mysql_query($dbQuery2) or die (mysql_error());
	print '<br>Program downloaded sucessfully';
	print '<br><div class="action"><ul class="action"><li class="action"><a class="action" onclick="window.close()" title="Close">Close</a></li></ul></div>';
}else {
	print '<br>Program Failed to download';
	print '<br><div class="action"><ul class="action"><li class="action"><a class="action" onclick="window.close()" title="Close">Close</a></li></ul></div>';
}
?>


<?php
//header( 'Content-type: text/html; charset=utf-8' );
//echo "<PRE>";
//ob_start();
//ob_implicit_flush(true);
//ob_end_flush();
//$command = 'get_flash_videos --subtitles "http://www.hulu.com/watch/427292" 2>&1';
//system($command);
?>

