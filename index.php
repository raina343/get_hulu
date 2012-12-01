<?
include ("includes/config.php");
include ("includes/header.php");
include ("includes/options.php");
?>
<style type="text/css">
@import "includes/demo_table.css";
@import "includes/demo_page.css"; 
@import "includes/header.ccss";
@import "includes/demo_table.css";
.even                   {background: none repeat scroll 0 0 #444444;}
.odd                    {background: none repeat scroll 0 0 #444444;}

</style>
<table id="PCRList" width="100%" Cellspacing="0" cellpadding="0" border="0" class="search">
<thead>
<tr>
<th style="font-size:110%;border-spacing:0;text-align:center;font-family: verdana,sans-serif;background:black;" align="center" width="16">&nbsp;</th>
<th style="font-size:110%;border-spacing:0;text-align:center;font-family: verdana,sans-serif;background:black;" width="16" align="center" ><input type="checkbox" id="checkAllAuto" style="width:25px;"></th>
<th style="font-size:110%;border-spacing:0;text-align:center;font-family: verdana,sans-serif;background:black;" align="center">Actions</th>
<th style="font-size:110%;border-spacing:0;text-align:center;font-family: verdana,sans-serif;background:black;" align="center">Image</th>
<th style="font-size:110%;border-spacing:0;text-align:center;font-family: verdana,sans-serif;background:black;" align="center">Type</th>
<th style="font-size:110%;border-spacing:0;text-align:center;font-family: verdana,sans-serif;background:black;" align="center">Name</th>
<th style="font-size:110%;border-spacing:0;text-align:center;font-family: verdana,sans-serif;background:black;" align="center">Season</th>
<th style="font-size:110%;border-spacing:0;text-align:center;font-family: verdana,sans-serif;background:black;" align="center">Episode</th>
<th style="font-size:110%;border-spacing:0;text-align:center;font-family: verdana,sans-serif;background:black;" align="center">Description</th>
<th style="font-size:110%;border-spacing:0;text-align:center;font-family: verdana,sans-serif;background:black;" align="center">Categories</th>
<th style="font-size:110%;border-spacing:0;text-align:center;font-family: verdana,sans-serif;background:black;" align="center">Time Added</th>
<th style="font-size:110%;border-spacing:0;text-align:center;font-family: verdana,sans-serif;background:black;" width="16">&nbsp;</th>
</tr></thead>
<tbody id="billingcontent" style="font-weight:normal">
</tbody>
</table>



<div class="action"><ul class="action"><li class="action"><a class="action" onclick="BackupFormVars(form); form.NEXTPAGE.value='search_progs'; form.PAGENO.value=1; form.submit(); RestoreFormVars(form);" title="Perform search based on search options">Search</a></li> <li class="action"><a class="action" onclick="if(! ( check_if_selected(document.form, 'PROGSELECT') ||  form.URL.value ) ) { alert('No Quick URL or programmes were selected'); return false; } BackupFormVars(form); form.SEARCH.value=''; form.NEXTPAGE.value='record_now'; var random=Math.floor(Math.random()*99999); form.target='_newtab_'+random; form.submit(); RestoreFormVars(form); form.target=''; form.URL.value=''; disable_selected_checkboxes(document.form, 'PROGSELECT');" title="Immediately Record selected programmes (or Quick URL) in a new tab">Record</a></li> <li class="action"><a class="action" onclick="if(! ( check_if_selected(document.form, 'PROGSELECT') ||  form.URL.value ) ) { alert('No Quick URL or programmes were selected'); return false; } BackupFormVars(form); form.SEARCH.value=''; form.ACTION.value='genplaylist'; form.submit(); form.ACTION.value=''; RestoreFormVars(form); form.URL.value='';" title="Get a Playlist based on selected programmes (or Quick URL) to stream in your media player">Play</a></li> <li class="action"><a class="action" onclick="if(! ( check_if_selected(document.form, 'PROGSELECT') ||  form.URL.value ) ) { alert('No Quick URL or programmes were selected'); return false; } BackupFormVars(form); form.SEARCH.value=''; form.NEXTPAGE.value='pvr_queue'; form.submit(); RestoreFormVars(form); form.URL.value=''; disable_selected_checkboxes(document.form, 'PROGSELECT');" title="Queue selected programmes (or Quick URL) for one-off recording">Queue</a></li> <li class="action"><a class="action" onclick="if(! check_if_selected(document.form, 'PROGSELECT')) { alert('No programmes were selected'); return false; } BackupFormVars(form); form.SEARCH.value=''; form.ACTION.value='genplaylistdirect'; form.submit(); RestoreFormVars(form);" title="Get a Playlist based on selected programmes for remote file streaming in your media player">Play Remote</a></li> <li class="action"><a class="action darker" onclick="var version = 'default'; if ('.*' == '.*' &amp;&amp; 0 == 0 &amp;&amp; version.toLowerCase().indexOf('default') != -1) { alert('Search = .* will download all available programmes.  Please enter a more specific search term or additional advanced search criteria (excluding Search Future Schedule).'); return false; } if ('.*' == '' ) { alert('Please enter a search term. Use Search = .* to record all programmes matching advanced search criteria.'); return false; } if ( 1060 &gt; 30 ) { alert('Please limit your search to result in no more than 30 current programmes'); return false; }  BackupFormVars(form); form.NEXTPAGE.value='pvr_add'; form.submit(); RestoreFormVars(form);" title="Create a persistent PVR search using the current search terms (i.e. all below programmes)">Add Search to PVR</a></li> <li class="action"><a class="action" onclick="BackupFormVars(form); form.target='_newtab_refresh'; form.NEXTPAGE.value='refresh'; form.submit(); RestoreFormVars(form); form.target=''; form.NEXTPAGE.value=''; " title="Refresh the list of programmes - can take a while">Refresh Cache</a></li></ul></div><div id="status" /><div><input type="hidden" name=".cgifields" value="REFRESHFUTURE"  /><input type="hidden" name=".cgifields" value="FUTURE"  /><input type="hidden" name=".cgifields" value="SUBTITLES"  /><input type="hidden" name=".cgifields" value="HISTORY"  /><input type="hidden" name=".cgifields" value="REVERSE"  /><input type="hidden" name=".cgifields" value="PROGTYPES"  /><input type="hidden" name=".cgifields" value="THUMB"  /><input type="hidden" name=".cgifields" value="HIDE"  /><input type="hidden" name=".cgifields" value="HIDEDELETED"  /><input type="hidden" name=".cgifields" value="FORCE"  /><input type="hidden" name=".cgifields" value="COLS"  />
</div></form><p><b class="footer">get_iplayer Web PVR Manager v2.82, &copy;2009-2010 Phil Lewis - Licensed under GPLv3</b></p>
</body>
</html>
<script language="javascript">
$(document).ready(function() {
        $('.checkall').click(function () {
                $(this).parents('fieldset:eq(0)').find(':checkbox').attr('checked', this.checked);
        });
        $('#PCRList tbody td').live('click', function () {
        var oTable = jQuery("#PCRList").dataTable();
        var aPos = oTable.fnGetPosition( this );
        if ((aPos[1]=="0")||(aPos[1]=="7")){
        }else {
        var aData = oTable.fnGetData( aPos[0] );
        var id = aData[ 0 ];
//                document.location.href="dispatch.php?id="+id;
}
});
        var foo = innerHTML='<input type="checkbox">';
        $('#PCRList').dataTable( {
		"aaSorting": [[12,'desc']],
		"sDom": '<"top"iflp<"clear">>rt<"bottom"iflp<"clear">>',
                "bPaginate": true,
                "bLengthChange": true,
                "aLengthMenu": [[25, 50, 75, 100, -1], [25, 50, 75, 100, "All"]],
                "bProcessing": true,
                "sPaginationType": "full_numbers",
                "iDisplayLength": 25,
                "aoColumns": [
null,
                { "fnRender": function ( oObj ) {
                                return '<input name="PCR" type=\"checkbox\" style=\"width:20px\" value="'+ oObj.aData[0] +'"> ';
                 } },
                { "fnRender": function ( oObj ) {
                                return '<a class="search" target="new" href="http://www.hulu.com/watch/'+oObj.aData[2]+'" title="Play from Internet">Play</a><br><a class="search" target="new" href="record.php?id='+oObj.aData[0]+'" style="cursor:pointer" title="Record \''+ oObj.aData[11] +'\' Now">Record</a><br><label id="nowrap" class="search" title="Queue \''+ oObj.aData[11] +'\' Now">Queue</label><br><a id="nowrap" class="search" target="new" href="addseries.php?id='+oObj.aData[0]+'" title="Add Series \''+oObj.aData[5]+'\' Now to PVR">Add Series</a>';
                 } },

                { "fnRender": function ( oObj ) {
			return '<img class="search" height="40" src="http://ib3.huluim.com/video/'+oObj.aData[3]+'?size=220x124">';
                 } },
{ "sWidth": "20" },
{ "sWidth": "120" },
{ "sWidth": "20" },
{ "sWidth": "320" },
{ "sWidth": "520" },
{ "sWidth": "200" },

//              null,
//              null,
//              null,
//              null,
//		null,
//		null,
		null,
		null,
            ],
                "bFilter": true,
                "bInfo": false,
                "bAutoWidth": false,
                "sAjaxSource": "getprogramcontent.php?gettable=true",
                "aoColumnDefs": [{"bSortable":false,"aTargets":[0,1]},{ "bSearchable": false, "bVisible": false, "aTargets": [ 0,11 ] }]
        });
});

//getlist();
function getlist(type,value,extra){
	if (type=="sort"){
		var poststr2="Sort=true&SortBy="+value+"&direction="+extra;
	}
$.ajax({type: "POST",async:true,url: "programcontent.php",data: poststr2,success: function(html){$("#programcontent").empty();$("#programcontent").append(html);}});
}
function search(value){
//alert(value);
var oTable = $('#PCRList').dataTable();
oTable.fnFilter( value );
}
function quickrecord(){
	var url = document.getElementById('quickrecord').value;
	if (url==""){
		return false;
	}else {
		document.getElementById('quickrecord').value='';
		window.open("record.php?quick=true&id="+encodeURIComponent(url));
	}
}
</script>
