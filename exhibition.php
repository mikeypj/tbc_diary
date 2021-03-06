<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		exhibition.php
//	Desc:		exhibition details page
//  Client:		BCD
//	Author:		Rob Curle
//	Date:		23 November 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////

	include("includes/startup.php");

	$db = new db_mysql;
	$back_url = "planner.php";

	if (!empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
		$db->query("SELECT * FROM exhibitions WHERE id = " . $_REQUEST['id']);
		$db->next_record();

		if ($db->num_rows() == 0) {
			redirect ($back_url);
		}
	}
	else {
		redirect ($back_url);
	}

	$locations = get_locations();
	$path = get_image_path();
	$has_thumb = is_file($path.$db->f("image_src"));

	$section = 3;
	include("includes/html_header.php");
?>

<table width="750" border="0" cellpadding="7" cellspacing="0">
<tr><td colspan="2" valign="middle" class="title_bar"><a href="<?php echo $back_url;?>"><img src="media/btns/back.gif" width="42" height="18" alt="" border="0" align="absmiddle" /></a><?php echo ($_SESSION['loc_rights'] ? "&nbsp;&nbsp;<a href=\"admin_exhibition.php?id={$_REQUEST['id']}&r=1\"><img src=\"media/btns/edit.gif\" alt=\"\" width=\"34\" height=\"18\" border=\"0\" align=\"absmiddle\" /></a>":"");?>&nbsp;&nbsp;<a href="print_view.php?type=exhibitions&id=<?php echo  $_REQUEST['id'] ;?>"><img src="media/btns/print.gif" alt="" width="38" height="18" border="0" align="absmiddle" /></a></td><tr>
<tr><td valign="top" class="content"<?php echo ($has_thumb ? "":" colspan='2'");?>><?php
	echo get_icons($db->f("status"));

	echo "<p><span class=\"title_txt\">" . $db->f("exhib_title") . "</span><span class=\"large_txt\"><br/>";
	echo "<span class=\"grey\">Date: </span>" . format_longdate($db->f("date_from")) ." - " . format_longdate($db->f("date_to"))  . "<br/>";
	echo "<span class=\"grey\">Organised by: </span>" . ($db->f("hosted_by")==OTHER_IDX ? $db->f("hosted_other"):$hosted_by[$db->f("hosted_by")]). "<br/>";
	echo "<span class=\"grey\">Location: </span>" . $locations[$db->f("location")];
	if ($db->f("location") == NLA_SEMINAR) {
		foreach ($nla_suite as $bit => $room) {
			if (query_bit($db->f("nla_suite"), $bit)) {
				echo " {$room}";
			}
		}
	}
	echo "</span></p>\n";
?>
</td><?php
	if ($has_thumb) {
			echo "<td class=\"content\" valign=\"top\" width=\"".IMG_WIDTH."\"><img src=\"{$path}".$db->f("image_src")."\" width=\"".IMG_WIDTH."\" height=\"".IMG_HEIGHT."\" border=\"0\" /></td>";
		}
?></tr>
<tr><td colspan="2" class="dark_grey">
	<table border="0" cellpadding="0" cellspacing="1">
	<tr><td>Event created:&nbsp;</td><td><?php echo last_modified($db->f("created_date"), $db->f("created_by"));?></td></tr>
	<tr><td>Event updated:&nbsp;</td><td><?php echo last_modified($db->f("modified_date"), $db->f("modified_by"));?></td></tr>
	</table>
</td></tr>
</table>

<?php
	include("includes/html_footer.php");
?>
