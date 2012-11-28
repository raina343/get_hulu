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
	print '<P>This show has already been downloaded, and cannot be downloaded again</p>';
	exit;
}


print '<strong><p>Please leave this page open until the recording completes</p></strong>';
print '<div class="action"><ul class="action"><li class="action"><a class="action" onclick="window.close()" title="Close">Close</a></li></ul></div><p>Recording The Following Programmes</p><ul>';
if ($_GET['quick']=="true"){
	print $_GET['id'];
	$Huluid = explode ("/",$_GET['id']);
	$Huluid = end($Huluid);
//	echo $Huluid;	
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
$command = 'get_flash_videos http://www.hulu.com/watch/'.$Huluid.' --filename=\''.$row['show_name'].'-S'.$row['season_number'].'E'.$row['episode_number'].' '.$row['title'].'.flv\' 2>&1';
//$command = 'get_flash_videos -y --subtitles http://www.hulu.com/watch/'.$Huluid.' --filename=\''.$row['show_name'].'-S'.$row['season_number'].'E'.$row['episode_number'].' '.$row['title'].'.flv\' 2>&1';
system($command);
ob_flush();
$dbQuery2 = "INSERT INTO DownloadHistory(HuluID,DownloadTime,DownloadURL) VALUES ('".$Huluid."','".date("Y-m-d H:i:s")."','http://www.hulu.com/watch/$Huluid')";
$result2 = mysql_query($dbQuery2) or die (mysql_error());
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

