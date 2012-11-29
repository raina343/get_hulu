<?
include ("includes/config.php");
function dateDiff($start, $end) {
	$start_ts = strtotime($start);
	$end_ts = strtotime($end);
	$diff = $end_ts - $start_ts;
	return $diff;
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<script src="includes/jquery.min.js" type="text/javascript"></script>
<html><HEAD><TITLE>get_hulu Web PVR Manager v0.01</TITLE>
<link rel="shortcut icon" href="favicon.ico" />
<STYLE type="text/css">
	
	.pointer		{ cursor: pointer; cursor: hand; }
	.pointer:hover		{ text-decoration: underline; }

	.pointer_noul		{ cursor: pointer; cursor: hand; }

	.extra_border		{ border-left: 2px solid #666; }
	.all_borders		{ border-left: 2px solid #666; border-right: 2px solid #666; border-top: 2px solid #666; border-bottom: 2px solid #666; }

	.darker			{ color: #7D7D7D; }
	#logo			{ width: 190px; border-width: 0 0 1px 0; }
	#underline		{ text-decoration: underline; }
	#nowrap			{ white-space: nowrap; }
	#smaller80pc		{ font-size: 80%; }

	BODY			{ color: #FFF; background: black; font-size: 90%; font-family: verdana, sans-serif; }
	IMG			{ border: 0; }
	INPUT			{ border: 0 none; background: #ddd; }
	A			{ color: #FFF; text-decoration: none; }
	A:hover			{ text-decoration: none; }

	TABLE.title 		{ font-size: 150%; border-spacing: 0px; padding: 0px; }
	A.title			{ color: #F54997; font-weight: bold; font-family: Arial,Helvetica,sans-serif; }

	/* Nav bar */
	DIV.nav			{ font-family: Arial,Helvetica,sans-serif; background-color: #000; color: #FFF; }
	UL.nav			{ cursor: pointer; cursor: hand; padding-left: 0px; background-color: #000; font-size: 100%; font-weight: bold; height: 44px; margin: 0; margin-left: 0px; list-style-image: none; overflow: hidden; }
	LI.nav_tab		{ padding-left: 0px; border-top: 1px solid #444; border-left: 1px solid #444; border-right: 1px solid #444; border-bottom: 1px solid #888; display: inline; float: left; height: 42px; margin: 0; width: 13%; }
	LI.nav_tab_sel		{ padding-left: 0px; border-top: 1px solid #888; border-left: 1px solid #888; border-right: 1px solid #888; border-bottom: 0px solid #888; display: inline; float: left; height: 42px; margin: 0; width: 13%; }
	A.nav			{ display: block; height: 42px; line-height: 42px; text-align: center; }
	IMG.nav			{ padding: 7px; display: block; text-align: center; text-decoration: none; }
	A.nav:hover		{ color: #ADADAD; }

	TABLE.header		{ font-size: 80%; border-spacing: 1px; padding: 0; }
	INPUT.header		{ font-size: 80%; } 
	SELECT.header		{ font-size: 80%; } 

	TABLE.types		{ font-size: 70%; text-align: left; border-spacing: 0px; padding: 0; }
	TR.types		{ white-space: nowrap; }
	TD.types		{ width: 20px }
	
	TABLE.options_embedded	{ font-size: 100%; text-align: left; border-spacing: 0px; padding: 0; white-space: nowrap; }
	TR.options_embedded	{ white-space: nowrap; }
	TH.options_embedded	{ width: 20px }
	TD.options_embedded	{ width: 20px }

	/*DIV.options		{ padding-top: 10px; padding-bottom: 10px; font-family: Arial,Helvetica,sans-serif; background-color: #000; color: #FFF; }*/
	/* options_tab */
	UL.options_tab		{ text-align: left; cursor: pointer; cursor: hand; list-style-type: none; display: inline; padding-left: 0px; background-color: #000; font-size: 100%; font-weight: bold; height: 24px; margin: 0; margin-left: 0px; list-style-image: none; overflow: hidden; }
	/* selected tab button */
	LI.options_tab_sel	{ padding-left: 10px; padding-right: 10px; padding-bottom: 2px; padding-top: 2px; border-top: 1px solid #888; display: inline; float: left; border-left: 1px solid #888; border-right: 1px solid #888; border-bottom: 0px solid #888; margin: 0; margin-left: 0px; margin-bottom: 5px; }
	/* unselected tab button */
	LI.options_tab		{ padding-left: 10px; padding-right: 10px; padding-bottom: 2px; padding-top: 2px; border-top: 1px solid #444; display: inline; float: left; border-left: 1px solid #444; border-right: 1px solid #444; border-bottom: 1px solid #888; margin: 0; margin-left: 0px; margin-bottom: 5px; }
	/* unselected tab button */
	LI.options_button	{ padding-left: 10px; padding-right: 10px; padding-bottom: 2px; padding-top: 2px; border-top: 1px solid #888; display: inline; float: left; border-left: 1px solid #888; border-right: 1px solid #888; border-bottom: 1px solid #888; margin: 0; margin-right: 5px; margin-bottom: 5px; }

	TABLE.options		{ font-size: 100%; text-align: left; border-spacing: 0px; padding: 0; white-space: nowrap; }
	TR.options		{ white-space: nowrap; }
	TH.options		{ padding-right: 4px; text-align: left; }
	TD.options		{ }
	LABEL.options		{ font-size: 100%; } 
	INPUT.options[type="radio"],INPUT.options[type="checkbox"] { font-size: 100%; background:none; }
	INPUT.options		{ font-size: 100%; } 
	SELECT.options		{ font-size: 100%; } 

	TABLE.options_outer	{ font-size: 70%; text-align: left; border-spacing: 0px 0px; padding: 0; white-space: nowrap; overflow: visible; table-layout: fixed; }
	TR.options_outer	{ vertical-align: top; white-space: nowrap; }
	TH.options_outer	{ }
	TD.options_outer	{ padding-right: 50px; }
	LABEL.options_outer	{ font-weight: bold; font-size: 120%; color: #F54997; font-family: Arial,Helvetica,sans-serif; } 
	LABEL.options_heading	{ font-weight: bold; font-size: 110%; color: #CCC; } 
	
	/* Action bar */
	DIV.action		{ padding-top: 10px; padding-bottom: 10px; font-family: Arial,Helvetica,sans-serif; background-color: #000; color: #FFF; }
	UL.action		{ padding-left: 0px; background-color: #000; font-size: 100%; font-weight: bold; height: 24px; margin: 0; margin-left: 0px; list-style-image: none; overflow: hidden; }
	LI.action		{ cursor: pointer; cursor: hand; padding-left: 0px; border-top: 1px solid #888; border-left: 1px solid #666; border-right: 1px solid #666; border-bottom: 1px solid #666; display: inline; float: left; height: 22px; margin: 0; margin-left: 2px; width: 13.0%; }
	A.action		{ color: #FFF; display: block; height: 42px; line-height: 22px; text-align: center; }
	IMG.action		{ padding: 7px; display: block; text-align: center; text-decoration: none; }
	A.action:hover		{ color: #ADADAD; }

	TABLE.pagetrail		{ font-size: 70%; text-align: center; font-weight: bold; border-spacing: 10px 0; padding: 0px; }
	#centered		{ height:20px; margin:0px auto 0; position: relative; }
	LABEL.pagetrail		{ color: #FFF; }
	LABEL.pagetrail-current	{ color: #F54997; }

	TABLE.colselect		{ font-size: 70%; color: #fff; background: #333; border-spacing: 2px; padding: 0; }
	TR.colselect		{ text-align: left; }
	TH.colselect		{ font-weight: bold; }
	INPUT.colselect		{ font-size: 70%; }
	LABEL.colselect		{ font-size: 70%; }
	
	TABLE.search		{ font-size: 70%; color: #fff; background: #333; border-spacing: 2px; padding: 0; width: 100%; }
	TABLE.searchhead	{ font-size: 110%; border-spacing: 0px; padding: 0; width: 100%; }
	TR.search		{ background: #444; }
	TR.search:hover		{ background: #555; }
	TH.search		{ color: #FFF; text-align: center; background: #000; text-align: center; }
	TD.search		{ text-align: left; }
	A.search		{ }
	LABEL.search		{ text-decoration: none; }
	INPUT.search		{ font-size: 70%; background: none; }
	LABEL.sorted            { color: #CFC; }
	LABEL.unsorted          { color: #FFF; }
	LABEL.sorted_reverse    { color: #FCC; }
	INPUT.edit		{ font-size: 100%; background: #DDD; }

	TABLE.info		{ font-size: 70%; color: #fff; background: #333; border-spacing: 2px; padding: 0; }
	TR.info			{ background: #444; }
	TR.info:hover		{ background: #555; }
	TH.info			{ color: #FFF; text-align: center; background: #000; text-align: center; }
	TD.info			{ text-align: left; }
	A.info			{ text-decoration: underline; }
	A.info:hover		{ }

	B.footer		{ font-size: 70%; color: #777; font-weight: normal; }
	</STYLE>
</HEAD>

	<script type="text/javascript">
	
	function RefreshTab(url, time, force ) {
		if ( force ) {
			window.location.href = url;
		}
		setTimeout( "RefreshTab('" + url + "'," + time + ", 1 )", time );
	}


	// global hash table for saving copy of form
	var form_backup = {};
	
	//
	// Copy all non-grouped form values into a global hash
	//
	function BackupFormVars( form ) {
		// empty out array
		for(var key in form_backup) {
			delete( form_backup[key] );
		}

		// copy forms elements
		var elem = form.elements;
		for(var i = 0; i < elem.length; i++) {
			// exclude radio and checkbox types - can be duplicate names in groups...
			if ( elem[i].type != "checkbox" && elem[i].type != "radio" ) {
				form_backup[ elem[i].name ] = elem[i].value;
			}
		} 
	}

	//
	// Copy all form values in the global hash into the specified form
	//	
	function RestoreFormVars( form ) {
		// copy form elements
		for(var key in form_backup) {
			form.elements[ key ].value = form_backup[key];
			// delete element
			delete( form_backup[key] );
		}
	}

	//
	// Hide show an element (and modify the text of the button/label)
	// e.g. document.getElementById('advanced_opts').style.display='table';
	//
	// Usage: show_options_tab( SELECTEDID, [ 'TAB1', 'TAB2' ] );
	// Displays first tab in list or tab suffixes
	// tab_TAB1 is the table element
	// option_TAB1 is the form variable
	// button_TAB1 is the label
	//
	function show_options_tab( selectedid, tabs ) {

		// selected tab element
		var selected_tab = document.getElementById( 'tab_' + selectedid );

		// Loop through the above tab elements
		for(var i = 0; i < tabs.length; i++) {
			var li     = document.getElementById( 'li_' + tabs[i] );
			var tab    = document.getElementById( 'tab_' + tabs[i] );
			var option = document.getElementById( 'option_' + tabs[i] );
			var button = document.getElementById( 'button_' + tabs[i] );
			if ( tab == selected_tab ) {
				tab.style.display = 'table-cell';
				tab.style.visibility = 'visible';
				option.value = 'yes';
				//button.innerHTML = '- ' + button.innerHTML.substring(2);
				button.style.color = '#F54997';
				//li.style.borderBottom = '0px solid #666';
				li.className = 'options_tab_sel';
			} else {
				tab.style.display = 'none';
				tab.style.visibility = 'collapse';
				option.value = 'no';
				//button.innerHTML = '+ ' + button.innerHTML.substring(2);
				button.style.color = '#ADADAD';
				//li.style.borderBottom = '1px solid #666';
				li.className = 'options_tab';
			}
		}
		return true;
	}
	
	//
	// Check/Uncheck all checkboxes named <name>
	//
	function check_toggle(f, name) {
		var empty_fields = "";
		var errors = "";
		var check;

		if (f.SELECTOR.checked == true) {
			check = 1;
		} else {
			check = 0;
		}

		// Loop through the elements of the form
		for(var i = 0; i < f.length; i++) {
			var e = f.elements[i];
			if (e.type == "checkbox" && e.name == name) {
				if (check == 1) {
					// First check if the box is checked (don't check a disabled box)
					if(e.checked == false && e.disabled == false) {
						e.checked = true;
					}
				} else {
					// First check if the box is not checked
					if(e.checked == true) {
						e.checked = false;
					}
				}
			}
		}
		return true;
	}

	//
	// Warn if none of the checkboxes named <name> are selected
	//
	function check_if_selected(f, name) {
		// Loop through the elements of the form
		for(var i = 0; i < f.length; i++) {
			var e = f.elements[i];
			if (e.type == "checkbox" && e.name == name && e.checked == true) {
				return true;
			}
		}
		return false;
	}

	//
	// Disable checkboxes named <name> that are selected
	//
	function disable_selected_checkboxes(f, name) {
		var empty_fields = "";
		var errors = "";
		var check;

		// Loop through the elements of the form
		for(var i = 0; i < f.length; i++) {
			var e = f.elements[i];
			if (e.type == "checkbox" && e.name == name) {
				// First check if the box is checked
				if(e.checked == true) {
					e.checked = false;
					e.disabled = true;
				}
			}
		}
		return true;
	}

	//
	// Submit Search only if enter is pressed from a textfield
	// Called as: onKeyDown="return submitonEnter(event);"
	//
	function submitonEnter(evt){
		var charCode = (evt.which) ? evt.which : event.keyCode
		if ( charCode == "13" ) {
			var searchbox = document.getElementById('searchbox').value
			var poststr2="Search=true&value="+searchbox;
			$.ajax({type: "POST",async:true,url: "programcontent.php",data: poststr2,success: function(html){$("#programcontent").empty();$("#programcontent").append(html);}});
		}
	}
	function submitonEnter2(evt){
		var charCode = (evt.which) ? evt.which : event.keyCode
		if ( charCode == "13" ) {
			var searchbox = document.getElementById('PVRSeries').value;
			window.open("addseries.php?manual=true&showURL="+encodeURIComponent(searchbox));
//			alert(searchbox);
//			var searchbox = document.getElementById('searchbox').value
//			var poststr2="Search=true&value="+searchbox;
//			$.ajax({type: "POST",async:true,url: "programcontent.php",data: poststr2,success: function(html){$("#programcontent").empty();$("#programcontent").append(html);}});
		}
	}

	</SCRIPT>
<body>
<form method="post" action="/" enctype="multipart/form-data" name="formheader">
<div class="nav"><ul class="nav"><li class="nav_tab" id="logo"><a class="nav" href="http://192.168.1.11/hulu">
<img class="nav" title="get_iplayer Web PVR Manager" height="32" src="images/hululogo.png" href="http://192.168.1.11/hulu/" width="96" /></a></li><li class="nav_tab_sel"><a class="nav" title="Main search page" href="http://192.168.1.11:1935/">Search</a></li><li class="nav_tab"><a class="nav" onclick="BackupFormVars(formheader); formheader.NEXTPAGE.value='search_history'; formheader.submit(); RestoreFormVars(formheader);" title="History search page">Recordings</a></li><li class="nav_tab"><a class="nav" onclick="BackupFormVars(formheader); formheader.NEXTPAGE.value='pvr_list'; formheader.submit(); RestoreFormVars(formheader);" title="List all saved PVR searches">PVR List</a></li>
<li class="nav_tab"><a HREF="runPVR.php" target="new" class="nav" onclick="" title="Run the PVR now - wait for the PVR to complete">Run PVR</a></li>
<li class="nav_tab"><a class="nav" onclick="if (! confirm('Please restart the Web PVR Manager service once the update has completed') ) { return false; } BackupFormVars(formheader); formheader.NEXTPAGE.value='update_script'; formheader.submit(); RestoreFormVars(formheader);" title="Update the Web PVR Manager and get_iplayer software - please restart Web PVR Manager after updating">Update Software</a></li><li class="nav_tab"><a class="nav" title="Show help and instructions" href="http://linuxcentre.net/projects/get_iplayer-pvr-manager/">Help</a></li></ul></div>
