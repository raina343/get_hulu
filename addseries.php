<?
include ("includes/config.php");
include ("includes/header.php");
function objectToArray($object){
	$array=array();
        foreach($object as $member=>$data){
	        $array[$member]=$data;
        }
        return $array;
}
if ($_GET['manual']=="true"){
//First we need to get the show ID
	$showdata = explode ("/",$_GET['showURL']);
	$showdata = end($showdata);
	$QueryURL="http://www.hulu.com/api/2.0/shelf_config.json?_user_pgid=1&_content_pgid=67&_device_id=1&location=show&canonical_name=".$showdata;
        $handle = fopen($QueryURL, "rb");
        $contents = stream_get_contents($handle);
        fclose($handle);
        $results = json_decode($contents);
	$data = $results[0];
	$show_id = $data->restParams->show_id;
	$QueryURL = "http://www.hulu.com/api/2.0/videos.json?include_seasons=true&order=asc&shorter_cache=true&show_id=".$show_id."&sort=original_premiere_date&video_type[]=episode&video_type[]=game&items_per_page=6400&position=0&_user_pgid=1&_content_pgid=67&_device_id=1";
        $handle = fopen($QueryURL, "rb");
        $contents = stream_get_contents($handle);
        fclose($handle);
        $results = json_decode($contents);
	$Importantstuff = $results->data;
	$ShowName = $Importantstuff[0];
	$ShowName = $ShowName->video->show->name;
	//Add the Show to the PVR List
	print '<P><Strong>Adding show '.$ShowName.' to PVR</strong>';
	$dbQuery2 = "DELETE FROM PVRList WHERE show_name='".$ShowName."'";
	$result2 = mysql_query($dbQuery2) or die (mysql_error());
	$dbQuery3 = "INSERT INTO PVRList (show_name,DateAdded,HuluShowID) VALUES ('".$ShowName."','".date("Y-m-d H:i:s")."','".$show_id."')";
	$result3 = mysql_query($dbQuery3) or die (mysql_error());
	//Now grab all available episodes for this show and add them to the show database (ignore duplicates)
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
echo "<br> Show Addition Completed";
print '<br><div class="action"><ul class="action"><li class="action"><a class="action" onclick="window.close()" title="Close">Close</a></li></ul></div>';
exit;
}

$dbQuery = "SELECT showid,show_name FROM ProgramData WHERE id='".$_GET['id']."'";
$result = mysql_query($dbQuery) or die (mysql_error());
$row = mysql_fetch_array($result);
print '<P><Strong>Adding show '.$row['show_name'].' to PVR</strong>';

$dbQuery2 = "DELETE FROM PVRList WHERE show_name='".$row['show_name']."'";
$result2 = mysql_query($dbQuery2) or die (mysql_error());
$dbQuery3 = "INSERT INTO PVRList (show_name,DateAdded,HuluShowID) VALUES ('".$row['show_name']."','".date("Y-m-d H:i:s")."','".$row['showid']."')";
$result3 = mysql_query($dbQuery3) or die (mysql_error());

