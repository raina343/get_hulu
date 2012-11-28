<?
include ("includes/config.php");
include ("includes/header.php");
function dateDiff($start, $end) {
	$start_ts = strtotime($start);
	$end_ts = strtotime($end);
	$diff = $end_ts - $start_ts;
	return $diff;
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
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
			document.form.NEXTPAGE.value='search_progs';
			document.form.PAGENO.value=1;
			document.form.submit();
		}
	}

	</SCRIPT>
<body>
<form method="post" action="/" enctype="multipart/form-data" name="formheader">
<div class="nav"><ul class="nav"><li class="nav_tab" id="logo"><a class="nav" href="http://192.168.1.11:1935/">
<img class="nav" title="get_iplayer Web PVR Manager" height="32" src="images/hululogo.png" href="http://192.168.1.11:1935/" width="96" /></a></li><li class="nav_tab_sel"><a class="nav" title="Main search page" href="http://192.168.1.11:1935/">Search</a></li><li class="nav_tab"><a class="nav" onclick="BackupFormVars(formheader); formheader.NEXTPAGE.value='search_history'; formheader.submit(); RestoreFormVars(formheader);" title="History search page">Recordings</a></li><li class="nav_tab"><a class="nav" onclick="BackupFormVars(formheader); formheader.NEXTPAGE.value='pvr_list'; formheader.submit(); RestoreFormVars(formheader);" title="List all saved PVR searches">PVR List</a></li><li class="nav_tab"><a class="nav" onclick="BackupFormVars(formheader); formheader.NEXTPAGE.value='pvr_run'; formheader.target='_newtab_pvrrun'; formheader.submit(); RestoreFormVars(formheader); formheader.target='';" title="Run the PVR now - wait for the PVR to complete">Run PVR</a></li><li class="nav_tab"><a class="nav" onclick="if (! confirm('Please restart the Web PVR Manager service once the update has completed') ) { return false; } BackupFormVars(formheader); formheader.NEXTPAGE.value='update_script'; formheader.submit(); RestoreFormVars(formheader);" title="Update the Web PVR Manager and get_iplayer software - please restart Web PVR Manager after updating">Update Software</a></li><li class="nav_tab"><a class="nav" title="Show help and instructions" href="http://linuxcentre.net/projects/get_iplayer-pvr-manager/">Help</a></li></ul></div><input type="hidden" name="AUTOPVRRUN" value="4"  /><input type="hidden" name="NEXTPAGE" value="search_progs"  /></form><form method="post" action="/" enctype="multipart/form-data" name="form">
<table class="options_outer"><tr class="options_outer"><td class="options_outer" rowspan="3" id="tab_BASICTAB" style="display: table-cell; visibility: visible;"><table class="options"><tr class="options"><th class="options" title="Enter your partial text match (or regex expression)">Search</th><td class="options" title="Enter your partial text match (or regex expression)"><input type="text" name="SEARCH" value=".*" size="20" onkeydown="return submitonEnter(event);" class="options" /></td></tr> <tr class="options"><th class="options" title="Select which column you wish to search">Search in</th><td class="options" title="Select which column you wish to search"><select name="SEARCHFIELDS"  onchange id="option_SEARCHFIELDS" class="options">
<option value="index">Index</option>
<option value="thumbnail">Image</option>
<option value="pid">Pid</option>
<option value="available">Availability</option>
<option value="type">Type</option>
<option selected="selected" value="name">Name</option>
<option value="episode">Episode</option>
<option value="versions">Versions</option>
<option value="duration">Duration</option>
<option value="desc">Description</option>
<option value="channel">Channel</option>
<option value="categories">Categories</option>
<option value="timeadded">Time Added</option>
<option value="guidance">Guidance</option>
<option value="web">Web Page</option>
<option value="seriesnum">Series Number</option>
<option value="episodenum">Episode Number</option>
<option value="filename">Filename</option>
<option value="mode">Mode</option>
<option value="name,episode">Name+Episode</option>
<option value="name,episode,desc">Name+Episode+Desc</option>
</select></td></tr> <tr class="options"><th class="options" title="Select the programme types you wish to search">Programme type</th><td class="options"><table class="options_embedded"><tr class="options_embedded"><td class="options"><table class="options_embedded" title="Select the programme types you wish to search"><tr class="options_embedded"><td class="options_embedded"><label><input type="checkbox" name="PROGTYPES" value="tv" checked="checked" id="option_PROGTYPES_tv" class="options" /></label></td> <td class="options_embedded">BBC TV</td></tr></table></td><td class="options"><table class="options_embedded" title="Select the programme types you wish to search"><tr class="options_embedded"><td class="options_embedded"><label><input type="checkbox" name="PROGTYPES" value="radio" id="option_PROGTYPES_radio" class="options" /></label></td> <td class="options_embedded">BBC Radio</td></tr></table></td><td class="options"><table class="options_embedded" title="Select the programme types you wish to search"><tr class="options_embedded"><td class="options_embedded"><label><input type="checkbox" name="PROGTYPES" value="podcast" id="option_PROGTYPES_podcast" class="options" /></label></td> <td class="options_embedded">BBC Podcast</td></tr></table></td><tr class="options_embedded"><td class="options"><table class="options_embedded" title="Select the programme types you wish to search"><tr class="options_embedded"><td class="options_embedded"><label><input type="checkbox" name="PROGTYPES" value="livetv" id="option_PROGTYPES_livetv" class="options" /></label></td> <td class="options_embedded">Live BBC TV</td></tr></table></td><td class="options"><table class="options_embedded" title="Select the programme types you wish to search"><tr class="options_embedded"><td class="options_embedded"><label><input type="checkbox" name="PROGTYPES" value="liveradio" id="option_PROGTYPES_liveradio" class="options" /></label></td> <td class="options_embedded">Live BBC Radio</td></tr></table></td><td class="options"><table class="options_embedded" title="Select the programme types you wish to search"><tr class="options_embedded"><td class="options_embedded"><label><input type="checkbox" name="PROGTYPES" value="localfiles" id="option_PROGTYPES_localfiles" class="options" /></label></td> <td class="options_embedded">localfiles</td></tr></table></td><tr class="options_embedded"></tr></table></td></tr> <tr class="options"><th class="options" title="Whether to display and search programmes in the recordings history">Search History</th><td class="options" title="Whether to display and search programmes in the recordings history"><label><input type="checkbox" name="HISTORY" value="on" id="option_HISTORY" class="options" /></label></td></tr> <tr class="options"><th class="options" title="Enter your URL for Recording (then click 'Record' or 'Play')">Quick URL</th><td class="options" title="Enter your URL for Recording (then click 'Record' or 'Play')"><input type="text" name="URL"  size="36" onkeydown="return submitonEnter(event);" class="options" /></td></tr></table></td><td class="options_outer"><ul class="options_tab"><li class="options_tab_sel" id="li_SEARCHTAB"><label class="options_outer pointer_noul" onclick="show_options_tab( 'SEARCHTAB', [ 'SEARCHTAB', 'DISPLAYTAB', 'COLUMNSTAB', 'RECORDINGTAB', 'STREAMINGTAB' ] );" title="Show Advanced Search tab" id="button_SEARCHTAB" style="color: #F54997;">Advanced Search</label></li> <li class="options_tab" id="li_DISPLAYTAB"><label class="options_outer pointer_noul" onclick="show_options_tab( 'DISPLAYTAB', [ 'SEARCHTAB', 'DISPLAYTAB', 'COLUMNSTAB', 'RECORDINGTAB', 'STREAMINGTAB' ] );" title="Show Display tab" id="button_DISPLAYTAB" style="color: #ADADAD;">Display</label></li> <li class="options_tab" id="li_COLUMNSTAB"><label class="options_outer pointer_noul" onclick="show_options_tab( 'COLUMNSTAB', [ 'SEARCHTAB', 'DISPLAYTAB', 'COLUMNSTAB', 'RECORDINGTAB', 'STREAMINGTAB' ] );" title="Show Columns tab" id="button_COLUMNSTAB" style="color: #ADADAD;">Columns</label></li> <li class="options_tab" id="li_RECORDINGTAB"><label class="options_outer pointer_noul" onclick="show_options_tab( 'RECORDINGTAB', [ 'SEARCHTAB', 'DISPLAYTAB', 'COLUMNSTAB', 'RECORDINGTAB', 'STREAMINGTAB' ] );" title="Show Recording tab" id="button_RECORDINGTAB" style="color: #ADADAD;">Recording</label></li> <li class="options_tab" id="li_STREAMINGTAB"><label class="options_outer pointer_noul" onclick="show_options_tab( 'STREAMINGTAB', [ 'SEARCHTAB', 'DISPLAYTAB', 'COLUMNSTAB', 'RECORDINGTAB', 'STREAMINGTAB' ] );" title="Show Streaming tab" id="button_STREAMINGTAB" style="color: #ADADAD;">Streaming</label></li></ul></td></tr><tr class="options_outer"><td class="options_outer" id="tab_SEARCHTAB" style="display: table-cell; visibility: visible;"><table class="options"><tr class="options"><th class="options" title="Comma separated list of versions to try to record in order (e.g. default,signed,audiodescribed)">Programme Version</th><td class="options" title="Comma separated list of versions to try to record in order (e.g. default,signed,audiodescribed)"><input type="text" name="VERSIONLIST" value="default" size="30" onkeydown="return submitonEnter(event);" class="options" /></td></tr> <tr class="options"><th class="options" title="Comma separated list of programmes to exclude. Partial word matches are supported">Exclude Programmes</th><td class="options" title="Comma separated list of programmes to exclude. Partial word matches are supported"><input type="text" name="EXCLUDE"  size="30" onkeydown="return submitonEnter(event);" class="options" /></td></tr> <tr class="options"><th class="options" title="Comma separated list of categories to match. Partial word matches are supported">Categories Containing</th><td class="options" title="Comma separated list of categories to match. Partial word matches are supported"><input type="text" name="CATEGORY"  size="30" onkeydown="return submitonEnter(event);" class="options" /></td></tr> <tr class="options"><th class="options" title="Comma separated list of categories to exclude. Partial word matches are supported">Exclude Categories Containing</th><td class="options" title="Comma separated list of categories to exclude. Partial word matches are supported"><input type="text" name="EXCLUDECATEGORY"  size="30" onkeydown="return submitonEnter(event);" class="options" /></td></tr> <tr class="options"><th class="options" title="Comma separated list of channels to match. Partial word matches are supported">Channels Containing</th><td class="options" title="Comma separated list of channels to match. Partial word matches are supported"><input type="text" name="CHANNEL"  size="30" onkeydown="return submitonEnter(event);" class="options" /></td></tr> <tr class="options"><th class="options" title="Comma separated list of channels to exclude. Partial word matches are supported">Exclude Channels Containing</th><td class="options" title="Comma separated list of channels to exclude. Partial word matches are supported"><input type="text" name="EXCLUDECHANNEL"  size="30" onkeydown="return submitonEnter(event);" class="options" /></td></tr> <tr class="options"><th class="options" title="Only show programmes added to the local programmes cache in the past number of hours">Added Since (hours)</th><td class="options" title="Only show programmes added to the local programmes cache in the past number of hours"><input type="text" name="SINCE"  size="3" onkeydown="return submitonEnter(event);" class="options" /></td></tr> <tr class="options"><th class="options" title="Only show programmes added to the local programmes cache over this number of hours ago">Added Before (hours)</th><td class="options" title="Only show programmes added to the local programmes cache over this number of hours ago"><input type="text" name="BEFORE"  size="3" onkeydown="return submitonEnter(event);" class="options" /></td></tr> <tr class="options"><th class="options" title="Whether to additionally display and search programmes in the future programmes schedule (will only work if Refresh future schedule option is enable and refreshed)">Search Future Schedule</th><td class="options" title="Whether to additionally display and search programmes in the future programmes schedule (will only work if Refresh future schedule option is enable and refreshed)"><label><input type="radio" name="FUTURE" value="1" class="options" />On</label> <label><input type="radio" name="FUTURE" value="0" checked="checked" class="options" />Off</label></td></tr></table></td><td class="options_outer" id="tab_DISPLAYTAB" style="display: none; visibility: collapse;"><table class="options"><tr class="options"><th class="options" title="Sort the results in this order">Sort by</th><td class="options" title="Sort the results in this order"><select name="SORT"  onchange="BackupFormVars(form); form.NEXTPAGE.value='search_progs'; form.submit(); RestoreFormVars(form);" id="option_SORT" class="options">
<option selected="selected" value="index">Index</option>
<option value="thumbnail">Image</option>
<option value="pid">Pid</option>
<option value="available">Availability</option>
<option value="type">Type</option>
<option value="name">Name</option>
<option value="episode">Episode</option>
<option value="versions">Versions</option>
<option value="duration">Duration</option>
<option value="desc">Description</option>
<option value="channel">Channel</option>
<option value="categories">Categories</option>
<option value="timeadded">Time Added</option>
<option value="guidance">Guidance</option>
<option value="web">Web Page</option>
<option value="seriesnum">Series Number</option>
<option value="episodenum">Episode Number</option>
<option value="filename">Filename</option>
<option value="mode">Mode</option>
</select></td></tr> <tr class="options"><th class="options" title="Reverse the sort order">Reverse sort</th><td class="options" title="Reverse the sort order"><label><input type="radio" name="REVERSE" value="1" class="options" />On</label> <label><input type="radio" name="REVERSE" value="0" checked="checked" class="options" />Off</label></td></tr> <tr class="options"><th class="options" title="Select the number of search results displayed on each page">Programmes per Page</th><td class="options" title="Select the number of search results displayed on each page"><select name="PAGESIZE"  onchange="BackupFormVars(form); form.NEXTPAGE.value='search_progs'; form.PAGENO.value=1; form.submit(); RestoreFormVars(form);" id="option_PAGESIZE" class="options">
<option value="10">10</option>
<option value="25">25</option>
<option value="50">50</option>
<option value="100">100</option>
<option value="200">200</option>
<option value="400">400</option>
</select></td></tr> <tr class="options"><th class="options" title="Whether to hide programmes that have already been successfully recorded">Hide Recorded</th><td class="options" title="Whether to hide programmes that have already been successfully recorded"><label><input type="radio" name="HIDE" value="1" class="options" />On</label> <label><input type="radio" name="HIDE" value="0" checked="checked" class="options" />Off</label></td></tr> <tr class="options"><th class="options" title="Whether to hide deleted programmes from the recordings history list">Hide Deleted Recordings</th><td class="options" title="Whether to hide deleted programmes from the recordings history list"><label><input type="radio" name="HIDEDELETED" value="1" class="options" />On</label> <label><input type="radio" name="HIDEDELETED" value="0" checked="checked" class="options" />Off</label></td></tr></table></td><td class="options_outer" id="tab_COLUMNSTAB" style="display: none; visibility: collapse;"><table class="options"><tr class="options"><th class="options" title="Select the columns you wish to display">Enable Columns</th><td class="options"><table class="options_embedded"><tr class="options_embedded"><td class="options"><table class="options_embedded" title="Select the columns you wish to display"><tr class="options_embedded"><td class="options_embedded"><label><input type="checkbox" name="COLS" value="index" id="option_COLS_index" class="options" /></label></td> <td class="options_embedded">Index</td></tr></table></td><td class="options"><table class="options_embedded" title="Select the columns you wish to display"><tr class="options_embedded"><td class="options_embedded"><label><input type="checkbox" name="COLS" value="thumbnail" checked="checked" id="option_COLS_thumbnail" class="options" /></label></td> <td class="options_embedded">Image</td></tr></table></td><td class="options"><table class="options_embedded" title="Select the columns you wish to display"><tr class="options_embedded"><td class="options_embedded"><label><input type="checkbox" name="COLS" value="pid" id="option_COLS_pid" class="options" /></label></td> <td class="options_embedded">Pid</td></tr></table></td><tr class="options_embedded"><td class="options"><table class="options_embedded" title="Select the columns you wish to display"><tr class="options_embedded"><td class="options_embedded"><label><input type="checkbox" name="COLS" value="available" id="option_COLS_available" class="options" /></label></td> <td class="options_embedded">Availability</td></tr></table></td><td class="options"><table class="options_embedded" title="Select the columns you wish to display"><tr class="options_embedded"><td class="options_embedded"><label><input type="checkbox" name="COLS" value="type" checked="checked" id="option_COLS_type" class="options" /></label></td> <td class="options_embedded">Type</td></tr></table></td><td class="options"><table class="options_embedded" title="Select the columns you wish to display"><tr class="options_embedded"><td class="options_embedded"><label><input type="checkbox" name="COLS" value="name" checked="checked" id="option_COLS_name" class="options" /></label></td> <td class="options_embedded">Name</td></tr></table></td><tr class="options_embedded"><td class="options"><table class="options_embedded" title="Select the columns you wish to display"><tr class="options_embedded"><td class="options_embedded"><label><input type="checkbox" name="COLS" value="episode" checked="checked" id="option_COLS_episode" class="options" /></label></td> <td class="options_embedded">Episode</td></tr></table></td><td class="options"><table class="options_embedded" title="Select the columns you wish to display"><tr class="options_embedded"><td class="options_embedded"><label><input type="checkbox" name="COLS" value="versions" id="option_COLS_versions" class="options" /></label></td> <td class="options_embedded">Versions</td></tr></table></td><td class="options"><table class="options_embedded" title="Select the columns you wish to display"><tr class="options_embedded"><td class="options_embedded"><label><input type="checkbox" name="COLS" value="duration" id="option_COLS_duration" class="options" /></label></td> <td class="options_embedded">Duration</td></tr></table></td><tr class="options_embedded"><td class="options"><table class="options_embedded" title="Select the columns you wish to display"><tr class="options_embedded"><td class="options_embedded"><label><input type="checkbox" name="COLS" value="desc" checked="checked" id="option_COLS_desc" class="options" /></label></td> <td class="options_embedded">Description</td></tr></table></td><td class="options"><table class="options_embedded" title="Select the columns you wish to display"><tr class="options_embedded"><td class="options_embedded"><label><input type="checkbox" name="COLS" value="channel" checked="checked" id="option_COLS_channel" class="options" /></label></td> <td class="options_embedded">Channel</td></tr></table></td><td class="options"><table class="options_embedded" title="Select the columns you wish to display"><tr class="options_embedded"><td class="options_embedded"><label><input type="checkbox" name="COLS" value="categories" checked="checked" id="option_COLS_categories" class="options" /></label></td> <td class="options_embedded">Categories</td></tr></table></td><tr class="options_embedded"><td class="options"><table class="options_embedded" title="Select the columns you wish to display"><tr class="options_embedded"><td class="options_embedded"><label><input type="checkbox" name="COLS" value="timeadded" checked="checked" id="option_COLS_timeadded" class="options" /></label></td> <td class="options_embedded">Time Added</td></tr></table></td><td class="options"><table class="options_embedded" title="Select the columns you wish to display"><tr class="options_embedded"><td class="options_embedded"><label><input type="checkbox" name="COLS" value="guidance" id="option_COLS_guidance" class="options" /></label></td> <td class="options_embedded">Guidance</td></tr></table></td><td class="options"><table class="options_embedded" title="Select the columns you wish to display"><tr class="options_embedded"><td class="options_embedded"><label><input type="checkbox" name="COLS" value="web" id="option_COLS_web" class="options" /></label></td> <td class="options_embedded">Web Page</td></tr></table></td><tr class="options_embedded"><td class="options"><table class="options_embedded" title="Select the columns you wish to display"><tr class="options_embedded"><td class="options_embedded"><label><input type="checkbox" name="COLS" value="seriesnum" id="option_COLS_seriesnum" class="options" /></label></td> <td class="options_embedded">Series Number</td></tr></table></td><td class="options"><table class="options_embedded" title="Select the columns you wish to display"><tr class="options_embedded"><td class="options_embedded"><label><input type="checkbox" name="COLS" value="episodenum" id="option_COLS_episodenum" class="options" /></label></td> <td class="options_embedded">Episode Number</td></tr></table></td><td class="options"><table class="options_embedded" title="Select the columns you wish to display"><tr class="options_embedded"><td class="options_embedded"><label><input type="checkbox" name="COLS" value="filename" id="option_COLS_filename" class="options" /></label></td> <td class="options_embedded">Filename</td></tr></table></td><tr class="options_embedded"><td class="options"><table class="options_embedded" title="Select the columns you wish to display"><tr class="options_embedded"><td class="options_embedded"><label><input type="checkbox" name="COLS" value="mode" id="option_COLS_mode" class="options" /></label></td> <td class="options_embedded">Mode</td></tr></table></td></tr></table></td></tr></table></td><td class="options_outer" id="tab_RECORDINGTAB" style="display: none; visibility: collapse;"><table class="options"><tr class="options"><th class="options" title="Folder on the server where recordings should be saved">Override Recordings Folder</th><td class="options" title="Folder on the server where recordings should be saved"><input type="text" name="OUTPUT"  size="30" onkeydown="return submitonEnter(event);" class="options" /></td></tr> <tr class="options"><th class="options" title="Comma separated list of recording modes which should be tried in order">Recording Modes</th><td class="options" title="Comma separated list of recording modes which should be tried in order"><input type="text" name="MODES" value="flashaachigh,flashaacstd,flashaudio,flashhigh,flashstd,flashnormal,realaudio,flashaaclow" size="30" onkeydown="return submitonEnter(event);" class="options" /></td></tr> <tr class="options"><th class="options" title="e.g. http://192.168.1.2:8080">Web Proxy URL</th><td class="options" title="e.g. http://192.168.1.2:8080"><input type="text" name="PROXY"  size="30" onkeydown="return submitonEnter(event);" class="options" /></td></tr> <tr class="options"><th class="options" title="Whether to download the subtitles when recording">Download Subtitles</th><td class="options" title="Whether to download the subtitles when recording"><label><input type="radio" name="SUBTITLES" value="1" class="options" />On</label> <label><input type="radio" name="SUBTITLES" value="0" checked="checked" class="options" />Off</label></td></tr> <tr class="options"><th class="options" title="Format of metadata file to create when recording">Download Meta-data</th><td class="options" title="Format of metadata file to create when recording"><select name="METADATA"  onchange id="option_METADATA" class="options">
<option selected="selected" value="">Off</option>
<option value="xbmc">XBMC Episode nfo format</option>
<option value="xbmc_movie">XBMC Movie nfo format</option>
<option value="generic">Generic XML</option>
<option value="freevo">Freevo FXD</option>
</select></td></tr> <tr class="options"><th class="options" title="Whether to download the thumbnail when recording">Download Thumbnail</th><td class="options" title="Whether to download the thumbnail when recording"><label><input type="radio" name="THUMB" value="1" class="options" />On</label> <label><input type="radio" name="THUMB" value="0" checked="checked" class="options" />Off</label></td></tr> <tr class="options"><th class="options" title="Wait this number of hours before allowing the PVR to record a programme. This sometimes helps when the flashhd version is delayed in being made available.">PVR Hold off period (hours)</th><td class="options" title="Wait this number of hours before allowing the PVR to record a programme. This sometimes helps when the flashhd version is delayed in being made available."><input type="text" name="PVRHOLDOFF"  size="3" onkeydown="return submitonEnter(event);" class="options" /></td></tr> <tr class="options"><th class="options" title="Ignore the history and re-record a programme (Please delete the existing recording first). Doesn't apply to PVR Searches or 'Add Series'">Force Recording</th><td class="options" title="Ignore the history and re-record a programme (Please delete the existing recording first). Doesn't apply to PVR Searches or 'Add Series'"><label><input type="radio" name="FORCE" value="1" class="options" />On</label> <label><input type="radio" name="FORCE" value="0" checked="checked" class="options" />Off</label></td></tr> <tr class="options"><th class="options" title="Automatically refresh the default caches in another browser tab (hours)">Auto-Refresh Cache Interval</th><td class="options" title="Automatically refresh the default caches in another browser tab (hours)"><input type="text" name="AUTOWEBREFRESH" value="1" size="3" onkeydown="return submitonEnter(event);" class="options" /></td></tr> <tr class="options"><th class="options" title="Automatically run the PVR in another browser tab (hours)">Auto-Run PVR Interval</th><td class="options" title="Automatically run the PVR in another browser tab (hours)"><input type="text" name="AUTOPVRRUN" value="4" size="3" onkeydown="return submitonEnter(event);" class="options" /></td></tr> <tr class="options"><th class="options" title="When Refresh is clicked also get the future programme schedule. This will take a longer time to index.">Refresh Future Schedule</th><td class="options" title="When Refresh is clicked also get the future programme schedule. This will take a longer time to index."><label><input type="radio" name="REFRESHFUTURE" value="1" class="options" />On</label> <label><input type="radio" name="REFRESHFUTURE" value="0" checked="checked" class="options" />Off</label></td></tr></table></td><td class="options_outer" id="tab_STREAMINGTAB" style="display: none; visibility: collapse;"><table class="options"><tr class="options"><th class="options" title="Remote Audio Bitrate (in kbps) to transcode remotely played files - leave blank for native bitrate">Remote Audio Bitrate</th><td class="options" title="Remote Audio Bitrate (in kbps) to transcode remotely played files - leave blank for native bitrate"><input type="text" name="BITRATE"  size="3" onkeydown="return submitonEnter(event);" class="options" /></td></tr> <tr class="options"><th class="options" title="Video size '&lt;width&gt;x&lt;height&gt;' to transcode remotely played files - leave blank for native size">Remote Streaming Video Size</th><td class="options" title="Video size '&lt;width&gt;x&lt;height&gt;' to transcode remotely played files - leave blank for native size"><select name="VSIZE"  onchange id="option_VSIZE" class="options">
<option selected="selected" value="">Native</option>
<option value="176x96">176x96</option>
<option value="320x176">320x176</option>
<option value="480x272">480x272</option>
<option value="512x288">512x288</option>
<option value="640x360">640x360</option>
<option value="832x468">832x468</option>
<option value="1280x720">1280x720</option>
</select></td></tr> <tr class="options"><th class="options" title="Remote Video Frame Rate (in frames per second) to transcode remotely played files - leave blank for native framerate">Remote Video Frame Rate</th><td class="options" title="Remote Video Frame Rate (in frames per second) to transcode remotely played files - leave blank for native framerate"><input type="text" name="VFR"  size="2" onkeydown="return submitonEnter(event);" class="options" /></td></tr> <tr class="options"><th class="options" title="Force the output to be this type when using 'Play Remote' for 'PlayDirect' streaming(e.g. flv, mov). Specify 'none' to disable transcoding/remuxing.  Leave blank for auto-detection">Remote Streaming type</th><td class="options" title="Force the output to be this type when using 'Play Remote' for 'PlayDirect' streaming(e.g. flv, mov). Specify 'none' to disable transcoding/remuxing.  Leave blank for auto-detection"><select name="STREAMTYPE"  onchange="form.submit();" id="option_STREAMTYPE" class="options">
<option selected="selected" value="">Auto</option>
<option value="none">Disable Transcoding</option>
<option value="flv">Flash Video (flv)</option>
<option value="mov">Quicktime (mov)</option>
<option value="asf">Advanced Streaming Format (asf)</option>
<option value="avi">AVI</option>
<option value="mp3">MP3 (Audio Only)</option>
<option value="aac">AAC (Audio Only)</option>
<option value="wav">WAV (Audio Only)</option>
<option value="flac">FLAC (Audio Only)</option>
</select></td></tr></table></td>
<td class="options_outer" id="tab_HIDDENTAB" style="display: none; visibility: collapse;">

<table class="options"><tr class="options">
<input type="hidden" name="SAVE" value="0" id="option_SAVE" /></tr>
 <tr class="options"><input type="hidden" name="SEARCHTAB" value="yes" id="option_SEARCHTAB" /></tr> <tr class="options"><input type="hidden" name="COLUMNSTAB" value="no" id="option_COLUMNSTAB" /></tr> <tr class="options"><input type="hidden" name="DISPLAYTAB" value="no" id="option_DISPLAYTAB" /></tr> <tr class="options"><input type="hidden" name="RECORDINGTAB" value="no" id="option_RECORDINGTAB" /></tr> <tr class="options"><input type="hidden" name="STREAMINGTAB" value="no" id="option_STREAMINGTAB" /></tr> <tr class="options"><input type="hidden" name="PAGENO" value="1" id="option_PAGENO" /></tr>
 <tr class="options"><input type="hidden" name="INFO" value="0" id="option_INFO" /></tr> <tr class="options"><input type="hidden" name="NEXTPAGE" value="search_progs" id="option_NEXTPAGE" /></tr> <tr class="options"><input type="hidden" name="ACTION" value="" id="option_ACTION" /></tr></table></td></tr><tr class="options_outer"><td class="options_outer"><ul class="options_tab"><li class="options_button"><label class="options_outer pointer_noul" onclick="BackupFormVars(form); form.NEXTPAGE.value='search_progs'; form.PAGENO.value=1; form.submit(); RestoreFormVars(form);" title="Apply Current Options">Apply Settings</label></li> <li class="options_button"><label class="options_outer pointer_noul" onclick="BackupFormVars(form); form.SAVE.value=1; form.submit(); RestoreFormVars(form);" title="Remember Current Options as Default">Save As Default</label></li></ul></td></tr></table><div class="action"><ul class="action"><li class="action"><a class="action" onclick="BackupFormVars(form); form.NEXTPAGE.value='search_progs'; form.PAGENO.value=1; form.submit(); RestoreFormVars(form);" title="Perform search based on search options">Search</a></li> <li class="action">
<a class="action" onclick="if(! ( check_if_selected(document.form, 'PROGSELECT') ||  form.URL.value ) ) { alert('No Quick URL or programmes were selected'); return false; } BackupFormVars(form); form.SEARCH.value=''; form.NEXTPAGE.value='record_now'; var random=Math.floor(Math.random()*99999); form.target='_newtab_'+random; form.submit(); RestoreFormVars(form); form.target=''; form.URL.value=''; disable_selected_checkboxes(document.form, 'PROGSELECT');" title="Immediately Record selected programmes (or Quick URL) in a new tab">Record</a></li> <li class="action"><a class="action" onclick="if(! ( check_if_selected(document.form, 'PROGSELECT') ||  form.URL.value ) ) { alert('No Quick URL or programmes were selected'); return false; } BackupFormVars(form); form.SEARCH.value=''; form.ACTION.value='genplaylist'; form.submit(); form.ACTION.value=''; RestoreFormVars(form); form.URL.value='';" title="Get a Playlist based on selected programmes (or Quick URL) to stream in your media player">Play</a></li> <li class="action"><a class="action" onclick="if(! ( check_if_selected(document.form, 'PROGSELECT') ||  form.URL.value ) ) { alert('No Quick URL or programmes were selected'); return false; } BackupFormVars(form); form.SEARCH.value=''; form.NEXTPAGE.value='pvr_queue'; form.submit(); RestoreFormVars(form); form.URL.value=''; disable_selected_checkboxes(document.form, 'PROGSELECT');" title="Queue selected programmes (or Quick URL) for one-off recording">Queue</a></li> <li class="action"><a class="action" onclick="if(! check_if_selected(document.form, 'PROGSELECT')) { alert('No programmes were selected'); return false; } BackupFormVars(form); form.SEARCH.value=''; form.ACTION.value='genplaylistdirect'; form.submit(); RestoreFormVars(form);" title="Get a Playlist based on selected programmes for remote file streaming in your media player">Play Remote</a></li> <li class="action"><a class="action darker" onclick="var version = 'default'; if ('.*' == '.*' &amp;&amp; 0 == 0 &amp;&amp; version.toLowerCase().indexOf('default') != -1) { alert('Search = .* will download all available programmes.  Please enter a more specific search term or additional advanced search criteria (excluding Search Future Schedule).'); return false; } if ('.*' == '' ) { alert('Please enter a search term. Use Search = .* to record all programmes matching advanced search criteria.'); return false; } if ( 1060 &gt; 30 ) { alert('Please limit your search to result in no more than 30 current programmes'); return false; }  BackupFormVars(form); form.NEXTPAGE.value='pvr_add'; form.submit(); RestoreFormVars(form);" title="Create a persistent PVR search using the current search terms (i.e. all below programmes)">Add Search to PVR</a></li> <li class="action"><a class="action" onclick="BackupFormVars(form); form.target='_newtab_refresh'; form.NEXTPAGE.value='refresh'; form.submit(); RestoreFormVars(form); form.target=''; form.NEXTPAGE.value=''; " title="Refresh the list of programmes - can take a while">Refresh Cache</a></li></ul></div>
<table class="pagetrail" id="centered"><tr class="pagetrail">
<td class="pagetrail"><label class="pagetrail-current" title="Current Page">1</label></td> <td class="pagetrail pointer"><label class="pagetrail pointer" onclick="BackupFormVars(form); form.NEXTPAGE.value='search_progs'; form.PAGENO.value='2'; form.submit(); RestoreFormVars(form);" title="Page 2">2</label></td> <td class="pagetrail pointer"><label class="pagetrail pointer" onclick="BackupFormVars(form); form.NEXTPAGE.value='search_progs'; form.PAGENO.value='3'; form.submit(); RestoreFormVars(form);" title="Page 3">3</label></td> <td class="pagetrail pointer"><label class="pagetrail pointer" onclick="BackupFormVars(form); form.NEXTPAGE.value='search_progs'; form.PAGENO.value='4'; form.submit(); RestoreFormVars(form);" title="Page 4">4</label></td> <td class="pagetrail pointer"><label class="pagetrail pointer" onclick="BackupFormVars(form); form.NEXTPAGE.value='search_progs'; form.PAGENO.value='5'; form.submit(); RestoreFormVars(form);" title="Page 5">5</label></td> <td class="pagetrail pointer"><label class="pagetrail pointer" onclick="BackupFormVars(form); form.NEXTPAGE.value='search_progs'; form.PAGENO.value='6'; form.submit(); RestoreFormVars(form);" title="Page 6">6</label></td> <td class="pagetrail pointer"><label class="pagetrail pointer" onclick="BackupFormVars(form); form.NEXTPAGE.value='search_progs'; form.PAGENO.value='7'; form.submit(); RestoreFormVars(form);" title="Page 7">7</label></td> <td class="pagetrail pointer"><label class="pagetrail pointer" onclick="BackupFormVars(form); form.NEXTPAGE.value='search_progs'; form.PAGENO.value='8'; form.submit(); RestoreFormVars(form);" title="Page 8">8</label></td> <td class="pagetrail">...</td> <td class="pagetrail pointer"><label class="pagetrail pointer" onclick="BackupFormVars(form); form.NEXTPAGE.value='search_progs'; form.PAGENO.value=54; form.submit(); RestoreFormVars(form);" title="Page 54">54</label></td> <td class="pagetrail pointer"><label class="pagetrail pointer" onclick="BackupFormVars(form); form.NEXTPAGE.value='search_progs'; form.PAGENO.value=1+1; form.submit(); RestoreFormVars(form);" title="Next Page">>></label></td> 
<td class="pagetrail"><label class="pagetrail" title="Matches" id="prog_count"></label></td>
</tr></table>
<div id="programcontent">
<table class="search">

<tr> <th class="search">
<label><input type="checkbox" name="SELECTOR" value="1" onclick="check_toggle(document.form, 'PROGSELECT')" title="Select/Unselect All Programmes" class="search" /></label></th>
<th class="search">Actions</th> <th class="search"><table class="searchhead">
<tr class="search"><th class="search"><label class="unsorted pointer" onclick="" title="Sort by thumbnail">Image</label></th></tr></table></th> 
<th class="search"><table class="searchhead"><tr class="search"><th class="search"><label class="unsorted pointer" onclick="" title="Sort by type">Type</label></th></tr></table></th>
<th class="search"><table class="searchhead"><tr class="search"><th class="search"><label class="unsorted pointer" onclick="" title="Sort by name">Name</label></th></tr></table></th>
<th class="search"><table class="searchhead"><tr class="search"><th class="search"><label class="unsorted pointer" onclick="" title="Sort by episode">Season</label></th></tr></table></th>
<th class="search"><table class="searchhead"><tr class="search"><th class="search"><label class="unsorted pointer" onclick="" title="Sort by episode">Episode</label></th></tr></table></th>
<th class="search"><table class="searchhead"><tr class="search"><th class="search"><label class="unsorted pointer" onclick="form.NEXTPAGE.value='search_progs'; form.SORT.value='desc'; form.REVERSE[1].checked=true; form.submit();" title="Sort by desc">Description</label></th></tr></table></th> <th class="search"><table class="searchhead"><tr class="search"><th class="search"><label class="unsorted pointer" onclick="form.NEXTPAGE.value='search_progs'; form.SORT.value='channel'; form.REVERSE[1].checked=true; form.submit();" title="Sort by channel">Channel</label></th></tr></table></th> <th class="search"><table class="searchhead"><tr class="search"><th class="search"><label class="unsorted pointer" onclick="form.NEXTPAGE.value='search_progs'; form.SORT.value='categories'; form.REVERSE[1].checked=true; form.submit();" title="Sort by categories">Categories</label></th></tr></table></th> <th class="search"><table class="searchhead"><tr class="search"><th class="search"><label class="unsorted pointer" onclick="form.NEXTPAGE.value='search_progs'; form.SORT.value='timeadded'; form.REVERSE[1].checked=true; form.submit();" title="Sort by timeadded">Time Added</label></th></tr></table></th> </tr>

<?
$dbQuery = "SELECT * FROM ProgramData WHERE expires_at >'".date(c)."' OR expires_at='0000-00-00' ORDER BY DateAdded DESC";
$result = mysql_query($dbQuery) or die (mysql_error());
while ($row = mysql_fetch_array($result)){
print ' <tr class="search"><td class="search">';
print '<label><input type="checkbox" name="PROGSELECT" value="'.$row['id'].'" class="search" /></label></td>';
print '<td class="search"><a class="search" title="Play from Internet" href="http://www.hulu.com/watch/'.$row['HuluID'].'" target="new">Play</a>';
print '<br /><label class="search" title="Record \''.$row['title'].'\' Now" id="nowrap">Record</label><br />';
print '<label class="search" title="Queue \''.$row['title'].'\' Now" for PVR Recording" id="nowrap">Queue</label><br />';
print '<label class="search pointer_noul" title="Add Series \''.$row['title'].'\' Now" to PVR" id="nowrap">Add Series</label></td> ';
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
</div>
<table class="pagetrail" id="centered"><tr class="pagetrail"><td class="pagetrail"><label class="pagetrail-current" title="Current Page">1</label></td> <td class="pagetrail pointer"><label class="pagetrail pointer" onclick="BackupFormVars(form); form.NEXTPAGE.value='search_progs'; form.PAGENO.value='2'; form.submit(); RestoreFormVars(form);" title="Page 2">2</label></td> <td class="pagetrail pointer"><label class="pagetrail pointer" onclick="BackupFormVars(form); form.NEXTPAGE.value='search_progs'; form.PAGENO.value='3'; form.submit(); RestoreFormVars(form);" title="Page 3">3</label></td> <td class="pagetrail pointer"><label class="pagetrail pointer" onclick="BackupFormVars(form); form.NEXTPAGE.value='search_progs'; form.PAGENO.value='4'; form.submit(); RestoreFormVars(form);" title="Page 4">4</label></td> <td class="pagetrail pointer"><label class="pagetrail pointer" onclick="BackupFormVars(form); form.NEXTPAGE.value='search_progs'; form.PAGENO.value='5'; form.submit(); RestoreFormVars(form);" title="Page 5">5</label></td> <td class="pagetrail pointer"><label class="pagetrail pointer" onclick="BackupFormVars(form); form.NEXTPAGE.value='search_progs'; form.PAGENO.value='6'; form.submit(); RestoreFormVars(form);" title="Page 6">6</label></td> <td class="pagetrail pointer"><label class="pagetrail pointer" onclick="BackupFormVars(form); form.NEXTPAGE.value='search_progs'; form.PAGENO.value='7'; form.submit(); RestoreFormVars(form);" title="Page 7">7</label></td> <td class="pagetrail pointer"><label class="pagetrail pointer" onclick="BackupFormVars(form); form.NEXTPAGE.value='search_progs'; form.PAGENO.value='8'; form.submit(); RestoreFormVars(form);" title="Page 8">8</label></td> <td class="pagetrail">...</td> <td class="pagetrail pointer"><label class="pagetrail pointer" onclick="BackupFormVars(form); form.NEXTPAGE.value='search_progs'; form.PAGENO.value=54; form.submit(); RestoreFormVars(form);" title="Page 54">54</label></td> <td class="pagetrail pointer"><label class="pagetrail pointer" onclick="BackupFormVars(form); form.NEXTPAGE.value='search_progs'; form.PAGENO.value=1+1; form.submit(); RestoreFormVars(form);" title="Next Page">>></label></td> <td class="pagetrail"><label class="pagetrail" title="Matches">(1060 programmes)</label></td></tr></table><div class="action"><ul class="action"><li class="action"><a class="action" onclick="BackupFormVars(form); form.NEXTPAGE.value='search_progs'; form.PAGENO.value=1; form.submit(); RestoreFormVars(form);" title="Perform search based on search options">Search</a></li> <li class="action"><a class="action" onclick="if(! ( check_if_selected(document.form, 'PROGSELECT') ||  form.URL.value ) ) { alert('No Quick URL or programmes were selected'); return false; } BackupFormVars(form); form.SEARCH.value=''; form.NEXTPAGE.value='record_now'; var random=Math.floor(Math.random()*99999); form.target='_newtab_'+random; form.submit(); RestoreFormVars(form); form.target=''; form.URL.value=''; disable_selected_checkboxes(document.form, 'PROGSELECT');" title="Immediately Record selected programmes (or Quick URL) in a new tab">Record</a></li> <li class="action"><a class="action" onclick="if(! ( check_if_selected(document.form, 'PROGSELECT') ||  form.URL.value ) ) { alert('No Quick URL or programmes were selected'); return false; } BackupFormVars(form); form.SEARCH.value=''; form.ACTION.value='genplaylist'; form.submit(); form.ACTION.value=''; RestoreFormVars(form); form.URL.value='';" title="Get a Playlist based on selected programmes (or Quick URL) to stream in your media player">Play</a></li> <li class="action"><a class="action" onclick="if(! ( check_if_selected(document.form, 'PROGSELECT') ||  form.URL.value ) ) { alert('No Quick URL or programmes were selected'); return false; } BackupFormVars(form); form.SEARCH.value=''; form.NEXTPAGE.value='pvr_queue'; form.submit(); RestoreFormVars(form); form.URL.value=''; disable_selected_checkboxes(document.form, 'PROGSELECT');" title="Queue selected programmes (or Quick URL) for one-off recording">Queue</a></li> <li class="action"><a class="action" onclick="if(! check_if_selected(document.form, 'PROGSELECT')) { alert('No programmes were selected'); return false; } BackupFormVars(form); form.SEARCH.value=''; form.ACTION.value='genplaylistdirect'; form.submit(); RestoreFormVars(form);" title="Get a Playlist based on selected programmes for remote file streaming in your media player">Play Remote</a></li> <li class="action"><a class="action darker" onclick="var version = 'default'; if ('.*' == '.*' &amp;&amp; 0 == 0 &amp;&amp; version.toLowerCase().indexOf('default') != -1) { alert('Search = .* will download all available programmes.  Please enter a more specific search term or additional advanced search criteria (excluding Search Future Schedule).'); return false; } if ('.*' == '' ) { alert('Please enter a search term. Use Search = .* to record all programmes matching advanced search criteria.'); return false; } if ( 1060 &gt; 30 ) { alert('Please limit your search to result in no more than 30 current programmes'); return false; }  BackupFormVars(form); form.NEXTPAGE.value='pvr_add'; form.submit(); RestoreFormVars(form);" title="Create a persistent PVR search using the current search terms (i.e. all below programmes)">Add Search to PVR</a></li> <li class="action"><a class="action" onclick="BackupFormVars(form); form.target='_newtab_refresh'; form.NEXTPAGE.value='refresh'; form.submit(); RestoreFormVars(form); form.target=''; form.NEXTPAGE.value=''; " title="Refresh the list of programmes - can take a while">Refresh Cache</a></li></ul></div><div id="status" /><div><input type="hidden" name=".cgifields" value="REFRESHFUTURE"  /><input type="hidden" name=".cgifields" value="FUTURE"  /><input type="hidden" name=".cgifields" value="SUBTITLES"  /><input type="hidden" name=".cgifields" value="HISTORY"  /><input type="hidden" name=".cgifields" value="REVERSE"  /><input type="hidden" name=".cgifields" value="PROGTYPES"  /><input type="hidden" name=".cgifields" value="THUMB"  /><input type="hidden" name=".cgifields" value="HIDE"  /><input type="hidden" name=".cgifields" value="HIDEDELETED"  /><input type="hidden" name=".cgifields" value="FORCE"  /><input type="hidden" name=".cgifields" value="COLS"  /></div></form><p><b class="footer">get_iplayer Web PVR Manager v2.82, &copy;2009-2010 Phil Lewis - Licensed under GPLv3</b></p>
</body>
</html>
<script language="javascript">
var LevelName = document.getElementById('LevelName').value;
var SecurityLevelID = document.getElementById('SecurityLevelID').value;
var poststr2="LevelName="+LevelName+"&id="+SecurityLevelID+"&savelayout=true&section=Destination&values="+arSelected5;
$.ajax({type: "POST",async:true,url: "managesecurity.php",data: poststr2,success: function(html){$("#levelarea").empty();$("#levelarea").append(html);}});
}
</script>
