<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		day_events.php
//	Desc:		query db, and display a days events
//  Client:		LAD
//	Author:		Rob Curle
//	Date:		13 June 2005
//	Notes:		expects:  $day_date for event day, and $where_clause for record filters
//
///////////////////////////////////////////////////////////////////////////////////////

	$db_ev = new db_mysql;
	$no_exhibitions = false;
	
	$db_ev->query("SELECT e.id, e.status, e.hosted_by, e.hosted_other, e.date_from, e.date_to, e.exhib_title, e.location, e.nla_suite, e.image_src FROM exhibitions AS e {$joins} WHERE e.visible=1 AND {$where_clause} (('{$day_date}' BETWEEN e.date_from AND e.date_to) OR (e.date_from = '{$day_date}')) ORDER BY e.date_from, e.exhib_title");	
	if ($show_date) {
		echo "<tr><td colspan=\"2\" class=\"dark_grey\"><strong>" . format_longdate($day_date) . "</strong></td></tr>\n";
		echo "<tr><td colspan=\"2\"> </td></tr>\n";
	}
	
	if (!$show_date || $db_ev->num_rows() > 0) {
		echo "<tr><td colspan=\"2\" class=\"dark_grey\"><strong>Exhibitions</strong></td></tr>\n";
	}
	
	if ($db_ev->num_rows() > 0) {
		$show_line = false;
		while ($db_ev->next_record()) {								
			if ($show_line) {
				echo "<tr><td colspan=\"2\"><img src=\"media/line_gry1.gif\" width=\"100%\" height=\"1\"border=\"0\" /></td></tr>";
			}
			
			$hosted_by = ($db_ev->f("hosted_by")==OTHER_IDX ? $db_ev->f("hosted_other"):$hosted_by_abr[$db_ev->f("hosted_by")]);
			echo show_exhibition($db_ev->f("id"), $db_ev->f("status"), $hosted_by, $db_ev->f("date_from"), $db_ev->f("date_to"), $db_ev->f("location"), $db_ev->f("nla_suite"), $db_ev->f("exhib_title"), $db_ev->f("image_src")); 			
			$show_line = true;
		}
	}
	else {
		if (!$show_date) {
			echo "<tr><td colspan=\"2\">There are no exhibitions scheduled for this day.</td></tr>";
		}
		$no_exhibitions = true;
	}
	

	$db_ev->query("SELECT e.id, e.status, e.hosted_by, e.hosted_other, e.time_from, e.time_to, e.event_title, e.location, e.nla_suite, e.num_people, e.image_src FROM events AS e {$joins} WHERE e.visible=1 AND {$where_clause} e.event_date='{$day_date}' ORDER BY e.time_from, e.event_title");	
	if (!$show_date || $db_ev->num_rows() > 0) {
		echo "<tr><td colspan=\"2\" class=\"dark_grey\"><strong>Events</strong></td></tr>\n";
	}
	if ($show_date && $no_exhibitions) {
		echo "<tr><td colspan=\"2\"> </td></tr>\n";
	}
	
	if ($db_ev->num_rows() > 0) {
		$show_line = false;
		while ($db_ev->next_record()) {								
			if ($show_line) {
				echo "<tr><td colspan=\"2\"><img src=\"media/line_gry1.gif\" width=\"100%\" height=\"1\"border=\"0\" /></td></tr>";
			}
			
			$hosted_by = ($db_ev->f("hosted_by")==OTHER_IDX ? $db_ev->f("hosted_other"):$hosted_by_abr[$db_ev->f("hosted_by")]);
			echo show_event($db_ev->f("id"), $db_ev->f("status"), $hosted_by, $db_ev->f("time_from"), $db_ev->f("time_to"), $db_ev->f("location"), $db_ev->f("nla_suite"), $db_ev->f("event_title"), $db_ev->f("num_people"), $db_ev->f("image_src")); 			
			$show_line = true;
		}
	}
	else {
		if (!$show_date) {
			echo "<tr><td colspan=\"2\">There are no events scheduled for this day.</td></tr>";
		}
		else if ($no_exhibitions) {
			echo "<tr><td colspan=\"2\">&nbsp;</td></tr>";
		}
	}
?>