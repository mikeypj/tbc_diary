<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		admin_message.php
//	Desc:		add/edit a message
//  Client:		BCD
//	Author:		Rob Curle
//	Date:		17 November 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////

	include("includes/startup.php");
	include("includes/image.php");
	
	if (!$_SESSION['msg_rights']) {
		msg("Access Denied", "Sorry you do not have access to this page.", HOME);
	}
	
	$db = new db_mysql;
	$back_url = "admin_messages.php";
	
	switch ($_REQUEST['a']) {
		case "save":
			$msg = save_item();
			if (empty($msg)) msg("Messages", "This Message has now been saved.", $back_url);
			break;
		case "ric": question_msg ("Messages", "Are you sure you want to delete this Image?", $_SERVER['PHP_SELF']."?id=".$_GET['id']."&a=ri", $_SERVER['PHP_SELF']."?id=".$_GET['id']); break;
		case "ri": remove_image(); break;
	}	
	
	
	$page_title = "Add message";
	if (!empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
		$page_title = "Edit message";
		$db->query("SELECT m.*, e.event_title, x.exhib_title, IF (e.event_title!='', 'Event', IF (x.exhib_title!='', 'Exhibition', '')) AS attached_to FROM messages AS m LEFT OUTER JOIN events e ON m.event_id=e.id LEFT OUTER JOIN exhibitions x ON m.exhib_id=x.id WHERE m.id=" . $_REQUEST['id']);
	}	
	 
	$section = 4;
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
	<input type="hidden" name="a" value="save">
	
	<table border="0" cellpadding="3" cellspacing="1" class="frame">
	<tr><td colspan="2" class="yellow" align="right"><input type="image" name="save1" src="media/btns/save.gif" width="38" height="18" border="0" alt="Save Record" /></a>&nbsp;&nbsp;<a href="<?php echo $back_url;?>"><img src="media/btns/cancel.gif" width="51" height="18" border="0" alt="Cancel Changes" /></a></td></tr>

<?php	
	if ($db->num_rows() > 0) {
		$db->next_record();
?>
		<input type="hidden" name="id" value="<?php echo $db->f("id");?>"/>
		<tr><td class="row_title">Message created:</td><td class="row_title"><?php echo last_modified($db->f("created_date"), $db->f("created_by"));?></td></tr>
		<tr><td class="row_title">Message updated:</td><td class="row_title"><?php echo last_modified($db->f("modified_date"), $db->f("modified_by"));?></td></tr>
	<?php
		if ($db->f("attached_to")) {
			echo "<tr><td class=\"row_title\">".$db->f("attached_to").":</td><td class=\"row_value\">".($db->f("event_title") ? $db->f("event_title"):$db->f("exhib_title"))."</td></tr>\n";
		}
	?>
		<tr><td class="row_title">Status:</td><td class="row_value"><input name="active" type="radio" value="1"<?php echo ($db->f("active") ? " checked='checked'":"");?> /> On <input name="active" type="radio" value="0"<?php echo (!$db->f("active") ? " checked='checked'":"");?> /> Off</td></tr>
		<tr><td class="row_title" valign="top">Image:</td><td class="row_value"><span class="grey">Image format: GIF, JPG or JPEG, <?php echo BANNER_WIDTH;?>W x <?php echo BANNER_HEIGHT;?>H.</span><br /><br /><input type="file" name="upload" /><?php
		$path = get_image_path();
		if (is_file($path . $db->f("image_src"))) {
			echo "<p><img src=\"" . $path . $db->f("image_src") . "\" width=\"".$db->f("image_width")." height=\"".$db->f("image_height")."\" border=\"0\" alt=\"\" /><br/><a href=\"{$_SERVER['PHP_SELF']}?a=ric&id={$_REQUEST['id']}\">Remove image &raquo;</a></p>";
		}
		echo "<input type=\"hidden\" name=\"image_src\" value=\"".$db->f("image_src")."\" />";
		?></td></tr>
		<tr><td class="row_title">Message:</td><td class="row_value"><textarea name="message" class="msg"><?php echo (isset($_POST['message']) ? $_POST['message']:$db->f("message"));?></textarea></td></tr>
		<tr><td class="row_title">Date:</td><td valign="middle" class="row_value"><?php echo get_date_ctl("date_from", $db->f("date_from"));?>&nbsp;&nbsp;To&nbsp;&nbsp;<?php echo get_date_ctl("date_to", $db->f("date_to"));?></td></tr>
		<tr><td class="row_title">Time:</td><td valign="middle" class="row_value"><?php echo get_time_ctl("time_from", $db->f("time_from"));?>&nbsp;&nbsp;To&nbsp;&nbsp;<?php echo get_time_ctl("time_to", $db->f("time_to"));?></td></tr>
<?php
	}
	else {		
?>	
		<input type="hidden" name="id" value="">
		<tr><td class="row_title">Status:</td><td class="row_value"><input name="active" type="radio" value="1" checked='checked' /> On <input name="active" type="radio" value="0" /> Off</td></tr>
		<tr><td class="row_title" valign="top">Image:</td><td class="row_value"><span class="grey">Image format: GIF, JPG or JPEG, <?php echo BANNER_WIDTH;?>W x <?php echo BANNER_HEIGHT;?>H.</span><br /><br /><input type="file" name="upload" /></td></tr>
		<tr><td class="row_title">Message:</td><td class="row_value"><textarea name="message" class="msg"><?php echo $_POST['message'];?></textarea></td></tr>
		<tr><td class="row_title">Date:</td><td valign="middle" class="row_value"><?php echo get_date_ctl("date_from", 'now');?>&nbsp;&nbsp;To&nbsp;&nbsp;<?php echo get_date_ctl("date_to", 'now');?></td></tr>
		<tr><td class="row_title">Time:</td><td valign="middle" class="row_value"><?php echo get_time_ctl("time_from");?>&nbsp;&nbsp;To&nbsp;&nbsp;<?php echo get_time_ctl("time_to");?></td></tr>
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
		
		if (!empty($_FILES['upload']['name'])) {
			$path = get_image_path();
			$image_filename = "msg" . date("dmyHis") . substr($_FILES['upload']['name'], -4);
	
			$msg = upload_image ($path, $image_filename, BANNER_WIDTH, BANNER_HEIGHT);
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
		
		if (empty($_POST['date_from'])) {
			$msg = "Please complete at least the from date field";
		}
		else if (!validate_date($_POST['date_from'])) {
			$msg = "Please provide a valid from date, in the format ccyy-mm-dd.";
		}
		else if (!empty($_POST['date_to']) && !validate_date($_POST['date_to'])) {
			$msg = "Please provide a valid to date, in the format ccyy-mm-dd.";
		}
		else if (!empty($_POST['time_from']) && $_POST['time_from']!='hh:mm' && !validate_time($_POST['time_from'])) {
			$msg = "Please provide a valid from time, in the format hh:mm in 24 hour clock.";
		}
		else if (!empty($_POST['time_to']) && $_POST['time_to']!='hh:mm' && !validate_time($_POST['time_to'])) {
			$msg = "Please provide a valid to time, in the format hh:mm in 24 hour clock.";
		}
		else {
			if (empty($_POST['date_to'])) $date_to = "'{$_POST['date_from']}'";
			else $date_to = "'{$_POST['date_to']}'";
			if (empty($_POST['time_from']) || $_POST['time_from']=='hh:mm') $time_from = "null";
			else $time_from = "'{$_POST['time_from']}'";
			if (empty($_POST['time_to']) || $_POST['time_to']=='hh:mm') $time_to = "null";
			else $time_to = "'{$_POST['time_to']}'";
			
			if (!empty($_POST['id'])) {
				$sql = "UPDATE messages SET active={$_POST['active']}, date_from='{$_POST['date_from']}', date_to={$date_to}, time_from={$time_from}, time_to={$time_to}, message=\"".addslashes($_POST['message'])."\"{$upd}, modified_by=\"{$_SESSION['admin_name']}\", modified_date=NOW() WHERE id = " . $_POST['id'];
			}
			else {
				$msg_order = get_next_id('messages');
				$sql = "INSERT INTO messages (active, msg_order, date_from, date_to, time_from, time_to, message{$ins_f}, created_by, created_date, modified_by, modified_date) VALUES ({$_POST['active']}, {$msg_order}, '{$_POST['date_from']}', {$date_to}, {$time_from}, {$time_to}, \"".addslashes($_POST['message'])."\"{$ins_v}, \"{$_SESSION['admin_name']}\", NOW(), \"{$_SESSION['admin_name']}\", NOW())";		
			}
			$db->query($sql);
		}
		
		return $msg;
	}
	
	function remove_image() {
		$db = new db_mysql;
		$db->query("SELECT image_src FROM messages WHERE id = " . $_REQUEST['id']);
		$db->next_record();
	
		delete_image($db->f("image_src"));
		$db->query("UPDATE messages SET image_src='' WHERE id = " . $_REQUEST['id']);
	}
?>