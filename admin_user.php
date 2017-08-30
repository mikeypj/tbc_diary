<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		admin_user.php
//	Desc:		add/edit a user
//  Client:		LAD
//	Author:		Rob Curle
//	Date:		13 June 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////

	include("includes/startup.php");
	$db = new db_mysql;
	$back_url = "admin_users.php";

	if (!$_SESSION['admin_rights']) {
		msg("Access Denied", "Sorry you do not have access to this page.", HOME);
	}

	if ($_POST['a'] == "save") {
		$msg = save_item();
		if (empty($msg)) {
			msg("User Admin", "This Admin User has now been saved.", $back_url);
		}
	}

	$page_title = "New User";
	if (!empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
		$db->query("SELECT * FROM admin_users WHERE id=" . $_REQUEST['id']);
		$page_title = "Edit User";
	}

	$locations = get_locations();

	$section = 5;
	include("includes/html_header.php");
?>
	<table width="750" border="0" cellpadding="7" cellspacing="0">
	<tr><td colspan="2" valign="middle" class="title_bar">Users</td><tr>
	<tr><td width="750" valign="top" class="content">

	<form name="userdata" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
	<input type="image" name="go" src="media/trans.gif" alt="" width="1" height="1" border="0" />
	<input type="hidden" name="a" value="save" />

<?php
	if (!empty($msg)) {
		echo "<p class=\"error\">$msg</p>\n";
	}
?>
	<table border="0" cellpadding="3" cellspacing="1" class="frame">
	<tr><td colspan="2" class="yellow" align="right"><input type="image" name="save" src="media/btns/save.gif" width="38" height="18" border="0" alt="Save Record" align="absmiddle" />&nbsp;&nbsp;<a href="<?php echo $back_url;?>"><img src="media/btns/cancel.gif" width="51" height="18" border="0" alt="Cancel Changes" align="absmiddle" /></a></td></tr>
<?php
	if ($db->num_rows() > 0) {
		$db->next_record();
?>
		<input type="hidden" name="id" value="<?php echo $_REQUEST['id'];?>" />
		<input type="hidden" name="prev_user" value="<?php echo $db->f("username");?>" />
		<tr><td class="row_title">Last Updated:</td><td width="200" class="row_title"><?php echo last_modified($db->f("modified_date"), $db->f("modified_by"));?></td></tr>
		<tr><td class="row_title">Full name:</td><td class="row_value"><input type="text" name="uname" size="35" maxlength="40" value="<?php echo (isset($_POST['uname']) ? $_POST['uname']:$db->f("name"));?>" /></td></tr>
		<tr><td class="row_title">Email address:</td><td class="row_value"><input type="text" name="email" size="35" maxlength="255" value="<?php echo (isset($_POST['email']) ? $_POST['email']:$db->f("email"));?>" /></td></tr>
		<tr><td class="row_title">Username:</td><td class="row_value"><input type="text" name="username" size="35" maxlength="20" value="<?php echo (isset($_POST['username']) ? $_POST['username']:$db->f("username"));?>" /></td></tr>
		<tr><td class="row_title">Password:</td><td class="row_value"><input type="password" name="user_pass" size="35" maxlength="20" value="<?php echo (isset($_POST['user_pass']) ? $_POST['user_pass']:$db->f("password"));?>" /></td></tr>

		<tr><td colspan="2"><strong>Add/Edit events in these locations</strong></td></tr>
	<?php
		$user_locs = array();
		$user_locs = explode("|", $db->f("location_rights"));

		foreach ($locations as $id => $txt) {
			echo "<tr><td class=\"row_title\">{$txt}</td><td class=\"row_value\"><input name=\"loc_rights[]\" type=\"checkbox\" value=\"{$id}\"";
			if (is_array($_POST['loc_rights'])) {
				if (in_array($id, $_POST['loc_rights'])) {
					echo " checked='checked'";
				}
			}
			else if (in_array($id, $user_locs)) {
				echo " checked='checked'";
			}
			echo " /></td></tr>";
		}
	?>
		<tr><td colspan="2"><strong>Other access</strong></td></tr>
		<tr><td class="row_title">Add/Edit messages:</td><td class="row_value"><input type="checkbox" name="msg_rights" value="1"<?php echo ($db->f("msg_rights") ? " checked='checked'":"");?> /></td></tr>
		<tr><td class="row_title">Administrator:</td><td class="row_value"><input type="checkbox" name="admin_rights" value="1"<?php echo ($db->f("admin_rights") ? " checked='checked'":"");?> /></td></tr>

		<tr><td colspan="2"><strong>Receive email</strong></td></tr>
		<tr><td class="row_title">When any event is added or edited:</td><td class="row_value"><input type="checkbox" name="email_notify" value="1"<?php echo ($db->f("email_notify") ? " checked='checked'":"");?> /></td></tr>
<?php
	}
	else {
?>
		<input type="hidden" name="id" value="" />
		<input type="hidden" name="prev_user" value="" />
		<tr><td class="row_title">Full name:</td><td class="row_value"><input type="text" name="uname" size="35" maxlength="40" value="<?php echo $_POST['uname'];?>" /></td></tr>
		<tr><td class="row_title">Email address:</td><td class="row_value"><input type="text" name="email" size="35" maxlength="255" value="<?php echo $_POST['email'];?>" /></td></tr>
		<tr><td class="row_title">Username:</td><td class="row_value"><input type="text" name="username" size="35" maxlength="20" value="<?php echo $_POST['username'];?>" /></td></tr>
		<tr><td class="row_title">Password:</td><td class="row_value"><input type="password" name="user_pass" size="35" maxlength="20" value="<?php echo $_POST['user_pass'];?>" /></td></tr>

		<tr><td colspan="2"><strong>Add/Edit events in these locations</strong></td></tr>
	<?php
		foreach ($locations as $id => $txt) {
			echo "<tr><td class=\"row_title\">{$txt}</td><td class=\"row_value\"><input name=\"loc_rights[]\" type=\"checkbox\" value=\"{$id}\"";
			if (is_array($_POST['loc_rights'])) {
				if (in_array($id, $_POST['loc_rights'])) {
					echo " checked='checked'";
				}
			}
			echo " /></td></tr>";
		}
	?>
		<tr><td colspan="2"><strong>Other access</strong></td></tr>
		<tr><td class="row_title">Add/Edit messages:</td><td class="row_value"><input type="checkbox" name="msg_rights" value="1"<?php echo ($_POST['msg_rights'] ? " checked='checked'":"");?> /></td></tr>
		<tr><td class="row_title">Administrator:</td><td class="row_value"><input type="checkbox" name="admin_rights" value="1"<?php echo ($_POST['admin_rights'] ? " checked='checked'":"");?> /></td></tr>

		<tr><td colspan="2"><strong>Receive email</strong></td></tr>
		<tr><td class="row_title">When any event is added or edited:</td><td class="row_value"><input type="checkbox" name="email_notify" value="1"<?php echo ($_POST['email_notify'] ? " checked='checked'":"");?> /></td></tr>
<?php
	}
?>
		<tr><td colspan="2" class="yellow" align="right"><input type="image" name="save2" src="media/btns/save.gif" width="38" height="18" border="0" alt="Save Record" align="absmiddle" />&nbsp;&nbsp;<a href="<?php echo $back_url;?>"><img src="media/btns/cancel.gif" width="51" height="18" border="0" alt="Cancel Changes" align="absmiddle" /></a></td></tr>
	</table>
</form>
</td></tr>
</table>

<?php
	include("includes/html_footer.php");

	// end of main //
	//////////////////////////////////////////////////////////////////////////////////////////////////

	function save_item() {
		$db = new db_mysql;

		$msg = 0;

		if (empty($_POST['uname']) || empty($_POST['username']) || empty($_POST['user_pass'])) {
			$msg = "Please complete all the fields.";
		}
		else if ($_POST['prev_user']!=$_POST['username'] && check_username($_POST['username'])) {
			$msg = "The Username '{$_POST['username']}' has already been taken.";
		}
		else if (!empty($_POST['email']) && !validate_email($_POST['email'])) {
			$msg = "Please provide a valid Contact Email address.";
		}
		else {
			$location_rights = @implode("|", $_POST['loc_rights']);

			if (!isset($_POST['msg_rights'])) $_POST['msg_rights']=0;
			if (!isset($_POST['admin_rights'])) $_POST['admin_rights']=0;
			if (!isset($_POST['email_notify'])) $_POST['email_notify']=0;

			if (!empty($_POST['id'])) {
				$db->query("UPDATE admin_users SET name=\"" . addslashes($_POST['uname']) . "\", email=\"" . addslashes($_POST['email']) . "\", username=\"" . addslashes($_POST['username']) . "\", password=\"" . addslashes($_POST['user_pass']) . "\", location_rights=\"{$location_rights}\", msg_rights={$_POST['msg_rights']}, admin_rights={$_POST['admin_rights']}, email_notify={$_POST['email_notify']}, modified_by=\"{$_SESSION['admin_name']}\", modified_date=NOW() WHERE id = {$_POST['id']}");
			}
			else {
				$db->query("INSERT INTO admin_users (name, email, username, password, location_rights, msg_rights, admin_rights, email_notify, modified_by, modified_date) VALUES (\"" . addslashes($_POST['uname']) . "\", \"" . addslashes($_POST['email']) . "\", \"" . addslashes($_POST['username']) . "\", \"" . addslashes($_POST['user_pass']) . "\", \"{$location_rights}\", {$_POST['msg_rights']}, {$_POST['admin_rights']}, {$_POST['email_notify']}, \"{$_SESSION['admin_name']}\", NOW())");
			}
		}

		return $msg;
	}

	function check_username($user) {
		$db = new db_mysql;
		$exists = false;

		$db->query("SELECT id FROM admin_users WHERE username=\"{$user}\"");
		if ($db->num_rows() > 0) {
			$exists = true;
		}

		return $exists;
	}
?>
