<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		event.php
//	Desc:		event details page
//  Client:		LAD
//	Author:		Rob Curle
//	Date:		23 November 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////

	include("includes/startup.php");

	$db = new db_mysql;
	$dbe = new db_mysql;
	$back_url = "planner.php";

	if (!empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
		$dbe->query("SELECT * FROM events WHERE id = " . $_REQUEST['id']);
		$dbe->next_record();

		if ($dbe->num_rows() == 0) {
			redirect ($back_url);
		}
	}
	else {
		redirect ($back_url);
	}

	$locations = get_locations();
	$path = get_image_path();
	$has_thumb = is_file($path.$dbe->f("image_src"));

	$section = 2;
	include("includes/html_header.php");
?>

<table width="750" border="0" cellpadding="7" cellspacing="0">
<tr><td colspan="2" valign="middle" class="title_bar"><a href="<?php echo $back_url;?>"><img src="media/btns/back.gif" width="42" height="18" alt="" border="0" align="absmiddle" /></a>&nbsp;&nbsp;<a href="print_view.php?type=events&id=<?php echo  $_REQUEST['id'] ;?>" target="_new"><img src="media/btns/print.gif" alt="" width="38" height="18" border="0" align="absmiddle" /></a><?php echo ($_SESSION['loc_rights'] ? "&nbsp;&nbsp;<a href=\"admin_event.php?id={$_REQUEST['id']}&r=1\"><img src=\"media/btns/edit.gif\" alt=\"\" width=\"34\" height=\"18\" border=\"0\" align=\"absmiddle\" /></a>":"");?><?php echo ($_SESSION['loc_rights'] ? "&nbsp;&nbsp;<a href=\"admin_events.php?id={$_REQUEST['id']}&a=dc\"><img src=\"media/btns/delete.gif\" alt=\"\" width=\"48\" height=\"18\" border=\"0\" align=\"absmiddle\" /></a>":"");?></td><tr>
<tr><td valign="top" class="content nav_icons"<?php echo ($has_thumb ? "":" colspan='2'");?>><?php
	echo get_icons($dbe->f("status"));

	echo "<p><span class=\"title_txt\">" . $dbe->f("event_title") . "</span><div class=\"large_txt\">";
	echo "<span class=\"grey\">Organised by: </span>" . ($dbe->f("hosted_by")==OTHER_IDX ? $dbe->f("hosted_other"):$hosted_by[$dbe->f("hosted_by")]). "<br/>";
	echo "<span class=\"grey\">Location: </span>" . $locations[$dbe->f("location")];
	if ($dbe->f("location") == NLA_SEMINAR) {
		foreach ($nla_suite as $bit => $room) {
			if (query_bit($dbe->f("nla_suite"), $bit)) {
				echo " {$room}";
			}
		}
	}
	echo "<br/>";
	echo "<span class=\"grey\">Date: </span>" . format_longdate($dbe->f("event_date")) . "<br/>";
	echo "<span class=\"grey\">Time: </span>" . format_time($dbe->f("time_from")) . " - " . format_time($dbe->f("time_to")) . "<br/>";

	if ($dbe->f("period_from")!= null && $dbe->f("period_to")!= null) {
		echo "<span class=\"grey\">Period of occupation: </span>" . format_time($dbe->f("period_from")) . " - " . format_time($dbe->f("period_to")) . "<br/>";
	}
	if ($dbe->f("num_people") > 0) {
		echo "<span class=\"grey\">Number of people: </span>" . $dbe->f("num_people");
	}
	echo "</div></p>\n";

	echo "</td>";
	if ($has_thumb) {
		echo "<td class=\"content\" valign=\"top\" width=\"".IMG_WIDTH."\"><img src=\"{$path}".$dbe->f("image_src")."\" width=\"".IMG_WIDTH."\" height=\"".IMG_HEIGHT."\" border=\"0\" /></td>";
	}
	echo "</tr>\n<tr><td class=\"content\" colspan=\"2\">";

	if ($dbe->f("notes") != "") {
		echo "<p><img src=\"media/line_gry1.gif\" width=\"100%\" height=\"1\"border=\"0\" /></p>\n";
		echo "<p><strong>General Notes</strong><br/>" . nl2br($dbe->f("notes")) . "</p>";
	}

	// contact details //
	$db->query("SELECT * FROM contacts WHERE event_id = {$_REQUEST['id']}");
	if ($db->num_rows() > 0) {
		$db->next_record();

		echo "<p><img src=\"media/line_gry1.gif\" width=\"100%\" height=\"1\"border=\"0\" /></p>\n";
		echo "<p><strong>Contact details</strong><br />\n";
		if ($db->f("contact_name")) echo "<span class='grey'>Name: </span>".$db->f("contact_name")."<br />\n";
		if ($db->f("tel")) echo "<span class='grey'>Tel: </span>".$db->f("tel")."<br />\n";
		if ($db->f("fax")) echo "<span class='grey'>Fax: </span>".$db->f("fax")."<br />\n";
		if ($db->f("email")) echo "<span class='grey'>Email: </span><a href=\"mailto:".$db->f("email")."\">".$db->f("email")."</a><br />\n";
		if ($db->f("company")) echo "<span class='grey'>Company name: </span>".$db->f("company")."<br />\n";
		if ($db->f("address_1")) echo "<span class='grey'>Address: </span>".$db->f("address_1")." ".$db->f("address_2")."<br />\n";
		if ($db->f("nla_partner")) echo "<span class='grey'>NLA partner: </span>Yes<br />\n";
		if ($db->f("contact_notes")) echo "<span class='grey'>Notes</span><br />".nl2br($db->f("contact_notes"))."<br />\n";
		echo "</p>\n";
	}

	// setup details //
	$db->query("SELECT * FROM setup WHERE event_id = {$_REQUEST['id']}");
	if ($db->num_rows() > 0) {
		$db->next_record();

		echo "<p><img src=\"media/line_gry1.gif\" width=\"100%\" height=\"1\"border=\"0\" /></p>\n";
		echo "<p><strong>Setup</strong><br />\n";
		if ($db->f("provided_by")) echo "<span class='provided_by_".$db->f("provided_by")."'>Provided by: ".$hosted_by_abr[$db->f("provided_by")]."</span><br />\n";
		if ($db->f("setup_time")) echo "<span class='grey'>Setup time: </span>".$setup_times[$db->f("setup_time")]."<br />\n";
		if ($db->f("layout")) echo "<span class='grey'>Layout: </span>".$db->f("layout")."<br />\n";
		if ($db->f("theme")) echo "<span class='grey'>Theme: </span>".$db->f("theme")."<br />\n";
		if ($db->f("setup_notes")) echo "<span class='grey'>Notes</span><br />".nl2br($db->f("setup_notes"))."<br />\n";
		echo "</p>\n";

	}

	// equipment details //
	$db->query("SELECT * FROM equipment WHERE event_id = {$_REQUEST['id']}");
	if ($db->num_rows() > 0) {
		$db->next_record();

		echo "<p><img src=\"media/line_gry1.gif\" width=\"100%\" height=\"1\"border=\"0\" /></p>\n";
		echo "<p><strong>Equipment</strong><br />\n";
		if ($db->f("provided_by")) echo "<span class='provided_by_".$db->f("provided_by")."'>Provided by: ".$hosted_by_abr[$db->f("provided_by")]."</span><br />\n";
		if ($db->f("equipment") > 0) {
			foreach ($equipment as $bit => $txt) {
				if (query_bit($db->f("equipment"), $bit)) {
					echo " - {$txt}<br />\n";
				}
			}
		}
		if ($db->f("equip_notes")) echo "<span class='grey'>Notes</span><br />".nl2br($db->f("equip_notes"))."<br />\n";
		echo "</p>\n";
	}

	// drinks details //
	$db->query("SELECT * FROM drinks WHERE event_id = {$_REQUEST['id']}");
	if ($db->num_rows() > 0) {
		$db->next_record();

		echo "<p><img src=\"media/line_gry1.gif\" width=\"100%\" height=\"1\"border=\"0\" /></p>\n";
		echo "<p><strong>Catering: Drinks</strong><br />\n";
		if ($db->f("provided_by")) echo "<span class='provided_by_".$db->f("provided_by")."'>Provided by: ".$hosted_by_abr[$db->f("provided_by")]."</span><br />\n";
		if ($db->f("coffee")) echo "<span class='grey'>Coffee: </span>".$db->f("coffee")."<br />\n";
		if ($db->f("tea")) echo "<span class='grey'>Tea: </span>".$db->f("tea")."<br />\n";
		if ($db->f("biscuits")) echo "<span class='grey'>Biscuits: </span>".$db->f("biscuits")."<br />\n";
		if ($db->f("orange")) echo "<span class='grey'>Orange juice: </span>".$db->f("orange")."<br />\n";
		if ($db->f("water")) echo "<span class='grey'>Water: </span>".$db->f("water")."<br />\n";
		if ($db->f("wine")) echo "<span class='grey'>Wine / Champagne: </span>".$db->f("wine")."<br />\n";
		if ($db->f("beer")) echo "<span class='grey'>Beer: </span>".$db->f("beer")."<br />\n";
		if ($db->f("drinks_cost")) echo "<span class='grey'>Cost: </span>&pound; ".number_format($db->f("drinks_cost"),2)."<br />\n";
		if ($db->f("drinks_notes")) echo "<span class='grey'>Notes</span><br />".nl2br($db->f("drinks_notes"))."<br />\n";
		echo "</p>\n";
	}

	// catering details //
	$db->query("SELECT * FROM catering WHERE event_id = {$_REQUEST['id']}");
	if ($db->num_rows() > 0) {
		$db->next_record();

		echo "<p><img src=\"media/line_gry1.gif\" width=\"100%\" height=\"1\"border=\"0\" /></p>\n";
		echo "<p><strong>Catering: Food</strong><br />\n";
		if ($db->f("provided_by")) echo "<span class='provided_by_".$db->f("provided_by")."'>Provided by: ".$hosted_by_abr[$db->f("provided_by")]."</span><br />\n";
		if ($db->f("menu_1")) echo "- Menu ".$db->f("menu_1")." for ".$db->f("people_1")." people at ".$db->f("time_1")."<br />\n";
		if ($db->f("menu_2")) echo "- Menu ".$db->f("menu_2")." for ".$db->f("people_2")." people at ".$db->f("time_2")."<br />\n";
		if ($db->f("person_cost")) echo "<span class='grey'>Cost per person: </span>&pound; ".number_format($db->f("person_cost"),2)."<br />\n";
		if ($db->f("catering_cost")) echo "<span class='grey'>Total cost: </span>&pound; ".number_format($db->f("catering_cost"),2)."<br />\n";
		if ($db->f("catering_notes")) echo "<span class='grey'>Notes</span><br />".nl2br($db->f("catering_notes"))."<br />\n";
		echo "</p>\n";
	}

	// staff details //
	$db->query("SELECT * FROM staff WHERE event_id = {$_REQUEST['id']}");
	if ($db->num_rows() > 0) {
		$db->next_record();

		echo "<p><img src=\"media/line_gry1.gif\" width=\"100%\" height=\"1\"border=\"0\" /></p>\n";
		echo "<p><strong>Catering: Staff</strong><br />\n";
		if ($db->f("provided_by")) echo "<span class='provided_by_".$db->f("provided_by")."'>Provided by: ".$hosted_by_abr[$db->f("provided_by")]."</span><br />\n";
		if ($db->f("reg_attendants")) echo "<span class='grey'>Registration attendants: </span>".$db->f("reg_attendants")."<br />\n";
		if ($db->f("food_waiters")) echo "<span class='grey'>Food waiters: </span>".$db->f("food_waiters")."<br />\n";
		if ($db->f("drinks_waiters")) echo "<span class='grey'>Drinks waiters: </span>".$db->f("drinks_waiters")."<br />\n";
		if ($db->f("cloak_attendants")) echo "<span class='grey'>Cloakroom attendants: </span>".$db->f("cloak_attendants")."<br />\n";
		if ($db->f("staff_cost")) echo "<span class='grey'>Cost: </span>&pound; ".number_format($db->f("staff_cost"),2)."<br />\n";
		if ($db->f("staff_notes")) echo "<span class='grey'>Notes</span><br />".nl2br($db->f("staff_notes"))."<br />\n";
		echo "</p>\n";
	}

	// security details //
	$db->query("SELECT * FROM security WHERE event_id = {$_REQUEST['id']}");
	if ($db->num_rows() > 0) {
		$db->next_record();

		echo "<p><img src=\"media/line_gry1.gif\" width=\"100%\" height=\"1\"border=\"0\" /></p>\n";
		echo "<p><strong>Security</strong><br />\n";
		if ($db->f("provided_by")) echo "<span class='provided_by_".$db->f("provided_by")."'>Provided by: ".$hosted_by_abr[$db->f("provided_by")]."</span><br />\n";
		if ($db->f("officers")) echo "<span class='grey'>Officers: </span>".$db->f("officers")."<br />\n";
		if ($db->f("security_cost")) echo "<span class='grey'>Cost: </span>&pound; ".number_format($db->f("security_cost"), 2)."<br />\n";
		if ($db->f("security_notes")) echo "<span class='grey'>Notes</span><br />".nl2br($db->f("security_notes"))."<br />\n";
		echo "</p>\n";
	}

	// payments details //
	if (!isset($_REQUEST['hp'])) {
		$db->query("SELECT *, IF (paid, 'Yes', 'No') AS paid_ans FROM payments WHERE event_id = {$_REQUEST['id']}");
		if ($db->num_rows() > 0) {
			echo "<p><img src=\"media/line_gry1.gif\" width=\"100%\" height=\"1\"border=\"0\" /></p>\n";
			echo "<p><strong>Payment</strong> <span class='grey'>[<a href=\"{$_SERVER['PHP_SELF']}?id={$_REQUEST['id']}&hp=1\">Hide</a>]</span><br />\n";

			while ($db->next_record()) {
				echo "<span class='grey'>Service:</span>".$db->f("service")."<span class='grey'> | Inv. by:</span>".$db->f("inv_by")." |</span> &pound; ".number_format($db->f("cost"),2)."<span class='grey'> | Paid:</span>".$db->f("paid_ans")."<span class='grey'> | Date:</span>".format_date($db->f("date"))."<br />\n";
			}

			echo "</p>\n";
		}
	}
?>
</td></tr>
<tr><td colspan="2" class="dark_grey">
	<table border="0" cellpadding="0" cellspacing="1">
	<tr><td>Event created:&nbsp;</td><td><?php echo last_modified($dbe->f("created_date"), $dbe->f("created_by"));?></td></tr>
	<tr><td>Event updated:&nbsp;</td><td><?php echo last_modified($dbe->f("modified_date"), $dbe->f("modified_by"));?></td></tr>
	</table>
</td></tr>
</table>

<?php
	include("includes/html_footer.php");
?>
