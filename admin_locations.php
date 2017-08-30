<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		admin_locations.php
//	Desc:		list of system room locaiotns, with delete function
//  Client:		BCD
//	Author:		Rob Curle
//	Date:		28 September 2006
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////

	include("includes/startup.php");
	$db = new db_mysql;

	if (!$_SESSION['admin_rights']) {
		msg("Access Denied", "Sorry you do not have access to this page.", HOME);
	}

	switch ($_GET['a']) {
		case "dc": question_msg ("Delete Location", "Are you sure you want to delete this Room location?", $_SERVER['PHP_SELF']."?id=".$_GET['id']."&a=di", $_SERVER['PHP_SELF']); break;
		case "di": delete_location($_GET['id']); break;
	}

	$section = 7;
	include("includes/html_header.php");
?>
	<table width="750" border="0" cellpadding="7" cellspacing="0">
	<tr><td colspan="2" valign="middle" class="title_bar">Room Locations</td><tr>
	<tr><td valign="top" class="content">

	<table border="0" cellpadding="4" cellspacing="1" class="frame">
	<tr><td align="right" class="row_title" colspan="3"><a href="admin_location.php"><img src="media/btns/new.gif" width="35" height="18" border="0" alt="Add a new Location"></a></td></tr>
	<tr class="row_title"><th align="left" nowrap="nowrap">Name</th><th width="100" align="left">Floor</th><th width="40">&nbsp;</th></tr>
<?php
	$db->query("SELECT * FROM locations ORDER BY name");
	if ($db->num_rows() > 0) {
		while ($db->next_record()) {
			echo "<tr><td class=\"row_value\"><a href=\"admin_location.php?id=".$db->f("id")."\">".$db->f("name")."</a></td><td class=\"row_value\">".$db->f("floor")."</td><td align=\"center\" class=\"row_value\"><a href=\"{$_SERVER['PHP_SELF']}?a=dc&id=".$db->f("id")."\">Delete</a></td></tr>\n";
		}
	}
	else {
		echo "<tr><td valign=\"middle\" align=\"center\" height=\"40\" class=\"row_value\" colspan=\"3\">No locations found.</td></tr>\n";
	}
?>
	</table>

	</td></tr>
	</table>

<?php
	include("includes/html_footer.php");

	//////////////////////////////////////////////////////////////////////////////////////////////////


	function delete_location($id) {
		$db = new db_mysql;

		if (!empty($id) && is_numeric($id)) {
			$db->query("SELECT COUNT(id) AS ev_num FROM events WHERE location={$id}");
			$db->next_record();
			$ev_num = $db->f("ev_num");

			$db->query("SELECT COUNT(id) AS ex_num FROM exhibitions WHERE location={$id}");
			$db->next_record();
			$ex_num = $db->f("ex_num");

			if (empty($ev_num) && empty($ex_num)) {
				$db->query("DELETE FROM locations WHERE id={$id}");
			}
			else {
				msg ("Delete Location", "Sorry, this location is still in use, and cannot be removed.", "admin_locations.php");
			}
		}
	}
?>
