<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		  admin_event.php
//	Desc:		  add/edit an event
//  Client:		BCD
//	Author:		Rob Curle
//	Date:		  23 November 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////

include("includes/startup.php");
include("includes/image.php");
if (empty($_SESSION['loc_rights'])) {
	msg ("Events", "Sorry, you do not have access rights to this page.", HOME);
}

$db = new db_mysql;
$back_url = ($_REQUEST['r'] ? "event.php?id={$_REQUEST['id']}":"admin_events.php");


switch ($_REQUEST['a']) {
	case "save":
		$msg = save_item();
		if (empty($msg)) msg("Events", "This Event has now been saved.", $back_url);
		break;
	case "ap": $db->query("INSERT INTO payments (event_id) VALUES ({$_REQUEST['id']})"); break;
	case "ric": question_msg ("Events", "Are you sure you want to delete this Image?", $_SERVER['PHP_SELF']."?id=".$_GET['id']."&a=ri", $_SERVER['PHP_SELF']."?id=".$_GET['id']); break;
	case "ri": remove_image(); break;
}

$user_locations = get_user_locations();

$page_title = "Add event";
if (!empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
	$page_title = "Edit event";
	$db->query("SELECT e.*, m.id AS mid, m.message FROM events AS e LEFT OUTER JOIN messages m ON e.id=m.event_id WHERE e.id=" . $_REQUEST['id']);
	$db->next_record();
	$event_id = $db->f("id");

	if ($db->num_rows() == 0) {
		msg ("Events", "Sorry, that event does not exist.", $back_url);
	}
	if (!array_key_exists ($db->f("location"), $user_locations)) {
		msg ("Events", "Sorry, you do not have access rights to this location.", $back_url);
	}
}


$section = 2;
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
	<!-- start of required fields, events table -->
	<input type="hidden" name="id" value="<?php echo $event_id;?>" />
	<input type="hidden" name="visible" value="1">
	<tr><td class="row_title">Event created:</td><td class="row_title"><?php echo last_modified($db->f("created_date"), $db->f("created_by"));?></td></tr>
	<tr><td class="row_title">Event updated:</td><td class="row_title"><?php echo last_modified($db->f("modified_date"), $db->f("modified_by"));?></td></tr>

	<tr><td colspan="2"><strong>Event Details</strong></td></tr>
	<tr><td class="row_title">Status: <span class="green">*</span></td><td class="row_value"><select name="status" size="1"><option value=""> Choose... </option><?php
		foreach ($event_status as $id => $txt) {
			echo "<option value=\"$id\"";
			if (!isset($_POST['status']) && $id==$db->f("status")) echo " selected='selected'";
			else if ($id==$_POST['status']) echo " selected='selected'";
			echo "> $txt </option>";
		}
	?></select></td></tr>
	<tr><td class="row_title">Title: <span class="green">*</span></td><td class="row_value"><input name="title" maxlength="255" type="text" size="45" value="<?php echo ($_POST['title'] ? $_POST['title']:$db->f("event_title"));?>" /></td></tr>

	<tr><td class="row_title">Organised by: <span class="green">*</span></td><td class="row_value"><select name="hosted_by" size="1" onchange="disable_other(this, <?php echo OTHER_IDX;?>)"><option value=""> Choose... </option><?php

	foreach ($hosted_by as $id => $txt) {

		echo '<option value="' . $id . '"' . ( $id == $db->f('hosted_by') ? ' selected="selected"' : '' ) . '> ' . $txt . ' </option>' ;
	}

	?></select> If other, please specify:<br />
	<input name="hosted_other" maxlength="100" type="text" size="45" value="<?php echo  $_POST['hosted_other'] . '"' . ( $_POST['hosted_by'] != OTHER_IDX ? ' disabled="disabled"' : '' );?> /></td></tr>

<!--
	<tr><td class="row_title" valign="top">Organisation: </td><td class="row_value"><input name="hosted_by" type="hidden" value="<?php echo  OTHER_IDX ;?>" /><input name="hosted_other" maxlength="100" type="text" size="45" value="<?php echo ($_POST['hosted_other'] ? $_POST['hosted_other']:$db->f("hosted_other")) . "\"" ;?> /></td></tr>
-->

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

	<tr><td class="row_title">Date: <span class="green">*</span></td><td valign="middle" class="row_value"><?php echo get_date_ctl("event_date", $db->f("event_date"));?></td></tr>
	<tr><td class="row_title">Time: <span class="green">*</span></td><td valign="middle" class="row_value"><?php echo get_time_ctl("time_from", $db->f("time_from"));?>&nbsp;&nbsp;To&nbsp;&nbsp;<?php echo get_time_ctl("time_to", $db->f("time_to"));?></td></tr>
	<tr><td class="row_title">Period of occupation: <span class="green">*</span></td><td valign="middle" class="row_value"><?php echo get_time_ctl("period_from", $db->f("period_from"));?>&nbsp;&nbsp;To&nbsp;&nbsp;<?php echo get_time_ctl("period_to", $db->f("period_to"));?></td></tr>

	<tr><td class="row_title">Number of people:</td><td class="row_value"><input type="text" name="num_people" size="10" maxlength="10" value="<?php echo (isset($_POST['num_people']) ? $_POST['num_people']:$db->f("num_people"));?>" /></td></tr>
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

		include ("includes/event_contact.php");

?>
	<tr><td colspan="2"><strong>Notes</strong></td></tr>
	<tr><td class="row_title"> </td><td class="row_value"><textarea name="notes" class="notes"><?php echo (isset($_POST['notes']) ? $_POST['notes']:$db->f("notes"));?></textarea></td></tr>

	<!-- end of required fields, events table -->

<?php
//		include ("includes/event_setup.php");
//		include ("includes/event_equipment.php");
//		include ("includes/event_drinks.php");
//		include ("includes/event_catering.php");
//		include ("includes/event_staff.php");
//		include ("includes/event_security.php");
//		include ("includes/event_payments.php");
?>

	<tr><td colspan="2"><strong>Equipment</strong></td></tr>
	<tr><td class="row_title"> </td><td class="row_value"><textarea name="equipment" class="msg"><?php echo (isset($_POST['equipment']) ? $_POST['equipment']:$db->f("equipment"));?></textarea></td></tr>

	<tr><td colspan="2"><strong>Catering</strong></td></tr>
	<tr><td class="row_title"> </td><td class="row_value"><textarea name="catering" class="msg"><?php echo (isset($_POST['catering']) ? $_POST['catering']:$db->f("catering"));?></textarea></td></tr>

	<tr><td colspan="2"><strong>Security</strong></td></tr>
	<tr><td class="row_title"> </td><td class="row_value"><textarea name="security" class="msg"><?php echo (isset($_POST['security']) ? $_POST['security']:$db->f("security"));?></textarea></td></tr>

	<tr><td class="row_title">Total cost &pound;:</td><td class="row_value"><input type="text" name="total_cost" size="10" maxlength="5" onkeypress="return numerics(event)" value="<?php echo (isset($_POST['total_cost']) ? number_format($_POST['total_cost'], 2):number_format($db->f("total_cost"), 2));?>" /></td></tr>
<?php

} else {

  //adding new event - limit the user's options
  $hosted_by = array(1 => "The Building Centre", 3 => "New London Architecture", 4 => "Other...");
  $hosted_by_abr = array(1 => "BC", 3 => "NLA", 4 => "Other");

?>
	<!-- start of required fields, events table -->
	<input type="hidden" name="id" value="">
	<input type="hidden" name="visible" value="1">

	<tr><td colspan="2"><strong>Event Details</strong></td></tr>
	<tr><td class="row_title">Status: <span class="green">*</span></td><td class="row_value"><select name="status" size="1"><option value=""> Choose... </option><?php
		foreach ($event_status as $id => $txt) {
			echo "<option value=\"$id\"".($id==$_POST['status'] ? " selected='selected'":"")."> $txt </option>";
		}
	?></select></td></tr>
	<tr><td class="row_title">Title: <span class="green">*</span></td><td class="row_value"><input name="title" maxlength="255" type="text" size="45" value="<?php echo $_POST['title'];?>" /></td></tr>

	<tr><td class="row_title">Organised by: <span class="green">*</span></td><td class="row_value"><select name="hosted_by" size="1" onchange="disable_other(this, <?php echo OTHER_IDX;?>)"><option value=""> Choose... </option><?php
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

	<tr><td class="row_title">Date: <span class="green">*</span></td><td valign="middle" class="row_value"><?php echo get_date_ctl("event_date");?></td></tr>
	<tr><td class="row_title">Time: <span class="green">*</span></td><td valign="middle" class="row_value"><?php echo get_time_ctl("time_from");?>&nbsp;&nbsp;To&nbsp;&nbsp;<?php echo get_time_ctl("time_to");?></td></tr>
	<tr><td class="row_title">Period of occupation: <span class="green">*</span></td><td valign="middle" class="row_value"><?php echo get_time_ctl("period_from");?>&nbsp;&nbsp;To&nbsp;&nbsp;<?php echo get_time_ctl("period_to");?></td></tr>
	<tr><td class="row_title">Number of people:</td><td class="row_value"><input type="text" name="num_people" size="10" maxlength="10" value="<?php echo $_POST['num_people'];?>" /></td></tr>
	<tr><td class="row_title" valign="top">Image:</td><td class="row_value"><span class="grey">Image format: GIF, JPG or JPEG, <?php echo IMG_WIDTH;?>W x <?php echo IMG_HEIGHT;?>H.</span><br /><br /><input type="file" name="upload" /></td></tr>

	<tr><td colspan="2"><strong>Reception Screen Message</strong></td></tr>
	<tr><td class="row_title">On / Off:</td><td class="row_value"><input name="info_screen" type="radio" value="1" checked="checked" /> On <input name="info_screen" type="radio" value="0" /> Off</td></tr>
<?php
	if ($_SESSION['msg_rights']) {
		echo "<tr><td class=\"row_title\">Message:</td><td class=\"row_value\"><textarea name=\"message\" class=\"msg\">{$_POST['message']}</textarea></td></tr>\n";
	}
?>
<?php

		include ("includes/event_contact.php");

?>
	<tr><td colspan="2"><strong>Notes</strong></td></tr>
	<tr><td class="row_title"> </td><td class="row_value"><textarea name="notes" class="notes"><?php echo $_POST['notes'];?></textarea></td></tr>

<!-- end of required fields, events table -->

<?php
//		include ("includes/event_setup.php");
//		include ("includes/event_equipment.php");
//		include ("includes/event_drinks.php");
//		include ("includes/event_catering.php");
//		include ("includes/event_staff.php");
//		include ("includes/event_security.php");
//		include ("includes/event_payments.php");
?>

	<tr><td colspan="2"><strong>Equipment</strong></td></tr>
	<tr><td class="row_title"> </td><td class="row_value"><textarea name="equipment" class="msg"><?php echo  $_POST['equipment'] ;?></textarea></td></tr>

	<tr><td colspan="2"><strong>Catering</strong></td></tr>
	<tr><td class="row_title"> </td><td class="row_value"><textarea name="catering" class="msg"><?php echo  $_POST['catering'] ;?></textarea></td></tr>

	<tr><td colspan="2"><strong>Security</strong></td></tr>
	<tr><td class="row_title"> </td><td class="row_value"><textarea name="security" class="msg"><?php echo  $_POST['security'] ;?></textarea></td></tr>

	<tr><td class="row_title">Total cost &pound;:</td><td class="row_value"><input type="text" name="total_cost" size="10" maxlength="5" onkeypress="return numerics(event)" value="<?php echo number_format($_POST['total_cost'], 2);?>" /></td></tr>
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


		if (empty($_POST['status']) || empty($_POST['title']) || empty($_POST['hosted_by']) || empty($_POST['location']) || empty($_POST['event_date']) || empty($_POST['time_from']) || empty($_POST['time_to'])) {
			$msg = "Please complete all fields marked with *";
		}
		else if (!validate_date($_POST['event_date'])) {
			$msg = "Please provide a valid event date, in the format ccyy-mm-dd.";
		}
		else if (!validate_time($_POST['time_from'])) {
			$msg = "Please provide a valid from time, in the format hh:mm.";
		}
		else if (!validate_time($_POST['time_to'])) {
			$msg = "Please provide a valid to time, in the format hh:mm in 24 hour clock.";
		}
		else if (!empty($_POST['period_from']) && !validate_time($_POST['period_from'])) {
			$msg = "Please provide a valid period from, in the format hh:mm in 24 hour clock.";
		}
		else if (!empty($_POST['period_to']) && !validate_time($_POST['period_to'])) {
			$msg = "Please provide a valid period to, in the format hh:mm in 24 hour clock.";
		}
//		else if ($_POST['hosted_by']==OTHER_IDX && empty($_POST['hosted_other'])) {
//			$msg = "Please provide the Hosted by field.";
//		}
		else if ($_POST['location']==NLA_SEMINAR && $nla_suite==0) {
			$msg = "Please select the location A, B, C.";
		}
		else {
			if (empty($_POST['visible'])) $_POST['visible']=0;
			if (empty($_POST['period_from'])) $period_from = "null";
			else $period_from = "'{$_POST['period_from']}'";
			if (empty($_POST['period_to'])) $period_to = "null";
			else $period_to = "'{$_POST['period_to']}'";


			if (!empty($_POST['id'])) {

				$db->query("UPDATE events SET info_screen={$_POST['info_screen']}, visible={$_POST['visible']}, status={$_POST['status']}, event_title=\"".addslashes($_POST['title'])."\", hosted_by={$_POST['hosted_by']}, hosted_other=\"".addslashes($_POST['hosted_other'])."\", location={$_POST['location']}, nla_suite={$nla_suite}, event_date='{$_POST['event_date']}', period_from={$period_from}, period_to={$period_to}, time_from='{$_POST['time_from']}', time_to='{$_POST['time_to']}', num_people=\"".addslashes($_POST['num_people'])."\", notes=\"".addslashes($_POST['notes'])."\", equipment=\"".addslashes($_POST['equipment'])."\", catering=\"".addslashes($_POST['catering'])."\", security=\"".addslashes($_POST['security'])."\", total_cost={$_POST['total_cost']}{$upd}, modified_by=\"{$_SESSION['admin_name']}\", modified_date=NOW() WHERE id = " . $_POST['id']);
				$event_id = $_POST['id'];
				$action = "updated";
			} else {

				$sql = "INSERT INTO events (info_screen, visible, status, event_title, hosted_by, hosted_other, location, nla_suite, event_date, period_from, period_to, time_from, time_to, num_people, notes, equipment, catering, security, total_cost{$ins_f}, created_by, created_date, modified_by, modified_date) VALUES (";
				$sql .= "{$_POST['info_screen']}, {$_POST['visible']}, {$_POST['status']}, \"".addslashes($_POST['title'])."\", {$_POST['hosted_by']}, \"".addslashes($_POST['hosted_other'])."\", {$_POST['location']}, {$nla_suite}, '{$_POST['event_date']}', {$period_from}, {$period_to}, '{$_POST['time_from']}', '{$_POST['time_to']}', \"".addslashes($_POST['num_people'])."\", \"".addslashes($_POST['notes'])."\", \"".addslashes($_POST['equipment'])."\", \"".addslashes($_POST['catering'])."\", \"".addslashes($_POST['security'])."\", {$_POST['total_cost']}{$ins_v}, \"{$_SESSION['admin_name']}\", NOW(), \"{$_SESSION['admin_name']}\", NOW())";


//echo $sql ;


				$db->query($sql);
				$event_id = $db->insert_id();
				$action = "added";
			}

			// save message //
			if (!empty($_POST['message'])) {

				$time_from = date(TIME_24, strtotime("-15 min" , strtotime($_POST['time_from'])));
				$time_to = $_POST['time_to'];
				if (!empty($_POST['message_id'])) {

					$db->query("UPDATE messages SET active=1, event_id={$event_id}, date_from='{$_POST['event_date']}', date_to='{$_POST['event_date']}', time_from='{$time_from}', time_to='{$time_to}', message=\"".addslashes($_POST['message'])."\", modified_by=\"{$_SESSION['admin_name']}\", modified_date=NOW() WHERE id = {$_POST['message_id']}");
				} else {

					$db->query("INSERT INTO messages (active, event_id, date_from, date_to, time_from, time_to, message, created_by, created_date, modified_by, modified_date) VALUES (1, {$event_id}, '{$_POST['event_date']}', '{$_POST['event_date']}', '{$time_from}', '{$time_to}', \"".addslashes($_POST['message'])."\", \"{$_SESSION['admin_name']}\", NOW(), \"{$_SESSION['admin_name']}\", NOW())");
				}
			} else {

				if (!empty($_POST['message_id'])) {

          $db->query("DELETE FROM messages WHERE id = {$_POST['message_id']}");
				}
			}

			// save additional data //
			$msg = save_contact($event_id);
//			if (empty($msg)) $msg = save_setup($event_id);
//			if (empty($msg)) $msg = save_equipment($event_id);
//			if (empty($msg)) $msg = save_drinks($event_id);
//			if (empty($msg)) $msg = save_catering($event_id);
//			if (empty($msg)) $msg = save_staff($event_id);
//			if (empty($msg)) $msg = save_security($event_id);
//			if (empty($msg)) $msg = save_payments($event_id);


			include("includes/email.php");
			send_edit_notifiy_emails("event.php?id={$event_id}", $_POST['title'], "event", $action, $_SESSION['admin_name']);
		}

		return $msg;
	}

	function remove_image() {
		$db = new db_mysql;
		$db->query("SELECT image_src FROM events WHERE id = " . $_REQUEST['id']);
		$db->next_record();

		delete_image($db->f("image_src"));
		$db->query("UPDATE events SET image_src='' WHERE id = " . $_REQUEST['id']);
	}

	function save_contact($event_id) {
		$db = new db_mysql;
		$msg = "";

		if (!empty($_POST['email']) && !validate_email($_POST['email'])) {
			$msg = "Please provide a valid Contact Email address.";
		}
		else if (!empty($_POST['contact_name']) || !empty($_POST['tel']) || !empty($_POST['fax']) || !empty($_POST['email']) || !empty($_POST['company']) || !empty($_POST['address_1']) || !empty($_POST['address_2']) || !empty($_POST['nla_partner']) || !empty($_POST['contact_notes'])) {
			if (!empty($_POST['contact_id'])) {
				$sql = "UPDATE contacts SET event_id={$event_id}, contact_name=\"".addslashes($_POST['contact_name'])."\", tel=\"".addslashes($_POST['tel'])."\", fax=\"".addslashes($_POST['fax'])."\", email=\"".addslashes($_POST['email'])."\", company=\"".addslashes($_POST['company'])."\", address_1=\"".addslashes($_POST['address_1'])."\", address_2=\"".addslashes($_POST['address_2'])."\", nla_partner=\"" . $_POST['nla_partner'] . "\", contact_notes=\"".addslashes($_POST['contact_notes'])."\" WHERE id = {$_POST['contact_id']}";
			}
			else {
				$sql = "INSERT INTO contacts (event_id, contact_name, tel, fax, email, company, address_1, address_2, nla_partner, contact_notes) VALUES ({$event_id}, \"".addslashes($_POST['contact_name'])."\", \"".addslashes($_POST['tel'])."\", \"".addslashes($_POST['fax'])."\", \"".addslashes($_POST['email'])."\", \"".addslashes($_POST['company'])."\", \"".addslashes($_POST['address_1'])."\", \"".addslashes($_POST['address_2'])."\", '0', \"".addslashes($_POST['contact_notes'])."\")";
			}
			$db->query($sql);
		}

		return $msg;
	}

	function save_setup($event_id) {
		$db = new db_mysql;

		if (!empty($_POST['setup_by']) || !empty($_POST['setup_time']) || !empty($_POST['layout']) || !empty($_POST['theme']) || !empty($_POST['setup_notes'])) {
			if (!empty($_POST['setup_id'])) {
				$sql = "UPDATE setup SET event_id={$event_id}, provided_by={$_POST['setup_by']}, setup_time={$_POST['setup_time']}, layout=\"".addslashes($_POST['layout'])."\", theme=\"".addslashes($_POST['theme'])."\", setup_notes=\"".addslashes($_POST['setup_notes'])."\" WHERE id = {$_POST['setup_id']}";
			}
			else {
				$sql = "INSERT INTO setup (event_id, provided_by, setup_time, layout, theme, setup_notes) VALUES ({$event_id}, {$_POST['setup_by']}, {$_POST['setup_time']}, \"".addslashes($_POST['layout'])."\", \"".addslashes($_POST['theme'])."\", \"".addslashes($_POST['setup_notes'])."\")";
			}
			$db->query($sql);
		}
	}

	function save_equipment($event_id) {
		$db = new db_mysql;

		$equipment = 0;
		if (is_array($_POST['equipment'])) {
			foreach ($_POST['equipment'] as $id => $val) {
				 set_bit($equipment, $val);
			}
		}

		if (!empty($_POST['equip_by']) || !empty($equipment) || !empty($_POST['equip_notes'])) {
			if (!empty($_POST['equipment_id'])) {
				$sql = "UPDATE equipment SET event_id={$event_id}, provided_by={$_POST['equip_by']}, equipment={$equipment}, equip_notes=\"".addslashes($_POST['equip_notes'])."\" WHERE id = {$_POST['equipment_id']}";
			}
			else {
				$sql = "INSERT INTO equipment (event_id, provided_by, equipment, equip_notes) VALUES ({$event_id}, {$_POST['equip_by']}, {$equipment}, \"".addslashes($_POST['equip_notes'])."\")";
			}
			$db->query($sql);
		}
	}

	function save_drinks($event_id) {
		$db = new db_mysql;
		$msg = "";

		if (!is_numeric($_POST['drinks_cost'])) {
			$msg = "Please provide a numberic for total cost.";
		}
		else {
			if (!empty($_POST['drinks_by']) || !empty($_POST['coffee']) || !empty($_POST['tea']) || !empty($_POST['biscuits']) || !empty($_POST['orange']) || !empty($_POST['water']) || !empty($_POST['wine']) || !empty($_POST['beer']) || ($_POST['drinks_cost']>0) || !empty($_POST['drinks_notes'])) {
				if (!empty($_POST['drinks_id'])) {
					$sql = "UPDATE drinks SET event_id={$event_id}, provided_by={$_POST['drinks_by']}, coffee=\"".addslashes($_POST['coffee'])."\", tea=\"".addslashes($_POST['tea'])."\", biscuits=\"".addslashes($_POST['biscuits'])."\", orange=\"".addslashes($_POST['orange'])."\", water=\"".addslashes($_POST['water'])."\", wine=\"".addslashes($_POST['wine'])."\", beer=\"".addslashes($_POST['beer'])."\", drinks_cost={$_POST['drinks_cost']}, drinks_notes=\"".addslashes($_POST['drinks_notes'])."\" WHERE id = {$_POST['drinks_id']}";
				}
				else {
					$sql = "INSERT INTO drinks (event_id, provided_by, coffee, tea, biscuits, orange, water, wine, beer, drinks_cost, drinks_notes) VALUES ({$event_id}, {$_POST['drinks_by']}, \"".addslashes($_POST['coffee'])."\", \"".addslashes($_POST['tea'])."\", \"".addslashes($_POST['biscuits'])."\", \"".addslashes($_POST['orange'])."\", \"".addslashes($_POST['water'])."\", \"".addslashes($_POST['wine'])."\", \"".addslashes($_POST['beer'])."\", {$_POST['drinks_cost']}, \"".addslashes($_POST['drinks_notes'])."\")";
				}
				$db->query($sql);
			}
		}

		return $msg;
	}

	function save_catering($event_id) {
		$db = new db_mysql;
		$msg = "";

		if (empty($_POST['people_1'])) $_POST['people_1'] = 0;
		if (empty($_POST['people_2'])) $_POST['people_2'] = 0;
		if (empty($_POST['time_1']) || $_POST['time_1']=='hh:mm') $time1 = "null";
		else $time1 = "'".$_POST['time_1']."'";
		if (empty($_POST['time_2']) || $_POST['time_2']=='hh:mm') $time2 = "null";
		else $time2 = "'".$_POST['time_2']."'";

		if (!is_numeric($_POST['people_1']) || !is_numeric($_POST['people_2']) || !is_numeric($_POST['person_cost']) || !is_numeric($_POST['catering_cost'])) {
			$msg = "Please provide a numberics for the catering numbers of people and the costs.";
		}
		else {
			if (!empty($_POST['catering_by']) || !empty($_POST['menu_1']) || !empty($_POST['menu_2']) || !empty($_POST['people_1']) || !empty($_POST['people_2']) || !empty($_POST['catering_notes']) || ($_POST['person_cost']>0) || ($_POST['catering_cost']>0)) {
				if (!empty($_POST['catering_id'])) {
					$sql = "UPDATE catering SET event_id={$event_id}, provided_by={$_POST['catering_by']}, menu_1=\"".addslashes($_POST['menu_1'])."\", people_1={$_POST['people_1']}, time_1={$time1}, menu_2=\"".addslashes($_POST['menu_2'])."\", people_2={$_POST['people_2']}, time_2={$time2}, person_cost={$_POST['person_cost']}, catering_cost={$_POST['catering_cost']}, catering_notes=\"".addslashes($_POST['catering_notes'])."\" WHERE id = {$_POST['catering_id']}";
				}
				else {
					$sql = "INSERT INTO catering (event_id, provided_by, menu_1, people_1, time_1, menu_2, people_2, time_2, person_cost, catering_cost, catering_notes) VALUES ({$event_id}, {$_POST['catering_by']}, \"".addslashes($_POST['menu_1'])."\", {$_POST['people_1']}, {$time1}, \"".addslashes($_POST['menu_2'])."\", {$_POST['people_2']}, {$time2}, {$_POST['person_cost']}, {$_POST['catering_cost']}, \"".addslashes($_POST['catering_notes'])."\")";
				}
				$db->query($sql);
			}
		}

		return $msg;
	}

	function save_staff($event_id) {
		$db = new db_mysql;
		$msg = "";

		if (!is_numeric($_POST['staff_cost'])) {
			$msg = "Please provide a numberic for the staff cost.";
		}
		else {
			if (!empty($_POST['staff_by']) || !empty($_POST['reg_attendants']) || !empty($_POST['food_waiters']) || !empty($_POST['drinks_waiters']) || !empty($_POST['cloak_attendants']) || ($_POST['staff_cost']>0) || !empty($_POST['staff_notes'])) {
				if (!empty($_POST['staff_id'])) {
					$sql = "UPDATE staff SET event_id={$event_id}, provided_by={$_POST['staff_by']}, reg_attendants=\"".addslashes($_POST['reg_attendants'])."\", food_waiters=\"".addslashes($_POST['food_waiters'])."\", drinks_waiters=\"".addslashes($_POST['drinks_waiters'])."\", cloak_attendants=\"".addslashes($_POST['cloak_attendants'])."\", staff_cost={$_POST['staff_cost']}, staff_notes=\"".addslashes($_POST['staff_notes'])."\" WHERE id = {$_POST['staff_id']}";
				}
				else {
					$sql = "INSERT INTO staff (event_id, provided_by, reg_attendants, food_waiters, drinks_waiters, cloak_attendants, staff_cost, staff_notes) VALUES ({$event_id}, {$_POST['staff_by']}, \"".addslashes($_POST['reg_attendants'])."\", \"".addslashes($_POST['food_waiters'])."\", \"".addslashes($_POST['drinks_waiters'])."\", \"".addslashes($_POST['cloak_attendants'])."\", {$_POST['staff_cost']}, \"".addslashes($_POST['staff_notes'])."\")";
				}
				$db->query($sql);
			}
		}

		return $msg;
	}

	function save_security($event_id) {
		$db = new db_mysql;
		$msg = "";

		if (!is_numeric($_POST['security_cost'])) {
			$msg = "Please provide a numberic for the security cost.";
		}
		else {
			if (!empty($_POST['security_by']) || !empty($_POST['officers']) || ($_POST['security_cost']>0) || !empty($_POST['security_notes'])) {
				if (!empty($_POST['security_id'])) {
					$sql = "UPDATE security SET event_id={$event_id}, provided_by={$_POST['security_by']}, officers=\"".addslashes($_POST['officers'])."\", security_cost={$_POST['security_cost']}, security_notes=\"".addslashes($_POST['security_notes'])."\" WHERE id = {$_POST['security_id']}";
				}
				else {
					$sql = "INSERT INTO security (event_id, provided_by, officers, security_cost, security_notes) VALUES ({$event_id}, {$_POST['security_by']}, \"".addslashes($_POST['officers'])."\", {$_POST['security_cost']}, \"".addslashes($_POST['security_notes'])."\")";
				}
				$db->query($sql);
			}
		}

		return $msg;
	}

	function save_payments($event_id) {
		$db = new db_mysql;
		$msg = "";

		for ($i=1; $i<=$_POST['payment_num']; $i++) {
			if (!empty($_POST["service_{$i}"]) || !empty($_POST["inv_by_{$i}"]) || ($_POST["cost_{$i}"]>0) || !empty($_POST["date_{$i}"])) {
				if (!is_numeric($_POST["cost_{$i}"])) {
					$msg = "Please provide a numberic cost on payment row {$i}.";
					break;
				}
				else if (!empty($_POST["date_{$i}"]) && !validate_date($_POST["date_{$i}"])) {
					$msg = "Please provide a valid event date, in the format ccyy-mm-dd on payment row {$i}.";
				}
				else {
					if (empty($_POST["paid_{$i}"])) $_POST["paid_{$i}"]=0;
					if (empty($_POST["date_{$i}"])) $payment_date = "null";
					else $payment_date = "'".$_POST["date_{$i}"]."'";

					if (!empty($_POST["pay_id_{$i}"])) {
						$sql = "UPDATE payments SET event_id={$event_id}, service=\"".addslashes($_POST["service_{$i}"])."\", inv_by=\"".addslashes($_POST["inv_by_{$i}"])."\", cost=".$_POST["cost_{$i}"].", paid=".$_POST["paid_{$i}"].", date={$payment_date} WHERE id = " . $_POST["pay_id_{$i}"];
					}
					else {
						$sql = "INSERT INTO payments (event_id, service, inv_by, cost, paid, date) VALUES ({$event_id}, \"".addslashes($_POST["service_{$i}"])."\", \"".addslashes($_POST["inv_by_{$i}"])."\", ".$_POST["cost_{$i}"].", ".$_POST["paid_{$i}"].", {$payment_date})";
					}
					$db->query($sql);
				}
			}
			else {
				if (!empty($_POST["pay_id_{$i}"])) {
					$db->query("DELETE FROM payments WHERE id = " . $_POST["pay_id_{$i}"]);
				}
			}
		}

		return $msg;
	}
?>