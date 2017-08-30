<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		admin_messages.php
//	Desc:		list of messages - paged
//  Client:		BCD
//	Author:		Rob Curle
//	Date:		17 November 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////

	include("includes/startup.php");
	include("includes/admin_filters.php");

	
	if (!$_SESSION['msg_rights']) {
		msg("Access Denied", "Sorry you do not have access to this page.", HOME);
	}
	
	if (isset($_GET['o']) && is_numeric($_GET['o'])) $_SESSION['moffset'] = $_GET['o'];
	
	switch ($_REQUEST['a']) {	// process requests
		case "filter": set_filter(MESSAGES); $_SESSION['moffset']=0; break;
		case "clr": clear_filter(MESSAGES); $_SESSION['moffset']=0; break;
		case "o": swap_order(); break;
		case "dc": question_msg ("Delete Message", "Are you sure you want to delete this Message?", $_SERVER['PHP_SELF']."?id=".$_GET['id']."&a=dn", $_SERVER['PHP_SELF']); break;
		case "dn": delete_item($_GET['id']); break;
	}
	
	
	$db = new db_mysql;
	
	$filters = get_filter(MESSAGES);
	$where_clause =  build_where_clause($filters, 'msg_fields');
	
	$db->query("SELECT COUNT(l.id) AS Num_rows FROM messages AS l $where_clause"); 
	$db->next_record();
	$num_rows = $db->f("Num_rows");
	
	$db->query("SELECT l.id, l.message, IF (l.event_id>0, 'Event', IF (l.exhib_id>0, 'Exhibition', '')) AS attached_to, IF (l.active, 'On','Off') AS active, l.date_from, l.date_to, l.time_from, l.time_to, l.msg_order FROM messages AS l $where_clause ORDER BY msg_order DESC LIMIT {$_SESSION['moffset']}, " . PAGE_ITEMS);

	$section = 4;
	include("includes/html_header.php");
?>

<table width="750" border="0" cellpadding="7" cellspacing="0">
<tr><td colspan="2" valign="middle" class="title_bar">Messages</td><tr>
<tr><td width="750" valign="top" class="content">
<form name="userdata" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<input type="hidden" name="a" value="filter"/>
<input type="image" name="go" src="media/trans.gif" alt="" width="1" height="1" border="0" />

<table border="0" cellpadding="3" cellspacing="1" class="frame">
<tr class="row_title"><td colspan="2"><strong>Show:</strong> <select name="current" size="1" onchange="submitform()" class="small_txt"><option value=""> All messages </option><option value="1"<?php echo ($filters['current']==1? " selected='selected'":"");?>> Scheduled messages </option><option value="2"<?php echo ($filters['current']==2 ? " selected='selected'":"");?>> Past messages </option></select></td><td colspan="6" align="right"><a href="admin_message.php"><img src="media/btns/addmessage.gif" width="91" height="18" border="0" align="absmiddle" alt="Add new Message" /></a></td></tr>
<?php
	$page_nav = get_paged_nav(PAGE_ITEMS, $num_rows, $_SESSION['moffset']);
	if (!empty($page_nav)) {
		echo "<tr><td valign=\"middle\" class=\"row_title\" colspan=\"8\">{$page_nav}</td></tr>\n";
	}
	echo "<tr valign=\"bottom\" class=\"row_title\"><th width=\"40\">ID</th><th width=\"200\">Text</th><th width=\"60\">Attached to</th><th width=\"40\">Status</th><th width=\"80\">Date</th><th width=\"80\">Time</th><th>Set<br />Order</th><th width=\"40\"> </th></tr>\n";

	if ($db->num_rows() > 0) {
		while ($db->next_record()) {
			echo "<tr class=\"row_value\"><td valign=\"top\">".format_id($db->f("id"))."</td><td valign=\"top\"><a href=\"admin_message.php?id=".$db->f("id")."\">".($db->f("message") ? summary($db->f("message"), 30):"View this message")."</a></td><td valign=\"top\">".$db->f("attached_to")."</td><td valign=\"top\">".$db->f("active")."</td><td valign=\"top\" nowrap='nowrap'>" . format_date($db->f("date_from")) . ($db->f("date_from")!=$db->f("date_to") ? " to ".format_date($db->f("date_to")):"") . "</td>";
			echo "<td valign=\"top\" nowrap='nowrap'>" . ($db->f("time_from")!=null && $db->f("time_to")!=null ? format_time24($db->f("time_from"))." to ".format_time24($db->f("time_to")):"All Day") . "</a></td><td align=\"center\">";
			if ($db->f("attached_to") == "") {
				echo "<a href=\"{$_SERVER['PHP_SELF']}?a=o&d=u&mid=".$db->f("id")."&s=".$db->f("msg_order")."\"><img src=\"media/btns/arrowup.gif\" border=\"0\" /></a>&nbsp;&nbsp;<a href=\"{$_SERVER['PHP_SELF']}?a=o&d=d&mid=".$db->f("id")."&s=".$db->f("msg_order")."\"><img src=\"media/btns/arrowdown.gif\" border=\"0\" /></a>";
			}
			echo "</td><td valign=\"top\"><a href=\"{$_SERVER['PHP_SELF']}?a=dc&id=".$db->f("id")."\">Delete</a></td></tr>\n";		
		}
	}
	else {
		echo "<tr><td align=\"center\" colspan=\"8\" class=\"row_value\"><img src=\"media/misc/trans.gif\" alt=\"\" width=\"1\" height=\"30\" border=\"0\" /><br/>There were no Message found.<br /><img src=\"media/misc/trans.gif\" alt=\"\" width=\"1\" height=\"30\" border=\"0\" /></td></tr>";
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
			$db->query("DELETE FROM messages WHERE id = {$id}");
			$_SESSION['moffset'] = 0;
		}	
	}
	
	function swap_order() {
		$db = new db_mysql;
		
		$filters = get_filter(MESSAGES);
		$where_clause =  build_where_clause($filters, 'msg_fields');
		if (!empty($where_clause)) {
			$where_clause = substr($where_clause, 5) . " AND ";	// remove WHERE
		}
		
		$db->query("SELECT l.id, l.msg_order FROM messages AS l WHERE {$where_clause} l.msg_order>0 AND l.msg_order ".($_REQUEST['d']=='u' ? ">":"<")." {$_REQUEST['s']} ORDER BY l.msg_order ".($_REQUEST['d']=='u' ? "ASC":"DESC")." LIMIT 1");
		$db->next_record();
		if ($db->num_rows() > 0) {
			$id = $db->f("id");
			$msg_order = $db->f("msg_order");
			
			$db->query("UPDATE messages set msg_order = {$_REQUEST['s']} WHERE id = {$id}");
			$db->query("UPDATE messages set msg_order = {$msg_order} WHERE id = {$_REQUEST['mid']}");
		}
	}
?>