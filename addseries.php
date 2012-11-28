<?
include ("includes/config.php");
include ("includes/header.php");
$dbQuery = "SELECT show_name FROM ProgramData WHERE id='".$_GET['id']."'";
$result = mysql_query($dbQuery) or die (mysql_error());
$row = mysql_fetch_array($result);
print '<P><Strong>Adding show '.$row['show_name'].' to PVR</strong>';

$dbQuery2 = "DELETE FROM PVRList WHERE show_name='".$row['show_name']."'";
$result2 = mysql_query($dbQuery2) or die (mysql_error());
$dbQuery3 = "INSERT INTO PVRList (show_name,DateAdded) VALUES ('".$row['show_name']."','".date("Y-m-d H:i:s")."')";
$result3 = mysql_query($dbQuery3) or die (mysql_error());

