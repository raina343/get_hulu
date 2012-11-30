<?
include_once ("includes/contenttype.php");
include_once ("includes/config.php");
include_once ("includes/header.php");
$runPVR = "1";
//First check to see if the cache
//include ("refreshcontent.php");
ob_start();
ob_implicit_flush(true);
ob_end_flush();
echo "<PRE>";
$dbQuery = "SELECT * FROM PVRList";
$result = mysql_query($dbQuery) or die (mysql_error());
while ($row = mysql_fetch_array($result)){
	$dbQuery2 = "SELECT * FROM ProgramData WHERE show_name='".$row['show_name']."' AND is_subscriber_only='0'";
	echo "Running PVR Searches:".$row['show_name']."<br>";
//	echo $dbQuery2;
//exit;
	$result2 = mysql_query($dbQuery2) or die (mysql_error());
	while ($row2 = mysql_fetch_array($result2)){
//		print_r($row2);
		
		$dbQuery3 = "SELECT * FROM DownloadHistory WHERE HuluID='".$row2['HuluID']."'";
//		echo $dbQuery3."<br>";;
		$result3 = mysql_query($dbQuery3) or die (mysql_error());
		$rows3 = mysql_num_rows($result3);
		if ($rows3>0){ //This show has been downloaded already so skip it
		}else{
//			echo "Beginning Download of". $row2['HuluID'];
			$Huluid = $row2['HuluID'];
			ob_start();
			ob_implicit_flush(true);
			ob_end_flush();
			$command = 'get_flash_videos http://www.hulu.com/watch/'.$Huluid.' --subtitles 2>&1';
			system($command,$returnvar);
			ob_flush();
			//echo $returnvar;
			if ($returnvar =="0"){
			        $dbQuery22 = "INSERT INTO DownloadHistory(HuluID,DownloadTime,DownloadURL) VALUES ('".$Huluid."','".date("Y-m-d H:i:s")."','http://www.hulu.com/watch/$Huluid')";
			        $result22 = mysql_query($dbQuery22) or die (mysql_error());
			        print '<br>Program downloaded sucessfully';
//			        print '<br><div class="action"><ul class="action"><li class="action"><a class="action" onclick="window.close()" title="Close">Close</a></li></ul></div>';
			}else {
			        print '<br>Program Failed to download';
//			        print '<br><div class="action"><ul class="action"><li class="action"><a class="action" onclick="window.close()" title="Close">Close</a></li></ul></div>';
			}
		}
	}
}
