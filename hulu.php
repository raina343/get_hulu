<?
header( 'Content-type: text/html; charset=utf-8' );
include ("includes/config.php");
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

$dbQuery = "SELECT * FROM DataPull ORDER BY DateRun DESC Limit 1";
$result = mysql_query($dbQuery) or die (mysql_error());
$LastRun = mysql_fetch_array($result);
$runarray = array();
$LastRunDate = strtotime($LastRun['DateRun']);
while ($LastRunDate<date(U)){
	array_push($runarray,$LastRunDate);
	$LastRunDate = $LastRunDate+86400;
}
//Now that we have the range of dates to query, we'll loop through that array and populate the database.
foreach ($runarray as $qq){
	echo ".";
	ob_flush();
        flush();
	$firstdate = date("c",$qq);
	$seconddate = date("c",$qq+86400);
	$url = "http://www.hulu.com/api/2.0/videos.json?free_only=1&order=desc&original_premiere_date_gte=".urlencode ($firstdate)."&original_premiere_date_lt=".urlencode($seconddate)."&sort=view_count_today&video_type=episode&items_per_page=256&position=0&_user_pgid=1&_content_pgid=67&_device_id=1";
//	echo $url."<br>";
	$handle = fopen($url, "rb");
	$contents = stream_get_contents($handle);
	fclose($handle);
	$results = json_decode($contents);
	$Importantstuff = $results->data;
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
