<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		sessions.php
//	Desc:		session handler
//  Client:		LAD
//	Author:		Rob Curle
//	Date:		13 June 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////

	session_start();
	
	if (!isset($_SESSION['sess_bcd'])) {
		$_SESSION['sess_bcd'] = false;
		$_SESSION['admin_id'] = 0;
		$_SESSION['admin_name'] = "";
		$_SESSION['loc_rights'] = "";
		$_SESSION['admin_rights'] = 0;
		$_SESSION['msg_rights'] = 0;
		$_SESSION['email_notify'] = 0;
		$_SESSION['offset'] = 0;
		$_SESSION['xoffset'] = 0;
		$_SESSION['moffset'] = 0;
	}	
	
	if (!strstr ($_SERVER['PHP_SELF'], "index.php") && !strstr ($_SERVER['PHP_SELF'], "password_reminder.php")) {
		if (!logged_in()) {
			redirect("index.php");
		}
	}
?>
