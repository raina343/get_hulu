<?
header( 'Content-type: text/html; charset=utf-8' );
include_once ("includes/contenttype.php");
include_once ("includes/config.php");
include_once ("includes/header.php");
	function objectToArray($object)
	{
		$array=array();
		foreach($object as $member=>$data)
		{
			$array[$member]=$data;
		}
		return $array;
	}
//Get the latest Run Date
 // Start output buffer (if not enabled in php.ini)
ob_start();
ob_implicit_flush(true);
ob_end_flush();
ob_flush();
$Episodelist = array();
print '<P><strong>Refreshing Hulu Content.  Please wait.</strong><br>';
//First Lets check to see what episodes we already have, that were pay only that may now be free
//We're only going to do items in the PVR List, cos otherwise it could take forever as the library builds
$dbQueryPVR = "SELECT * FROM PVRList";
$resultPVR = mysql_query($dbQueryPVR) or die (mysql_error());
echo "Refreshing PVR Shows for newly Free Content<br>";
echo "<PRE>";
while ($rowPVR = mysql_fetch_array($resultPVR)){
	$QueryURL = "http://www.hulu.com/api/2.0/videos.json?free_only=true&include_seasons=true&order=asc&shorter_cache=true&show_id=".$rowPVR['HuluShowID']."&sort=original_premiere_date&video_type%5B%5D=episode&video_type%5B%5D=game&items_per_page=6400&position=0&_user_pgid=1&_content_pgid=67&_device_id=1";
		echo "Checking ".$rowPVR['show_name']."<br>";
		ob_flush();
	        $handle = fopen($QueryURL, "rb");
	        $contents = stream_get_contents($handle);
	        fclose($handle);
	        $results = json_decode($contents);
		$StuffIWant = $results->data;
		$newshows = 0;
		$removedshows = 0;
		foreach ($StuffIWant as $y){
			$id = $y->video->id;
			$substatus = $y->video->is_subscriber_only;
			$expires_at = $y->video->expires_at;
			$available_at = $y->video->available_at;
			if (($substatus=="")||($substatus=="0")){
				$dbQueryPVR3 = "Update ProgramData SET expires_at='".$expires_at."',available_at='".$available_at."',is_subscriber_only='0' WHERE HuluID='".$id."'";
				$resultPVR3 = mysql_query($dbQueryPVR3) or die (mysql_error());
				if (mysql_affected_rows()==TRUE){
					array_push($Episodelist,$y->video->show->name.' - '.$y->video->title);
					$newshows++;
				}
			}else {
				$dbQueryPVR3 = "Update ProgramData SET expires_at='".$expires_at."',available_at='".$available_at."',is_subscriber_only='1' WHERE HuluID='".$id."'";
				$resultPVR3 = mysql_query($dbQueryPVR3) or die (mysql_error());
				if (mysql_affected_rows()==TRUE){
					$removedshows++;
				}
			}
		}
		echo "Added ".$newshows." and Removed ".$removedshows."<br>";
}
//exit;
$dbQuery = "SELECT * FROM DataPull ORDER BY DateRun DESC Limit 1";
$result = mysql_query($dbQuery) or die (mysql_error());
$LastRun = mysql_fetch_array($result);
$runarray = array();
$showcount = 0;
$LastRunDate = strtotime($LastRun['DateRun']);
while ($LastRunDate<date(U)){
	array_push($runarray,$LastRunDate);
	$LastRunDate = $LastRunDate+86400;
}
//Now that we have the range of dates to query, we'll loop through that array and populate the database.
echo "</PRE>";
echo "Refreshing for new content<br>";
foreach ($runarray as $qq){
	echo ".";
	echo "<PRE>";
	ob_flush();
        flush();
	$firstdate = date("c",$qq);
	$seconddate = date("c",$qq+86400);
	$url = "http://www.hulu.com/api/2.0/videos.json?free_only=0&order=desc&original_premiere_date_gte=".urlencode ($firstdate)."&original_premiere_date_lt=".urlencode($seconddate)."&sort=view_count_today&video_type=episode&items_per_page=256&position=0&_user_pgid=1&_content_pgid=67&_device_id=1";
//	echo $url."<br>";
	$handle = fopen($url, "rb");
	$contents = stream_get_contents($handle);
	fclose($handle);
	$results = json_decode($contents);
	$Importantstuff = $results->data;
//	print_r($Importantstuff);
	foreach ($Importantstuff as $y){
		$Data2 = $y->video;
		$Data = objectToArray($Data2);
		$Data['show'] = objectToArray($Data['show']);
		unset($Data['company']);
		unset($Data['cr_directors']);
		unset($Data['cr_countries']);
		unset($Data['video_game_id']);
		unset($Data['video_game']);
		$dbQuery = "SELECT * FROM ProgramData WHERE HuluID = '".$Data['id']."'";
		echo ".";
		$result = mysql_query($dbQuery) or die (mysql_error());
		$rows = mysql_num_rows($result);
		if ($rows>0){}else {
			array_push($Episodelist,$Data['show']['name'].' - '.$Data['title']);
			$showcount++;
			$dbQuery2 = "INSERT INTO ProgramData (HuluID,DateAdded) VALUES (";
			$dbQuery2 .=" '".$Data['id']."','".date("Y-m-d H:i:s")."')";
			$result2 = mysql_query($dbQuery2) or die (mysql_error());
			$dbQuery3 = "Update ProgramData SET ";
			$x=0;
			while ($x<count($Data)){
				if (key($Data)=="id"){
					$key = "HuluID";
				}else {
					$key = key($Data);
				}
				$dbQuery3 .= "`".$key."`";
				$dbQuery3 .="='";
				$dbQuery3 .= mysql_real_escape_string(trim(current($Data)));
				$dbQuery3 .="',";
				next ($Data);
			$x++;
			}
			$w=0;
			$showData = $Data['show'];
			while ($w<count($showData)){
				if (key($showData)=="id"){
	                                $key = "ShowID";
	                        }else {
	                                $key = "show_".key($showData);
	                        }
				$dbQuery3 .= "`".$key."`";
	                        $dbQuery3 .="='";
	                        $dbQuery3 .= mysql_real_escape_string(trim(current($showData)));
	                        $dbQuery3 .="',";
	                        next ($showData);
				$w++;
			}
			if (substr($dbQuery3,-1)==","){;
				$dbQuery3 = substr($dbQuery3,0,-1);
			}
			$dbQuery3 .=" WHERE HuluID='".$Data['id']."'";
			$result = mysql_query($dbQuery3) or die (mysql_error());
		}
	}
}

$dbQuery5 = "INSERT INTO DataPull(DateRun) VALUES ('".date("Y-m-d")."')";
$result5 = mysql_query($dbQuery5) or die (mysql_error());

print '<br>Refresh Complete - '.$showcount.' new episodes added</br>';
print '<ul>';
foreach ($Episodelist as $w){
	print '<li>'.$w.'</li>';
}
print '</ul>';
if ($runPVR=="1"){}else{
print '<br><div class="action"><ul class="action"><li class="action"><a class="action" onclick="window.close()" title="Close">Close</a></li></ul></div>';
}
//print_r($Episodelist);
