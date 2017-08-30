<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		admin_location.php
//	Desc:		add/edit a location
//  Client:		BCD
//	Author:		Rob Curle
//	Date:		28 September 2006
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////

	include("includes/startup.php");
	$db = new db_mysql;
	$back_url = "admin_locations.php";
		
	if (!$_SESSION['admin_rights']) {
		msg("Access Denied", "Sorry you do not have access to this page.", HOME);
	}
	
	if ($_POST['a'] == "save") {
		$msg = save_item();
		if (empty($msg)) {
			msg("Locations Admin", "This Location has now been saved.", $back_url);
		}		
	}	
	
	$page_title = "New Location";
	if (!empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
		$db->query("SELECT * FROM locations WHERE id=" . $_REQUEST['id']);
		$db->next_record();
		$page_title = "Edit Location";
	}
	
	$section = 7;
	include("includes/html_header.php");
?>
	<table width="750" border="0" cellpadding="7" cellspacing="0">
	<tr><td colspan="2" valign="middle" class="title_bar">Room Locations</td><tr>
	<tr><td width="750" valign="top" class="content">
	
	<form name="userdata" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
	<input type="image" name="go" src="media/trans.gif" alt="" width="1" height="1" border="0" />
	<input type="hidden" name="a" value="save" />
	<input type="hidden" name="id" value="<?php echo $db->f("id");?>" />
	
<?php
	if (!empty($msg)) {
		echo "<p class=\"error\">$msg</p>\n";
	}
?>
	<table border="0" cellpadding="3" cellspacing="1" class="frame">
	<tr><td colspan="2" class="yellow" align="right"><input type="image" name="save" src="media/btns/save.gif" width="38" height="18" border="0" alt="Save Record" align="absmiddle" />&nbsp;&nbsp;<a href="<?php echo $back_url;?>"><img src="media/btns/cancel.gif" width="51" height="18" border="0" alt="Cancel Changes" align="absmiddle" /></a></td></tr>
<?php
	if ($db->num_rows() > 0) {
?>
	<tr><td class="row_title">Last Updated:</td><td width="200" class="row_title"><?php echo last_modified($db->f("modified_date"), $db->f("modified_by"));?></td></tr>
<?php
	}
?>
	<tr><td class="row_title">Location Name:</td><td class="row_value"><input type="text" name="location" size="45" maxlength="150" value="<?php echo ($_POST['location'] ? $_POST['location']:$db->f("name"));?>" /></td></tr>
	<tr><td class="row_title">Floor:</td><td class="row_value"><input type="text" name="floor" size="45" maxlength="50" value="<?php echo ($_POST['floor'] ? $_POST['floor']:$db->f("floor"));?>" /></td></tr>
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
		
		if (empty($_POST['location']) || empty($_POST['floor'])) {
			$msg = "Please comlpete both fields.";
		}
		else {
			if (!empty($_POST['id'])) {
				$db->query("UPDATE locations SET name=\"" . addslashes($_POST['location']) . "\", floor=\"" . addslashes($_POST['floor']) . "\", modified_by=\"{$_SESSION['admin_name']}\", modified_date=NOW() WHERE id = {$_POST['id']}");
			}
			else {
				$db->query("INSERT INTO locations (name, floor, modified_by, modified_date) VALUES (\"".addslashes($_POST['location'])."\", \"".addslashes($_POST['floor'])."\", \"{$_SESSION['admin_name']}\", NOW())");
			}
		}
		 
		return $msg;
	}
?>