<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		index.php
//	Desc:		site login
//  Client:		BCD
//	Author:		Rob Curle
//	Date:		13 June 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////

	include("includes/startup.php");
	
	$page = ($_REQUEST['url'] ? $_REQUEST['url']:HOME);
	if (logged_in()) {
		redirect ($page);
	}
	
	if ($_REQUEST['a'] == "login") {
		$msg = login();
		
		if (empty ($msg)) {
			redirect ($page);
		}
	}

	$hide_nav = true;
	include("includes/html_header.php");
?>
<table width="750" border="0" cellpadding="7" cellspacing="0">
<tr><td width="350" valign="top" class="content"><p><img src="media/hdrs/hello.gif" alt="" width="46" height="16" border="0" /></p>

<?php
	if (!empty($msg)) {
		echo "<p class=\"error\">$msg</p>\n";
	}
?>
<form name="userdata" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<input type="hidden" name="a" value="login" />
<input type="hidden" name="url" value="<?php echo $_REQUEST['url'];?>" />
<table border="0" cellpadding="3" cellspacing="1" class="frame">
<tr><td class="row_title">Username</td><td class="row_value"><input type="text" name="user" size="25" value="<?php echo $_POST['user'];?>" /></td>
<tr><td class="row_title">Password</td><td class="row_value"><input type="password" name="pass" size="25" value="" /></td>
<tr><td colspan="2" class="row_title" align="right"><input type="image" src="media/btns/login.gif" name="login" width="40" height="18" border="0" /></td></tr>
</table>
</form>
<p><a href="password_reminder.php">Lost your login details?</a></p>

</td>
<td class="content_right">&nbsp;</td></tr>
</table>
<?php
	include("includes/html_footer.php");
	
///////////////////////////////////////////////////////////////////////////////////////
	
	function login() {
		$db = new db_mysql;
		$msg = "";
		
		$db->query("SELECT * FROM admin_users WHERE username=\"{$_POST['user']}\" AND password=\"{$_POST['pass']}\"");
		if ($db->num_rows() > 0) {
			$db->next_record();
			
			session_start();
			$_SESSION['sess_bcd'] = true;
			$_SESSION['admin_id'] = $db->f("id");
			$_SESSION['admin_name'] = $db->f("name");
			$_SESSION['admin_email'] = $db->f("email");
			$_SESSION['loc_rights'] = $db->f("location_rights");
			$_SESSION['msg_rights'] = $db->f("msg_rights");
			$_SESSION['admin_rights'] = $db->f("admin_rights");
			$_SESSION['email_notify'] = $db->f("email_notify");
		}
		else {
			$msg = "Sorry, login incorrect.";
		}
		
		return $msg;
	}

?>
