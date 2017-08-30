<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		admin_users.php
//	Desc:		list of system users, with delete function
//  Client:		LAD
//	Author:		Rob Curle
//	Date:		13 June 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////

	include("includes/startup.php");
	$db = new db_mysql;
		
	if (!$_SESSION['admin_rights']) {
		msg("Access Denied", "Sorry you do not have access to this page.", HOME);
	}
	
	switch ($_GET['a']) {
		case "dc": question_msg ("Delete User", "Are you sure you want to delete this User?", $_SERVER['PHP_SELF']."?id=".$_GET['id']."&a=di", $_SERVER['PHP_SELF']); break;
		case "di": delete_user($_GET['id']); break;
	}
	
	$section = 5;
	include("includes/html_header.php");
?>
	<table width="750" border="0" cellpadding="7" cellspacing="0">
	<tr><td colspan="2" valign="middle" class="title_bar">Users</td><tr>
	<tr><td width="750" valign="top" class="content">
	
	<table border="0" cellpadding="0" cellspacing="0"><tr><td>
	<table border="0" cellpadding="4" cellspacing="1" class="frame">
	<tr><td colspan="2"><strong>Users</strong></td></tr>
	<tr><td align="right" class="row_title" colspan="2"><a href="admin_user.php"><img src="media/btns/new.gif" width="35" height="18" border="0" alt="Add a new User"></a></td></tr>
	<tr class="row_title"><th align="left">Name</th><th>&nbsp;</th></tr>
<?php
	$db->query("SELECT * FROM admin_users ORDER BY name");
	while ($db->next_record()) {
		echo "<tr><td valign=\"middle\" width=\"100\" class=\"row_value\"><a href=\"admin_user.php?id=" . $db->f("id") . "\">" . $db->f("name") . "</a></td><td align=\"center\" valign=\"middle\" class=\"row_value\"><a href=\"" . $_SERVER['PHP_SELF'] . "?a=dc&id=" . $db->f("id") . "\">Delete</a></td></tr>\n";
	}
?>
	</table>
	</td><td width="30">&nbsp;</td><td valign="top">
	<h1>Utilities</h1>
	<p><a href="admin_db_backup.php">Database Backup</a></p>
	</td></tr>
	</table>
	
	</td></tr>
	</table>
	
	
	
<?php
	include("includes/html_footer.php");
	
	//////////////////////////////////////////////////////////////////////////////////////////////////	
		
	function delete_user($user_id) {
		$db = new db_mysql;
		
		if (!empty($user_id) && is_numeric($user_id)) {
			$db->query("DELETE FROM admin_users WHERE id=$user_id");
			$db->query("DELETE FROM sort_columns WHERE user_id=$user_id");
			$db->query("DELETE FROM filters WHERE user_id=$user_id");
		}	
	}
?>
