<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		admin_event.php
//	Desc:		add/edit an exhibition
//  Client:		BCD
//	Author:		Rob Curle
//	Date:		17 November 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////

include("includes/startup.php");
include("includes/image.php");

if (empty($_SESSION['loc_rights'])) {
	msg ("Exhibitions", "Sorry, you do not have access rights to this page.", HOME);
}

$db = new db_mysql;
$back_url = ($_REQUEST['r'] ? "exhibition.php?id={$_REQUEST['id']}":"admin_exhibitions.php");

switch ($_REQUEST['a']) {
	case "save":
		$msg = save_item();
		if (empty($msg)) msg("Exhibitions", "This Exhibition has now been saved.", $back_url);
		break;
	case "ric": question_msg ("Exhibitions", "Are you sure you want to delete this Image?", $_SERVER['PHP_SELF']."?id=".$_GET['id']."&a=ri", $_SERVER['PHP_SELF']."?id=".$_GET['id']); break;
	case "ri": remove_image(); break;
}

$user_locations = get_user_locations();

$page_title = "Add exhibition";
if (!empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
	$page_title = "Edit exhibition";
	$db->query("SELECT e.*, m.id AS mid, m.message FROM exhibitions AS e LEFT OUTER JOIN messages m ON e.id=m.exhib_id WHERE e.id=" . $_REQUEST['id']);
	$db->next_record();

	if ($db->num_rows() == 0) {
		msg ("Exhibitions", "Sorry, that exhibition does not exist.", $back_url);
	}
	if (!array_key_exists ($db->f("location"), $user_locations)) {
		msg ("Exhibitions", "Sorry, you do not have access rights to this location.", $back_url);
	}
}

$section = 3;
include("includes/html_header.php");
?>
<table width="750" border="0" cellpadding="7" cellspacing="0">
<tr><td colspan="2" valign="middle" class="title_bar"><?php echo $page_title;?></td><tr>
<tr><td width="750" valign="top" class="content">
<?php
if (!empty($msg)) {
	echo "<p class=\"error\">$msg</p>\n";
}
?>
<form name="userdata" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<input type="hidden" name="max_file_size" value="<?php echo IMG_FILE_SIZE;?>" />
<input type="hidden" name="r" value="<?php echo $_REQUEST['r'];?>" />
<input type="hidden" name="a" value="save" />

<table border="0" cellpadding="3" cellspacing="1" class="frame">
<tr><td colspan="2" class="yellow" align="right"><input type="image" name="save1" src="media/btns/save.gif" width="38" height="18" border="0" alt="Save Record" /></a>&nbsp;&nbsp;<a href="<?php echo $back_url;?>"><img src="media/btns/cancel.gif" width="51" height="18" border="0" alt="Cancel Changes" /></a></td></tr>
<tr><td colspan="2" class="row_title"><strong>Fields marked with * are required.</strong></td></tr>
<?php

if ($db->num_rows() > 0) {

?>
	<input type="hidden" name="id" value="<?php echo $db->f("id");?>"/>
	<tr><td class="row_title">Exhibition created:</td><td class="row_title"><?php echo last_modified($db->f("created_date"), $db->f("created_by"));?></td></tr>
	<tr><td class="row_title">Exhibition updated:</td><td class="row_title"><?php echo last_modified($db->f("modified_date"), $db->f("modified_by"));?></td></tr>

	<tr><td colspan="2"><strong>Exhibition details</strong></td></tr>
	<tr><td class="row_title">Visible:</td><td class="row_value"><input type="checkbox" name="visible" value="1"<?php echo ($_REQUEST['visible'] ? " checked='checked'" : (!isset($_POST['title'])&&$db->f("visible") ? " checked='checked'":""));?> /></td></tr>
	<tr><td class="row_title">Status: <span class="green">*</span></td><td class="row_value"><select name="status" size="1"><option value=""> Choose... </option><?php
		foreach ($event_status as $id => $txt) {
			echo "<option value=\"$id\"";
			if (!isset($_POST['status']) && $id==$db->f("status")) echo " selected='selected'";
			else if ($id==$_POST['status']) echo " selected='selected'";
			echo "> $txt </option>";
		}
	?></select></td></tr>
	<tr><td class="row_title">Title: <span class="green">*</span></td><td class="row_value"><input name="title" maxlength="255" type="text" size="45" value="<?php echo (isset($_POST['title']) ? $_POST['title']:$db->f("exhib_title"));?>" /></td></tr>
	<tr><td class="row_title" valign="top">Hosted by: <span class="green">*</span></td><td class="row_value"><select name="hosted_by" size="1" onchange="disable_other(this, <?php echo OTHER_IDX;?>)"><option value=""> Choose... </option><?php
		foreach ($hosted_by as $id => $txt) {
			echo "<option value=\"$id\"";
			if (!isset($_POST['hosted_by']) && $id==$db->f("hosted_by")) echo " selected='selected'";
			else if ($id==$_POST['hosted_by']) echo " selected='selected'";
			echo "> $txt </option>";
		}
	?>
	</select> If other, please specify:<br /><input name="hosted_other" maxlength="100" type="text" size="45" value="<?php echo ($_POST['hosted_other'] ? $_POST['hosted_other']:$db->f("hosted_other")) . "\"";
	if (!isset($_POST['hosted_by']) && $db->f("hosted_by")!=OTHER_IDX) echo " disabled='disabled'";
	else if (isset($_POST['hosted_by']) && $_POST['hosted_by']!=OTHER_IDX) echo " disabled='disabled'";
	?> /></td></tr>

	<tr><td class="row_title">Location: <span class="green">*</span></td><td class="row_value"><select name="location" size="1" onchange="change_action('');"><option value=""> Choose... </option><?php
		foreach ($user_locations as $id => $txt) {
			echo "<option value=\"$id\"";
			if (!isset($_POST['location']) && $id==$db->f("location")) echo " selected='selected'";
			else if ($id==$_POST['location']) echo " selected='selected'";
			echo "> $txt </option>";
		}
	?></select> <?php

	$nla_seminar = false;
	if (!isset($_POST['location']) && $db->f("location")==NLA_SEMINAR) $nla_seminar = true;
	else if ($_POST['location']==NLA_SEMINAR) $nla_seminar = true;

	if ($nla_seminar) {
		foreach ($nla_suite as $bit => $room) {
			echo "&nbsp;<input type=\"checkbox\" name=\"nla_suite[]\" value=\"{$bit}\"";
			if (is_array($_POST['nla_suite'])) {
				if (in_array($bit, $_POST['nla_suite'])) {
					echo " checked='checked'";
				}
			}
			else if (query_bit($db->f("nla_suite"), $bit)) {
				echo " checked='checked'";
			}
			echo " /> $room";
		}
	}
	?></td></tr>

	<tr><td class="row_title">Date: <span class="green">*</span></td><td valign="middle" class="row_value"><?php echo get_date_ctl("date_from", $db->f("date_from"));?>&nbsp;&nbsp;To&nbsp;&nbsp;<?php echo get_date_ctl("date_to", $db->f("date_to"));?></td></tr>
	<tr><td class="row_title" valign="top">Image:</td><td class="row_value"><span class="grey">Image format: GIF, JPG or JPEG, <?php echo IMG_WIDTH;?>W x <?php echo IMG_HEIGHT;?>H.</span><br /><br /><input type="file" name="upload" /><?php
	$path = get_image_path();
	if (is_file($path . $db->f("image_src"))) {
		echo "<p><img src=\"" . $path . $db->f("image_src") . "\" width=\"".$db->f("image_width")." height=\"".$db->f("image_height")."\" border=\"0\" alt=\"\" /><br/><a href=\"{$_SERVER['PHP_SELF']}?a=ric&id={$_REQUEST['id']}\">Remove image &raquo;</a></p>";
	}
	echo "<input type=\"hidden\" name=\"image_src\" value=\"".$db->f("image_src")."\" />";
	?></td></tr>

	<tr><td colspan="2"><strong>Reception Screen Message</strong></td></tr>
	<tr><td class="row_title">On / Off:</td><td class="row_value"><input name="info_screen" type="radio" value="1"<?php echo ($db->f("info_screen") ? " checked='checked'":"");?> /> On <input name="info_screen" type="radio" value="0"<?php echo (!$db->f("info_screen") ? " checked='checked'":"");?> /> Off</td></tr>
<?php

	if ($_SESSION['msg_rights']) {

		echo "<tr><td class=\"row_title\">Message:</td><td class=\"row_value\"><textarea name=\"message\" class=\"msg\">".(isset($_POST['message']) ? $_POST['message']:$db->f("message"))."</textarea><input name=\"message_id\" type=\"hidden\" value=\"" . $db->f("mid") . "\" /></td></tr>\n";
	} else {

		echo "<tr><td class=\"row_title\">Message:</td><td class=\"row_value\">" . $db->f("message") . "</td></tr>\n";
	}

?>

	<tr><td colspan="2"><strong>Notes</strong></td></tr>
	<tr><td class="row_title"> </td><td class="row_value"><textarea name="notes" class="notes"><?php echo (isset($_POST['notes']) ? $_POST['notes']:$db->f("notes"));?></textarea></td></tr>
<?php

} else {

  //adding new event - limit the user's options
  $hosted_by = array(1 => "The Building Centre", 3 => "New London Architecture", 4 => "Other...");
  $hosted_by_abr = array(1 => "BC", 3 => "NLA", 4 => "Other");

?>
		<input type="hidden" name="id" value="">
		<tr><td colspan="2"><strong>Exhibition details</strong></td></tr>
		<tr><td class="row_title">Visible:</td><td class="row_value"><input type="checkbox" name="visible" value="1"<?php echo ($_REQUEST['visible'] ? " checked='checked'" : (!isset($_POST['title']) ? " checked='checked'":""));?> /></td></tr>
		<tr><td class="row_title">Status: <span class="green">*</span></td><td class="row_value"><select name="status" size="1"><option value=""> Choose... </option><?php
			foreach ($event_status as $id => $txt) {
				echo "<option value=\"$id\"".($id==$_POST['status'] ? " selected='selected'":"")."> $txt </option>";
			}
		?></select></td></tr>
		<tr><td class="row_title">Title: <span class="green">*</span></td><td class="row_value"><input name="title" maxlength="255" type="text" size="45" value="<?php echo $_POST['title'];?>" /></td></tr>

		<tr><td class="row_title" valign="top">Organised by: <span class="green">*</span></td><td class="row_value"><select name="hosted_by" size="1" onchange="disable_other(this, <?php echo OTHER_IDX;?>)"><option value=""> Choose... </option><?php
			foreach ($hosted_by as $id => $txt) {
				echo "<option value=\"$id\"".($id==$_POST['hosted_by'] ? " selected='selected'":"")."> $txt </option>";
			}
		?></select> If other, please specify:<br /><input name="hosted_other" maxlength="100" type="text" size="45" value="<?php echo $_POST['hosted_other']."\"" . ($_POST['hosted_by']!=OTHER_IDX ? " disabled='disabled'":"");?> /></td></tr>

		<tr><td class="row_title">Location: <span class="green">*</span></td><td class="row_value"><select name="location" size="1" onchange="change_action('');"><option value=""> Choose... </option><?php
			foreach ($user_locations as $id => $txt) {
				echo "<option value=\"$id\"".($id==$_POST['location'] ? " selected='selected'":"")."> $txt </option>";
			}
		?></select><?php
		if ($_POST['location'] == NLA_SEMINAR) {
			foreach ($nla_suite as $bit => $room) {
				echo "&nbsp;<input type=\"checkbox\" name=\"nla_suite[]\" value=\"{$bit}\"";
				if (is_array($_POST['nla_suite'])) {
					if (in_array($bit, $_POST['nla_suite'])) {
						echo " checked='checked'";
					}
				}
				echo " /> $room";
			}
		}
		?></td></tr>

		<tr><td class="row_title">Date: <span class="green">*</span></td><td valign="middle" class="row_value"><?php echo get_date_ctl("date_from", 'now');?>&nbsp;&nbsp;To&nbsp;&nbsp;<?php echo get_date_ctl("date_to");?></td></tr>
		<tr><td class="row_title" valign="top">Image:</td><td class="row_value"><span class="grey">Image format: GIF, JPG or JPEG, <?php echo IMG_WIDTH;?>W x <?php echo IMG_HEIGHT;?>H.</span><br /><br /><input type="file" name="upload" /></td></tr>

		<tr><td colspan="2"><strong>Reception Screen Message</strong></td></tr>
		<tr><td class="row_title">On / Off:</td><td class="row_value"><input name="info_screen" type="radio" value="1" checked="checked" /> On <input name="info_screen" type="radio" value="0" /> Off</td></tr>
<?php
		if ($_SESSION['msg_rights']) {
			echo "<tr><td class=\"row_title\">Message:</td><td class=\"row_value\"><textarea name=\"message\" class=\"msg\">{$_POST['message']}</textarea></td></tr>\n";
		}
?>

		<tr><td colspan="2"><strong>Notes</strong></td></tr>
		<tr><td class="row_title"> </td><td class="row_value"><textarea name="notes" class="notes"><?php echo $_POST['notes'];?></textarea></td></tr>
<?php
	}
?>
	<tr><td colspan="2" class="yellow" align="right"><input type="image" name="save2" src="media/btns/save.gif" width="38" height="18" border="0" alt="Save Record" /></a>&nbsp;&nbsp;<a href="<?php echo $back_url;?>"><img src="media/btns/cancel.gif" width="51" height="18" border="0" alt="Cancel Changes" /></a></td></tr>
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
		$msg = "";

		$nla_suite = 0;
		if (is_array($_POST['nla_suite'])) {
			foreach ($_POST['nla_suite'] as $id => $val) {
				 set_bit($nla_suite, $val);
			}
		}

		if (!empty($_FILES['upload']['name'])) {
			$path = get_image_path();
			$image_filename = date("dmyHis") . substr($_FILES['upload']['name'], -4);

			$msg = upload_image ($path, $image_filename, IMG_WIDTH, IMG_HEIGHT);
			if (empty($msg)) {
				delete_image($_POST['image_src']);
				$ins_f = ", image_src";
				$ins_v = ", \"{$image_filename}\"";
				$upd = ", image_src=\"{$image_filename}\"";
			}
			else {
				return $msg;
			}
		}


		if (empty($_POST['status']) || empty($_POST['title']) || empty($_POST['hosted_by']) || empty($_POST['location']) || empty($_POST['date_from']) || empty($_POST['date_to'])) {
			$msg = "Please complete all fields marked with *";
		}
		else if (!validate_date($_POST['date_from'])) {
			$msg = "Please provide a valid from date, in the format ccyy-mm-dd.";
		}
		else if (!validate_date($_POST['date_to'])) {
			$msg = "Please provide a valid to date, in the format ccyy-mm-dd.";
		}
		else if ($_POST['hosted_by']==OTHER_IDX && empty($_POST['hosted_other'])) {
			$msg = "Please provide the Hosted by field.";
		}
		else if ($_POST['location']==NLA_SEMINAR && $nla_suite==0) {
			$msg = "Please select the location A, B, C.";
		}
		else {
			if (empty($_POST['visible'])) $_POST['visible']=0;

			if (!empty($_POST['id'])) {
				$sql = "UPDATE exhibitions SET info_screen={$_POST['info_screen']}, visible={$_POST['visible']}, status={$_POST['status']}, exhib_title=\"".addslashes($_POST['title'])."\", hosted_by={$_POST['hosted_by']}, hosted_other=\"".addslashes($_POST['hosted_other'])."\", location={$_POST['location']}, nla_suite={$nla_suite}, date_from='{$_POST['date_from']}', date_to='{$_POST['date_to']}', notes=\"".addslashes($_POST['notes'])."\"{$upd}, modified_by=\"{$_SESSION['admin_name']}\", modified_date=NOW() WHERE id = " . $_POST['id'];

				$db->query($sql);
				$exhib_id = $_POST['id'];
				$action = "updated";
			}
			else {
				$sql = "INSERT INTO exhibitions (info_screen, visible, status, exhib_title, hosted_by, hosted_other, location, nla_suite, date_from, date_to, notes{$ins_f}, created_by, created_date, modified_by, modified_date) VALUES ({$_POST['info_screen']}, {$_POST['visible']}, {$_POST['status']}, \"".addslashes($_POST['title'])."\", {$_POST['hosted_by']}, \"".addslashes($_POST['hosted_other'])."\", {$_POST['location']}, {$nla_suite}, '{$_POST['date_from']}', '{$_POST['date_to']}', \"".addslashes($_POST['notes'])."\"{$ins_v}, \"{$_SESSION['admin_name']}\", NOW(), \"{$_SESSION['admin_name']}\", NOW())";

				$db->query($sql);
				$exhib_id = $db->insert_id();
				$action = "added";
			}

			// save message //
			if (!empty($_POST['message'])) {
				if (!empty($_POST['message_id'])) {
					$db->query("UPDATE messages SET active=1, exhib_id={$exhib_id}, date_from='{$_POST['date_from']}', date_to='{$_POST['date_to']}', message=\"".addslashes($_POST['message'])."\", modified_by=\"{$_SESSION['admin_name']}\", modified_date=NOW() WHERE id = {$_POST['message_id']}");
				}
				else {
					$db->query("INSERT INTO messages (active, exhib_id, date_from, date_to, message, created_by, created_date, modified_by, modified_date) VALUES (1, {$exhib_id}, '{$_POST['date_from']}', '{$_POST['date_to']}', \"".addslashes($_POST['message'])."\", \"{$_SESSION['admin_name']}\", NOW(), \"{$_SESSION['admin_name']}\", NOW())");
				}
			}
			else {
				if (!empty($_POST['message_id'])) {
					$db->query("DELETE FROM messages WHERE id = {$_POST['message_id']}");
				}
			}

			include("includes/email.php");
			send_edit_notifiy_emails("exhibition.php?id={$exhib_id}", $_POST['title'], "exhibition", $action, $_SESSION['admin_name']);
		}

		return $msg;
	}

	function remove_image() {
		$db = new db_mysql;
		$db->query("SELECT image_src FROM exhibitions WHERE id = " . $_REQUEST['id']);
		$db->next_record();

		delete_image($db->f("image_src"));
		$db->query("UPDATE exhibitions SET image_src='' WHERE id = " . $_REQUEST['id']);
	}
?>