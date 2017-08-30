<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		html_header.php
//	Desc:		write out header html
//  Client:		LAD
//	Author:		Rob Curle
//	Date:		13 June 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Building Centre Diary</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="description" content="" />
<meta name="keywords" content="" />
<?php
	if (!empty($metatag)) {
		echo $metatag;
	}
?>
<link rel=stylesheet type='text/css' href='css/main.css' />

<script language="javascript" type="text/javascript" src="js/calendar.js"></script>
<script language="JavaScript" type="text/javascript">
<!--

function redirect(url) {
  location.href = url;
}

function submitform() {
  document.userdata.submit();
}

function change_action(todo) {
	document.userdata.a.value = todo;
	document.userdata.submit();
}

function change_combo(object) {
	if (object.options[object.selectedIndex].value > 0) {
		document.userdata.submit();
	}
	else if (object.options[object.selectedIndex].value == '') {
		document.userdata.submit();
	}
}

function disable_other (obj, index) {
	if (obj.selectedIndex == index) {
		document.userdata.hosted_other.disabled = false;
	}
	else {
		document.userdata.hosted_other.disabled = true;
		document.userdata.hosted_other.value = '';
	}
}

function print_event() {
	window.print();
}

function time_focus(objTime) {
	if (objTime.value == 'hh:mm') {
		objTime.value = '';
	}
}

function numerics (e) {
	var key;
	var keychar;

	if (window.event) {
		key = window.event.keyCode;
	}
	else if (e) {
		key = e.which;
	}
	else {
		return true;
	}

	keychar = String.fromCharCode(key);
	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27)) {
		return true;
	}
	else if (((".0123456789").indexOf(keychar) > -1)) {
		return true;
	}
	else {
		return false;
	}
}

function open_calendar(txtField, anchorName) {
	var cal = new CalendarPopup("cal");
	cal.setWeekStartDay(1);
	cal.setCssPrefix("cal_");

	cal.select(eval('document.forms[0].'+txtField), anchorName, 'yyyy-MM-dd');
	return false;
}

function newImage(arg) {
  if (document.images) {
    rslt = new Image();
    rslt.src = arg;
    return rslt;
  }
}

function changeImages() {
  if (document.images && (preloadFlag == true)) {
    for (var i=0; i<changeImages.arguments.length; i+=2) {
      document[changeImages.arguments[i]].src = changeImages.arguments[i+1];
    }
  }
}

var preloadFlag = false;
function preloadImages() {
  if (document.images) {
    // nav //
    planner_over = newImage("media/btns_menu/planner_on.gif");
    events_over = newImage("media/btns_menu/events_on.gif");
    exhibs_over = newImage("media/btns_menu/exhibitions_on.gif");
    msgs_over = newImage("media/btns_menu/messages_on.gif");
	users_over = newImage("media/btns_menu/users_on.gif");
    logout_over = newImage("media/btns_menu/logout_on.gif");
    preloadFlag = true;

<?php
	echo $onload;
?>
  }
}

<?php echo $js;?>

// -->
</script>
</head>
<body onload="preloadImages()" background="media/bg.gif">

<table width="750" border="0" cellspacing="0" cellpadding="0" id="hdr">
<tr><td valign="top" width="750" bgcolor="#FFFFFF"><?php include ("includes/header_banner.php");?></td></tr>
<tr><td valign="top" colspan="2"><img src="media/misc/trans.gif" alt="" width="1" height="20" border="0" /></td></tr>
<tr><td colspan="2" valign="baseline"><?php include ("includes/nav.php");?></td></tr>
</table>