<?php
include ("includes/config.php");
$dbQuery = "SELECT * FROM DownloadHistory WHERE HuluID IS NULL";
$result = mysql_query($dbQuery) or die (mysql_error());
while ($row = mysql_fetch_array($result)){
$huluID = explode ("/",$row['DownloadURL']);
$huluID = end($huluID);
$dbQuery5 = "Update DownloadHistory SET HuluID='".$huluID."' WHERE DownloadURL='".$row['DownloadURL']."'";
$result5 = mysql_query($dbQuery5) or die (mysql_error());
echo $dbQuery5;
}
exit;
header( 'Content-type: text/html; charset=utf-8' );
echo "<PRE>";
ob_start(); 
ob_implicit_flush(true);
ob_end_flush();
$command = 'get_flash_videos --subtitles "http://www.hulu.com/watch/427292" 2>&1';
system($command);
?> 
