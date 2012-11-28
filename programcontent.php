<?
include ("includes/config.php");
//print_r($_POST);
if ($_POST['SortBy']==""){
	$_POST['SortBy'] = "DateAdded";
}
if ($_POST['direction']==""){
	$_POST['direction'] = "DESC";
}
if ($_POST['direction']=="DESC"){
	$otherdir = "ASC";
}else {
	$otherdir = "DESC";
}
?>
<table class="search">
<tr> <th class="search">
<label><input type="checkbox" name="SELECTOR" value="1" onclick="check_toggle(document.form, 'PROGSELECT')" title="Select/Unselect All Programmes" class="search" /></label></th>
<th class="search">Actions</th> <th class="search"><table class="searchhead">
<tr class="search"><th class="search"><label class="unsorted pointer" onclick="" title="Sort by thumbnail">Image</label></th></tr></table></th> 
<th class="search"><table class="searchhead"><tr class="search"><th class="search"><label class="unsorted pointer" onclick="" title="Sort by type">Type</label></th></tr></table></th>
<th class="search"><table class="searchhead"><tr class="search"><th class="search"><label class="unsorted pointer" onclick="getlist('sort','show_name','<?echo $otherdir;?>')" title="Sort by name">Name</label></th></tr></table></th>
<th class="search"><table class="searchhead"><tr class="search"><th class="search"><label class="unsorted pointer" onclick="getlist('sort','season_number','<?echo $otherdir;?>')" title="Sort by episode">Season</label></th></tr></table></th>
<th class="search"><table class="searchhead"><tr class="search"><th class="search"><label class="unsorted pointer" onclick="getlist('sort','episode_number','<?echo $otherdir;?>')" title="Sort by episode">Episode</label></th></tr></table></th>
<th class="search"><table class="searchhead"><tr class="search"><th class="search"><label class="unsorted pointer" onclick="getlist('sort','description','<?echo $otherdir;?>')" title="Sort by desc">Description</label></th></tr></table></th> 
<th class="search"><table class="searchhead"><tr class="search"><th class="search"><label class="unsorted pointer" onclick="" title="Sort by channel">Channel</label></th></tr></table></th> 
<th class="search"><table class="searchhead"><tr class="search"><th class="search"><label class="unsorted pointer" onclick="" title="Sort by categories">Categories</label></th></tr></table></th> 
<th class="search"><table class="searchhead"><tr class="search"><th class="search"><label class="unsorted pointer" onclick="getlist('sort','DateAdded','<?echo $otherdir;?>')" title="Sort by time added">Time Added</label></th></tr></table></th> </tr>

<?
//print_r($_POST);
if ($_POST['Search']=="true"){
$dbQuery = "SELECT * FROM ProgramData WHERE (show_name LIKE '%".mysql_real_escape_string($_POST['value'])."%' OR title LIKE '%".mysql_real_escape_string($_POST['value'])."%') AND (expires_at >'".date(c)."' OR expires_at='0000-00-00') ORDER BY ".$_POST['SortBy']." ".$_POST['direction'];
}else {
$dbQuery = "SELECT * FROM ProgramData WHERE expires_at >'".date(c)."' OR expires_at='0000-00-00' ORDER BY ".$_POST['SortBy']." ".$_POST['direction'];
}
//$dbQuery = "SELECT * FROM ProgramData WHERE expires_at >'".date(c)."' OR expires_at='0000-00-00' ORDER BY DateAdded DESC";
//echo $dbQuery;
$result = mysql_query($dbQuery) or die (mysql_error());
while ($row = mysql_fetch_array($result)){
print ' <tr class="search"><td class="search">';
print '<label><input type="checkbox" name="PROGSELECT" value="'.$row['id'].'" class="search" /></label></td>';
print '<td class="search"><a class="search" title="Play from Internet" href="http://www.hulu.com/watch/'.$row['HuluID'].'" target="new">Play</a><br>';
print '<a class="search" title="Record \''.$row['title'].'\' Now" style="cursor:pointer" href="record.php?id='.$row['id'].'" target="new">Record</a><br />';
print '<label class="search" title="Queue \''.$row['title'].'\' Now" for PVR Recording" id="nowrap">Queue</label><br />';
print '<a class="search" title="Add Series \''.$row['show_name'].'\' Now to PVR" HREF="addseries.php?id='.$row['id'].'" target="new" id="nowrap">Add Series</a></label></td> ';
print '<td class="search">';
print '<a class="search" title="Open original web URL" href="http://www.hulu.com/watch/'.$row['HuluID'].'" target="new"><img class="search" height="40" src="http://ib3.huluim.com/video/'.$row['content_id'].'?size=220x124" /></a></td>';
print '<td class="search"><label class="search" title="Click for full info">tv</label></td>';
print '<td class="search"><label class="search" title="Click to list \''.$row['show_name'].'\'" id="underline">'.$row['show_name'].'</label></td>';
print '<td class="search"><label class="search" title="Click for full info">'.$row['season_number'].'</label></td>';
print '<td class="search"><label class="search" title="Click for full info">'.$row['episode_number'].' - '.$row['title'].'</label></td>';
print '<td class="search"><label class="search" title="Click for full info">'.$row['description'].'</label></td>';
print '<td class="search"><label class="search" title="" id="underline"></label></td>';
print '<td class="search">';
$Cats = explode ("~",$row['show_genres']);
foreach ($Cats as $y){
$yy = explode ("|",$y);
foreach ($yy as $z){
	print '<label class="search" title="Click to list" id="underline">'.$z.'</label> ';
}
}
print '</td>';
print '<td class="search"><label class="search" title="Click for full info">'.$row['DateAdded'];
print '</label></td></tr>';
}
?>
</table>
