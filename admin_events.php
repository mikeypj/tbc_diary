<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		admin_events.php
//	Desc:		list of events - paged
//  Client:		BCD
//	Author:		Rob Curle
//	Date:		13 June 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////

	include("includes/startup.php");
	include("includes/admin_filters.php");

	if (empty($_SESSION['loc_rights'])) {
		msg ("Events", "Sorry, you do not have access rights to this page.", HOME);
	}

	if (isset($_GET['o']) && is_numeric($_GET['o'])) $_SESSION['offset'] = $_GET['o'];

	switch ($_REQUEST['a']) {	// process requests
		case "filter": set_filter(EVENTS); $_SESSION['offset']=0; break;
		case "clr": clear_filter(EVENTS); $_SESSION['offset']=0; break;
		case "o": order_recordset(EVENTS, 'event_fields'); break;
		case "dc": question_msg ("Delete Event", "Are you sure you want to delete this Event?", $_SERVER['PHP_SELF']."?id=".$_GET['id']."&a=dn", $_SERVER['PHP_SELF']); break;
		case "dn": delete_item($_GET['id']); break;
		case "cp": question_msg ("Copy Event", "Are you sure you want to copy this Event?", $_SERVER['PHP_SELF']."?id=".$_GET['id']."&a=cpn", $_SERVER['PHP_SELF']); break;
		case "cpn": copy_item($_GET['id']); break;
	}


	$db = new db_mysql;

	$db->query("SELECT sort_col, sort_dir FROM sort_columns WHERE type=".EVENTS." AND user_id={$_SESSION['admin_id']}");
	if ($db->num_rows() > 0) {
		$db->next_record();
		$sort_col = $db->f("sort_col");
		$sort_dir = $db->f("sort_dir");
	}
	else {
		$sort_col = 5;
		$sort_dir = "ASC";
	}
	$sort_clause = "ORDER BY " . $event_fields['f'.$sort_col]." ".$sort_dir;

	$filters = get_filter(EVENTS);
	if ( $filters['current_event'] == null ) { $filters['current_event'] = 1 ; }
	$where_clause =  build_where_clause($filters, 'event_fields');

	$sql = "SELECT l.id, l.event_title, l.hosted_by,  l.location, l.nla_suite, l.event_date, l.time_from, l.time_to, l.created_by, IF (l.info_screen, 'On', 'Off') AS info_scrn, IF (m.id is not null, 'Yes', 'No') AS msg, IF (l.visible, 'Yes', 'No') AS is_visible, ";
	$sql .= "staff.provided_by AS staff_by, ";
	$sql .= "setup.provided_by AS setup_by, ";
	$sql .= "security.provided_by AS security_by, ";
	$sql .= "equipment.provided_by AS equip_by, ";
	$sql .= "drinks.provided_by AS drinks_by, ";
	$sql .= "catering.provided_by AS catering_by ";
	$sql .= "FROM events AS l LEFT OUTER JOIN messages m ON l.id=m.event_id ";
	$sql .= "LEFT OUTER JOIN catering ON l.id=catering.event_id ";
	$sql .= "LEFT OUTER JOIN drinks ON l.id=drinks.event_id ";
	$sql .= "LEFT OUTER JOIN equipment ON l.id=equipment.event_id ";
	$sql .= "LEFT OUTER JOIN security ON l.id=security.event_id ";
	$sql .= "LEFT OUTER JOIN setup ON l.id=setup.event_id ";
	$sql .= "LEFT OUTER JOIN staff ON l.id=staff.event_id ";
	$sql .= " $where_clause $sort_clause ";

	$db->query($sql);

	$users = get_users();
	$locations = get_locations();

	$section = 2;
	include("includes/html_header.php");
?>

<table border="0" cellpadding="7" cellspacing="0">
<tr><td colspan="2" valign="middle" class="title_bar">Events</td><tr>
<tr><td valign="top" class="content">
<form name="userdata" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<input type="hidden" name="a" value="filter"/>
<input type="image" name="go" src="media/trans.gif" alt="" />

<table border="0" cellpadding="3" cellspacing="1" class="frame">
<tr class="row_title">
  <td colspan="3"><strong>Show:</strong>
  <select name="current_event" size="1" onchange="submitform()" class="small_txt">
    <option value="1"<?php echo ($filters['current_event']==1? " selected='selected'":"");?>> Scheduled events </option>
    <option value="2"<?php echo ($filters['current_event']==2 ? " selected='selected'":"");?>> Past events </option>
  </select></td>
  <td colspan="6" align="right"><a href="admin_event.php"><img src="media/btns/addevent.gif" width="70" height="18" border="0" align="absmiddle" alt="Add new Event" /></a></td>
  </tr>
<?php
//	$page_nav = get_paged_nav(PAGE_ITEMS, $num_rows, $_SESSION['offset']);
//	if (!empty($page_nav)) {
//		echo "<tr><td valign=\"middle\" class=\"row_title\" colspan=\"13\">" . $page_nav . "</td></tr>\n";
//	}
	echo "<tr valign=\"bottom\" class=\"row_title\">
	  <th ".($sort_col==1 ? "class='row_current'":"")."><a href=\"{$_SERVER['PHP_SELF']}?a=o&c=1\">ID</a></th>
	  <th ".($sort_col==2 ? "class='row_current'":"")."><a href=\"{$_SERVER['PHP_SELF']}?a=o&c=2\">Title</a></th>
	  <th ".($sort_col==3 ? "class='row_current'":"")."><a href=\"{$_SERVER['PHP_SELF']}?a=o&c=3\">Organised by</a></th>
	  <th ".($sort_col==4 ? "class='row_current'":"")."><a href=\"{$_SERVER['PHP_SELF']}?a=o&c=4\">Location</a></th>
	  <th ".($sort_col==5 ? "class='row_current'":"")."><a href=\"{$_SERVER['PHP_SELF']}?a=o&c=5\">Date</a></th>
	  <th ".($sort_col==6 ? "class='row_current'":"")."><a href=\"{$_SERVER['PHP_SELF']}?a=o&c=6\">Time</a></th>
	  <th ".($sort_col==7 ? "class='row_current'":"")."><a href=\"{$_SERVER['PHP_SELF']}?a=o&c=7\">Created by</a></th>
	  <th> </th>
	  <th> </th>
	  </tr>\n";
?>
<tr valign="top" class="row_value">
<td width="40"> </td>
<td width="500"><input name="f2" type="text" size="15" maxlength="40" value="<?php echo $filters['f2'];?>" /></td>
<td><select name="f3" onchange='submitform()'><option value=""> All </option>
<?php
	foreach ($hosted_by_abr as $id => $txt) {
		echo "<option value=\"$id\" " . ($id==$filters['f3'] ? "selected='selected'":""). "> $txt </option>";
	}
?></select></td>
<td><select name="f4" onchange='submitform()'><option value=""> All </option>
<?php
	foreach ($locations as $id => $txt) {
		echo "<option value=\"$id\" " . ($id==$filters['f4'] ? "selected='selected'":""). "> $txt </option>";
	}
?></select></td>
<td width="50"> </td>
<td width="40"> </td>
<td><select name="f7" onchange='submitform()'><option value=""> All </option>
<?php
	foreach ($users as $txt) {
		echo "<option value=\"$txt\" " . ($txt==$filters['f7'] ? "selected='selected'":""). "> $txt </option>";
	}
?></select></td>
<td width="40"> </td>
<td width="40"><a href="<?php echo $_SERVER['PHP_SELF']."?a=clr";?>">Reset</a></td></tr>
<?php
	if ($db->num_rows() > 0) {
		while ($db->next_record()) {
			echo "<tr class=\"row_value\"><td valign=\"top\">".format_id($db->f("id"))."</td><td valign=\"top\"><a href=\"admin_event.php?id=".$db->f("id")."\">".$db->f("event_title")."</a></td><td valign=\"top\">".$hosted_by_abr[$db->f("hosted_by")]."</td><td valign=\"top\">".$locations[$db->f("location")];
			if ($db->f("location") == NLA_SEMINAR) {
				foreach ($nla_suite as $bit => $room) {
					if (query_bit($db->f("nla_suite"), $bit)) {
						echo "&nbsp;{$room}";
					}
				}
			}
			echo "</td><td valign=\"top\" nowrap='nowrap'>".format_date($db->f("event_date"))."</td>
			<td valign=\"top\" align=\"right\" nowrap='nowrap'>".format_time24($db->f("time_from")).($db->f("time_to")!=null ? " to ".format_time24($db->f("time_to")):"") ."</td>
			<td valign=\"top\" nowrap='nowrap'>".$db->f("created_by")."</a></td><td valign=\"top\"><a href=\"".$_SERVER['PHP_SELF']."?a=cp&id=".$db->f("id")."\">Copy</a></td><td valign=\"top\"><a href=\"".$_SERVER['PHP_SELF']."?a=dc&id=".$db->f("id")."\">Delete</a></td></tr>\n";
		}
	}
	else {
		echo "<tr><td align=\"center\" colspan=\"13\" class=\"row_value\"><img src=\"media/misc/trans.gif\" alt=\"\" width=\"1\" height=\"30\" border=\"0\" /><br/>There were no Events found.<br /><img src=\"media/misc/trans.gif\" alt=\"\" width=\"1\" height=\"30\" border=\"0\" /></td></tr>";
	}
?>
</table>
</form>
</td></tr>
</table>

<?php
	include("includes/html_footer.php");

//////////////////////////////////////////////////////////////////////////////////////////////////

	function delete_item($id) {
		$db = new db_mysql;

		if (!empty($id) && is_numeric($id)) {
			$db->query("DELETE FROM events WHERE id = {$id}");
			$db->query("DELETE FROM messages WHERE event_id = {$id}");
			$db->query("DELETE FROM contacts WHERE event_id = {$id}");
			$db->query("DELETE FROM setup WHERE event_id = {$id}");
			$db->query("DELETE FROM equipment WHERE event_id = {$id}");
			$db->query("DELETE FROM drinks WHERE event_id = {$id}");
			$db->query("DELETE FROM catering WHERE event_id = {$id}");
			$db->query("DELETE FROM staff WHERE event_id = {$id}");
			$db->query("DELETE FROM security WHERE event_id = {$id}");
			$db->query("DELETE FROM payments WHERE event_id = {$id}");
			$_SESSION['offset'] = 0;
		}
	}

	function copy_item($id) {
		$db = new db_mysql;

		if (!empty($id) && is_numeric($id)) {
			$db->query("INSERT INTO events (info_screen, visible, status, event_title, hosted_by, hosted_other, location, event_date, period_from, period_to, time_from, time_to, num_people, notes, equipment, catering, security, image_src, total_cost, created_by, created_date, modified_by, modified_date) SELECT info_screen, visible, status, event_title, hosted_by, hosted_other, location, event_date, period_from, period_to, time_from, time_to, num_people, notes, equipment, catering, security, image_src, total_cost, \"{$_SESSION['admin_name']}\", NOW(), \"{$_SESSION['admin_name']}\", NOW() FROM events WHERE id = {$id}");
			$event_id = $db->insert_id();

			$db->query("INSERT INTO messages (active, event_id, date_from, date_to, time_from, time_to, message, created_by, created_date, modified_by, modified_date) SELECT active, {$event_id}, date_from, date_to, time_from, time_to, message, \"{$_SESSION['admin_name']}\", NOW(), \"{$_SESSION['admin_name']}\", NOW() FROM messages WHERE event_id = {$id}");
			$db->query("INSERT INTO contacts (event_id, contact_name, tel, fax, email, company, address_1, address_2, nla_partner, contact_notes) SELECT {$event_id}, contact_name, tel, fax, email, company, address_1, address_2, nla_partner, contact_notes FROM contacts WHERE event_id = {$id}");
			$db->query("INSERT INTO setup (event_id, provided_by, setup_time, layout, theme, setup_notes) SELECT {$event_id}, provided_by, setup_time, layout, theme, setup_notes FROM setup WHERE event_id = {$id}");
			$db->query("INSERT INTO equipment (event_id, provided_by, equipment, equip_notes) SELECT {$event_id}, provided_by, equipment, equip_notes FROM equipment WHERE event_id = {$id}");
			$db->query("INSERT INTO drinks (event_id, provided_by, coffee, tea, biscuits, orange, water, wine, beer, drinks_cost, drinks_notes) SELECT {$event_id}, provided_by, coffee, tea, biscuits, orange, water, wine, beer, drinks_cost, drinks_notes FROM drinks WHERE event_id = {$id}");
			$db->query("INSERT INTO catering (event_id, provided_by, menu_1, people_1, time_1, menu_2, people_2, time_2, person_cost, catering_cost, catering_notes) SELECT {$event_id}, provided_by, menu_1, people_1, time_1, menu_2, people_2, time_2, person_cost, catering_cost, catering_notes FROM catering WHERE event_id = {$id}");
			$db->query("INSERT INTO staff (event_id, provided_by, reg_attendants, food_waiters, drinks_waiters, cloak_attendants, staff_cost, staff_notes)  SELECT {$event_id}, provided_by, reg_attendants, food_waiters, drinks_waiters, cloak_attendants, staff_cost, staff_notes FROM staff WHERE event_id = {$id}");
			$db->query("INSERT INTO security (event_id, provided_by, officers, security_cost, security_notes)  SELECT {$event_id}, provided_by, officers, security_cost, security_notes FROM security WHERE event_id = {$id}");
			$db->query("INSERT INTO payments (event_id, service, inv_by, cost, paid, date)  SELECT {$event_id}, service, inv_by, cost, paid, date FROM payments WHERE event_id = {$id}");

			redirect ("admin_event.php?id={$event_id}");
		}

	}
?>