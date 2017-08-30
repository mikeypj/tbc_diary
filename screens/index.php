<?php
// +------------------------------------------------------------+
// | Copyright (c) 2008 Mark Yasuda @ Manha                     |
// +------------------------------------------------------------+
// | THIS CODE IS PROTECTED BY COPYRIGHT LAW.                   |
// |                                                            |
// | Unauthorised re-use of this code is a breach of copyright. |
// | To request the use of the code herein, please contact:     |
// |                                           ---------------- |
// |                                            info@manha.com  |
// +------------------------------------------------------------+
// | Author:  Mark Yasuda <info@manha.com>                      |
// +------------------------------------------------------------+
// | Version: 1.0                                               |
// | Created: 17/09/2008                                        |
// +------------------------------------------------------------+
// | Version History                                            |
// |                                                            |
// |  2.0 - New presentation screens layout for er.             |
// |                                                            |
// |  1.0 - Presentation screens - Rob Curle - 2008/12/05       |
// +------------------------------------------------------------+
// | Known Bugs And Issues                                      |
// |                                                            |
// |  None.                                                     |
// +------------------------------------------------------------+


include("startup.php");

$locations = get_locations();
$floors = get_locations_floors();
$db = new db_mysql;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Today at the Building Centre</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="refresh" content="60">
<link rel=stylesheet type="text/css" href="css/main.v2.css" />
</head>

<body onload="show_clock();">
<table align="center" width="668" border="0" cellspacing="10" cellpadding="0">
<tr><td class="today_title" valign="top" colspan="2"> <script type="text/javascript" language="javascript" src="js/liveclock_lite.js"></script></td></tr>

<?php
$path = "../".get_image_path();

// Events //
//$db->query("SELECT hosted_by, hosted_other, time_from, time_to, event_title, location FROM events WHERE info_screen=1 AND event_date=CURDATE() AND (time_from>=CURTIME() OR time_to>=CURTIME()) ORDER BY time_from, event_title");
$db->query("SELECT e.hosted_by, e.hosted_other, e.time_from, e.time_to, e.event_title, e.image_src, e.location, e.nla_suite, m.message FROM events AS e LEFT JOIN messages AS m ON e.id=m.event_id WHERE e.visible=1 AND e.info_screen=1 AND e.event_date=CURDATE() ORDER BY e.time_from, e.event_title");	// display all of days events
if ($db->num_rows() > 0) {

  ?>
  <tr><td class="events_title" colspan="2"> </td></tr>
  <?php
	while ($db->next_record()) {

		$has_thumb = false;
		if (is_file($path.$db->f("image_src"))) {

			$has_thumb = true;
		}
		echo "<tr><td colspan=\"2\" valign=\"top\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"10\"><tr><td valign=\"top\">";
		echo "<span class=\"title_txt\">" . $db->f("event_title") . "</span><br />\n";

		if ( strlen( $db->f( "hosted_other" ) ) > 0 ) {

		  echo '<span class="grey org">' . $db->f("hosted_other") . "</span><br />\n";
		}

//		$hosted_txt = ($db->f("hosted_by")==OTHER_IDX ? $db->f("hosted_other"):$hosted_by[$db->f("hosted_by")]);
//		if (!empty($hosted_txt) && $hosted_txt!=$db->f("exhib_title")) echo "{$hosted_txt}<br />\n";

    echo '<span class="grey loc">' . $locations[$db->f("location")] ;
		if ($db->f("location") == NLA_SEMINAR) {
			foreach ($nla_suite as $bit => $room) {
				if (query_bit($db->f("nla_suite"), $bit)) {
					echo " {$room}";
				}
			}
		}
		echo " (".$floors[$db->f("location")]."),</span> ";

		echo "<span class=\"grey\">" . format_time($db->f("time_from"))." - ".format_time($db->f("time_to"))."</span>" ;

		if ($db->f("message")) echo "<br /><span class=\"grey\">".nl2br($db->f("message"))."</span>";
		echo "</td>";

		if ($has_thumb) {

			echo "<td valign=\"top\" width=\"".IMG_WIDTH."\"><img src=\"{$path}".$db->f("image_src")."\" width=\"".IMG_WIDTH."\" height=\"".IMG_HEIGHT."\" border=\"0\" /></td>";
		}

		echo "</tr></table></td></tr>\n";
	}
}

// Exhibitions //
$db->query("SELECT e.hosted_by, e.hosted_other, e.exhib_title, e.location, e.nla_suite, e.date_from, e.date_to, e.image_src, m.message FROM exhibitions AS e LEFT JOIN messages AS m ON e.id=m.exhib_id WHERE e.visible=1 AND e.info_screen=1 AND ((CURDATE() BETWEEN e.date_from AND e.date_to) OR (e.date_from = CURDATE())) ORDER BY e.date_from, e.exhib_title");
if ($db->num_rows() > 0) {

  ?>
  <tr><td class="exhibitions_title" colspan="2"> </td></tr>
  <?php
	while ($db->next_record()) {

		$has_thumb = false;
		if (is_file($path.$db->f("image_src"))) {

			$has_thumb = true;
		}

		echo "<tr><td colspan=\"2\" valign=\"top\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"10\"><tr><td valign=\"top\">";
		echo "<span class=\"title_txt\">" . $db->f("exhib_title") . "</span><br />\n";

		if ( strlen( $db->f( "hosted_other" ) ) > 0 ) {

		  echo '<span class="grey org">' . $db->f("hosted_other") . "</span><br />\n";
		}

//		$hosted_txt = ($db->f("hosted_by")==OTHER_IDX ? $db->f("hosted_other"):$hosted_by[$db->f("hosted_by")]);
//		if (!empty($hosted_txt) && $hosted_txt!=$db->f("exhib_title")) echo "{$hosted_txt}<br />\n";

    echo '<span class="grey loc">' . $locations[$db->f("location")] ;
		if ($db->f("location") == NLA_SEMINAR) {
			foreach ($nla_suite as $bit => $room) {
				if (query_bit($db->f("nla_suite"), $bit)) {
					echo " {$room}";
				}
			}
		}
		echo " (".$floors[$db->f("location")]."),</span> ";

//OLD DATE STYLE SHOWING BOTH DATES
//		echo "<span class=\"grey\">".format_date_title($db->f("date_from"), $db->f("date_to")).", ". $locations[$db->f("location")];
		echo "<span class=\"grey\">Until ".date( 'j F' , strtotime($db->f("date_to")))."</span>" ;

		if ($db->f("message")) echo "<br /><span class=\"grey\">".nl2br($db->f("message"))."</span>";
		echo "</td>";

		if ($has_thumb) {

			echo "<td valign=\"top\" width=\"".IMG_WIDTH."\"><img src=\"{$path}".$db->f("image_src")."\" width=\"".IMG_WIDTH."\" height=\"".IMG_HEIGHT."\" border=\"0\" /></td>";
		}

		echo "</tr></table></td></tr>\n";
	}
}

// Messages //
$db->query("SELECT message, image_src FROM messages WHERE active=1 AND exhib_id is null AND event_id is null AND ((CURDATE() BETWEEN date_from AND date_to AND time_from is null AND time_to is null) OR (CURDATE() BETWEEN date_from AND date_to AND CURTIME() BETWEEN time_from AND time_to)) ORDER BY msg_order DESC LIMIT 4");
if ($db->num_rows() > 0) {

	echo "<tr><td class=\"notices_title\" colspan=\"2\"> </td></tr>\n<tr>";
	for ($i=0; $i<4; $i++) {

		if ($i == 2) echo "</tr>\n<tr>";

	  echo "<td valign=\"top\" width=\"50%\">";
		if ($db->next_record()) {

			if (is_file($path.$db->f("image_src"))) {

				echo "<img src=\"".$path.$db->f("image_src")."\" width=\"".BANNER_WIDTH."\" height=\"".BANNER_HEIGHT."\" border=\"0\" />";
			}
			if ($db->f("message")) {

				echo "<p class=\"message_txt\">".nl2br($db->f("message"))."</p>";
			}
		}
		echo "</td>";
	}
	echo "</tr>\n";
}
?>
</table>
</body>
</html>