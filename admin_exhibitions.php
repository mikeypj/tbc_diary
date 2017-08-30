<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		admin_exhibitions.php
//	Desc:		list of exhibitions - paged
//  Client:		BCD
//	Author:		Rob Curle
//	Date:		17 November 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////

include("includes/startup.php");
include("includes/admin_filters.php");

if (empty($_SESSION['loc_rights'])) {

	msg ("Exhibitions", "Sorry, you do not have access rights to this page.", HOME);
}

if (isset($_GET['o']) && is_numeric($_GET['o'])) $_SESSION['xoffset'] = $_GET['o'];

switch ($_REQUEST['a']) {	// process requests

	case "filter": set_filter(EXHIBITIONS); $_SESSION['xoffset']=0; break;
	case "clr": clear_filter(EXHIBITIONS); $_SESSION['xoffset']=0; break;
	case "o": order_recordset(EXHIBITIONS, 'exhib_fields'); break;
	case "dc": question_msg ("Delete Exhibition", "Are you sure you want to delete this Exhibition?", $_SERVER['PHP_SELF']."?id=".$_GET['id']."&a=dn", $_SERVER['PHP_SELF']); break;
	case "dn": delete_item($_GET['id']); break;
}

$db = new db_mysql;
$db->query("SELECT sort_col, sort_dir FROM sort_columns WHERE type=".EXHIBITIONS." AND user_id={$_SESSION['admin_id']}");

if ($db->num_rows() > 0) {

	$db->next_record();
	$sort_col = $db->f("sort_col");
	$sort_dir = $db->f("sort_dir");
} else {

	$sort_col = 5;
	$sort_dir = "ASC";
}
$sort_clause = "ORDER BY " . $exhib_fields['f'.$sort_col]." ".$sort_dir;

$filters = get_filter(EXHIBITIONS);
if ( $filters['current'] == null ) { $filters['current'] = 1 ; }
$where_clause =  build_where_clause($filters, 'exhib_fields');

$db->query("SELECT l.id, IF (l.info_screen, 'On', 'Off') AS info_scrn, l.exhib_title, l.hosted_by, l.hosted_other, l.location, l.nla_suite, l.date_from, l.date_to, l.created_by, IF (m.id is not null, 'Yes', 'No') AS msg, IF (l.visible, 'Yes', 'No') AS is_visible FROM exhibitions AS l LEFT OUTER JOIN messages m ON l.id=m.exhib_id $where_clause $sort_clause");

$users = get_users();
$locations = get_locations();

$section = 3;
include("includes/html_header.php");
?>

<table border="0" cellpadding="7" cellspacing="0">
<tr><td colspan="2" valign="middle" class="title_bar">Exhibitions</td><tr>
<tr><td valign="top" class="content">
<form name="userdata" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<input type="hidden" name="a" value="filter"/>
<input type="image" name="go" src="media/trans.gif" alt="" width="1" height="1" border="0" />

<table border="0" cellpadding="3" cellspacing="1" class="frame">
  <tr class="row_title">
    <td colspan="3"><strong>Show:</strong>
    <select name="current" size="1" onchange="submitform()" class="small_txt">
      <option value="1"<?php echo ($filters['current']==1 ? " selected='selected'":"");?>> Scheduled exhibitions </option>
      <option value="2"<?php echo ($filters['current']==2 ? " selected='selected'":"");?>> Past exhibitions </option>
    </select></td>
    <td colspan="5" align="right"></a>&nbsp;&nbsp;<a href="admin_exhibition.php"><img src="media/btns/addexhibition.gif" width="95" height="18" border="0" align="absmiddle" alt="Add new Exhibition" /></a></td>
  </tr>
<?php

	echo "<tr valign=\"bottom\" class=\"row_title\">
	<th ".($sort_col==1 ? "class='row_current'":"")."><a href=\"{$_SERVER['PHP_SELF']}?a=o&c=1\">ID</a></th>
	<th width=\"500\" ".($sort_col==2 ? "class='row_current'":"")."><a href=\"{$_SERVER['PHP_SELF']}?a=o&c=2\">Title</a></th>
	<th ".($sort_col==3 ? "class='row_current'":"")."><a href=\"{$_SERVER['PHP_SELF']}?a=o&c=3\">Organised by</a></th>
	<th ".($sort_col==4 ? "class='row_current'":"")."><a href=\"{$_SERVER['PHP_SELF']}?a=o&c=4\">Location</a></th>
	<th ".($sort_col==5 ? "class='row_current'":"")."><a href=\"{$_SERVER['PHP_SELF']}?a=o&c=5\">Date</a></th>
	<th ".($sort_col==6 ? "class='row_current'":"")."><a href=\"{$_SERVER['PHP_SELF']}?a=o&c=6\">Created by</a></th>
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
<td width="40"> </td>
<td><select name="f6" onchange='submitform()'><option value=""> All </option>
<?php
	foreach ($users as $txt) {
		echo "<option value=\"$txt\" " . ($txt==$filters['f6'] ? "selected='selected'":""). "> $txt </option>";
	}
?></select></td>
<td width="40"><a href="<?php echo $_SERVER['PHP_SELF']."?a=clr";?>">Reset</a></td></tr>
<?php
	if ($db->num_rows() > 0) {
		while ($db->next_record()) {
			echo "<tr class=\"row_value\"><td valign=\"top\">".format_id($db->f("id"))."</td><td valign=\"top\"><a href=\"admin_exhibition.php?id=".$db->f("id")."\">".$db->f("exhib_title")."</a></td><td valign=\"top\">".$hosted_by_abr[$db->f("hosted_by")]."</td><td valign=\"top\" nowrap='nowrap'>".$locations[$db->f("location")];
			if ($db->f("location") == NLA_SEMINAR) {
				foreach ($nla_suite as $bit => $room) {
					if (query_bit($db->f("nla_suite"), $bit)) {
						echo "&nbsp;{$room}";
					}
				}
			}
			echo "</td><td valign=\"top\" nowrap='nowrap'>" . format_date($db->f("date_from")).($db->f("date_to")!=null ? " to ".format_date($db->f("date_to")):"") . "</a></td><td valign=\"top\" nowrap='nowrap'>".$db->f("created_by")."</a></td><td valign=\"top\"><a href=\"" . $_SERVER['PHP_SELF'] . "?a=dc&id=" . $db->f("id") . "\">Delete</a></td></tr>\n";
		}
	}
	else {
		echo "<tr><td align=\"center\" colspan=\"10\" class=\"row_value\"><img src=\"media/misc/trans.gif\" alt=\"\" width=\"1\" height=\"30\" border=\"0\" /><br/>There were no Exhibitions found.<br /><img src=\"media/misc/trans.gif\" alt=\"\" width=\"1\" height=\"30\" border=\"0\" /></td></tr>";
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
			$db->query("DELETE FROM exhibitions WHERE id = {$id}");
			$db->query("DELETE FROM messages WHERE exhib_id = {$id}");
		}
	}
?>

