<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		planner.php
//	Desc:		homepage, planner page
//  Client:		LAD
//	Author:		Rob Curle
//	Date:		13 June 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////


include("includes/startup.php");
require_once("includes/activecalendar.php");

validate_querystring_dates();

$where_clause = "";

//FORCE BOARDROOM FOR BR_GUEST START
if ( $_SESSION['admin_id'] == 43 ) {

  $_SESSION['r'] = 6 ;
} else
//FORCE BOARDROOM FOR BR_GUEST END

if (isset($_REQUEST['r'])) {

	$_SESSION['r'] = $_REQUEST['r'];
}
if (!empty($_REQUEST['m'])) {

	$_SESSION['m'] = $_REQUEST['m'];
}
if (!empty($_GET['d'])) {

	$_SESSION['d'] = $_GET['d'];
	$_SESSION['w'] = "";
} else if (!empty($_GET['w'])) {

	$_SESSION['w'] = $_GET['w'];
	$_SESSION['d'] = "";
}

if (!empty($_SESSION['r'])) {

	switch ($_SESSION['r']) {

//		case "bc": $where_clause = "(staff.provided_by=1 OR setup.provided_by=1 OR security.provided_by=1 OR equipment.provided_by=1 OR drinks.provided_by=1 OR catering.provided_by=1) AND "; break;
//		case "nla": $where_clause = "(staff.provided_by=2 OR setup.provided_by=2 OR security.provided_by=2 OR equipment.provided_by=2 OR drinks.provided_by=2 OR catering.provided_by=2) AND "; break;
		default: $where_clause = "e.location={$_SESSION['r']} AND ";
	}
}

if (!empty($_SESSION['d'])) {

	$day_date = $_SESSION['d'];
	$prev =  "?d=" . date('Y-m-d', strtotime('-1 day' , strtotime("{$day_date} 00:01:00 GMT")));
	$next =  "?d=" . date('Y-m-d', strtotime('+1 day' , strtotime("{$day_date} 00:01:00 GMT")));
	$title = format_longdate($day_date);
	$tooltip = "day";
}	else if (!empty($_SESSION['w'])) {

	$week_start = $_SESSION['w'];
	$week_end = date('Y-m-d', strtotime('+6 day' , strtotime("{$_SESSION['w']} 00:01:00 GMT")));
	$prev =  "?w=" . date('Y-m-d', strtotime('-1 week' , strtotime("{$week_start} 00:01:00 GMT")));
	$next =  "?w=" . date('Y-m-d', strtotime('+1 week' , strtotime("{$week_start} 00:01:00 GMT")));
	$title = format_date_title($week_start, $week_end);
	$tooltip = "week";
}	else {

  $_SESSION['d'] = $day_date = date('Y-m-d');
	$prev =  "?d=" . date('Y-m-d', strtotime('-1 day' , strtotime("{$day_date} 00:01:00 GMT")));
	$next =  "?d=" . date('Y-m-d', strtotime('+1 day' , strtotime("{$day_date} 00:01:00 GMT")));
	$title = format_longdate($day_date);
	$tooltip = "day";
}

$date_nav = "<a href=\"{$_SERVER['PHP_SELF']}{$prev}{$extra_data}\" title=\"previous {$tooltip}\"><img src=\"media/btns/left.gif\" alt=\"previous {$tooltip}\" width=\"14\" height=\"18\" border=\"0\" align=\"absmiddle\"></a>&nbsp;&nbsp;" . $title . "&nbsp;&nbsp;<a href=\"{$_SERVER['PHP_SELF']}{$next}{$extra_data}\" title=\"next {$tooltip}\"><img src=\"media/btns/right.gif\" width=\"14\" alt=\"next {$tooltip}\" height=\"18\" border=\"0\" align=\"absmiddle\"></a>";

if ( !empty( $_SESSION['w'] ) ) {

  $date_nav .= '&nbsp;&nbsp;<a href="print_week_table.php?' ;
  if ( !empty( $_SESSION['r'] ) ) {

    $date_nav .= 'r=' . $_SESSION['r'] . '&' ;
  }
  $date_nav .= 'w=' . $_SESSION['w'] . '" target="_new"><img src="media/btns/print.gif" alt="" width="38" height="18" border="0" align="absmiddle" /></a>' ;
}

$locations = get_locations();

$section = 1;
include("includes/html_header.php");
?>

<table width="750" border="0" cellpadding="7" cellspacing="0">
<form name="userdata" action="<?php echo $_SERVER['PHP_SELF'];?>" method="get">
<tr><td valign="middle" class="title_bar"><?php echo $date_nav;?></td><td align="right" valign="middle" class="title_bar">
<?php
if ( $_SESSION['admin_id'] == 43 ) {

  echo 'Boardroom' ;

} else {

  ?>Filter: <select name="r" size="1" onchange="submitform()" class="small_txt"><option value=""> All </option><option value=""> ----------------------------- </option>
  <!--<option value="bc"<?php echo ($_SESSION['r']=="bc" ? "selected='selected'":"");?>> Services provided by BC </option><option value="nla"<?php echo ($_SESSION['r']=="nla" ? "selected='selected'":"");?>> Services provided by NLA </option><option value=""> ----------------------------- </option>--><?php
	foreach ($locations as $id => $txt) {
		echo "<option value=\"$id\" " . ($id==$_SESSION['r'] ? "selected='selected'":""). "> $txt </option>";
	}
	?></select><?php

}
?></td></tr>
</form>
</table>
<table border="0" cellpadding="0" cellspacing="0">
<tr><td width="180" valign="top" class="dark_grey"><table width="180" border="0" cellspacing="0" cellpadding="7">
<?php

if (empty($_SESSION['m'])) {

	$start_month = date('m');
	$start_year = date('Y');
}	else {

	$start_month = substr($_SESSION['m'], 5, 2);
	$start_year = substr($_SESSION['m'], 0, 4);
}

$today = date('Y-m-d');
$this_week = find_start_of_week();
$prev_month = date('Y-m', strtotime('-1 month' , strtotime("{$start_year}-{$start_month}-01 00:01:00 GMT")));
$next_month = date('Y-m', strtotime('+1 month' , strtotime("{$start_year}-{$start_month}-01 00:01:00 GMT")));

?>
<tr><td align="center" valign="top"><a href="<?php echo $_SERVER['PHP_SELF']."?d={$today}";?>"><img src="media/btns/today.gif" width="45" height="18" border="0" align="absmiddle" /></a>&nbsp;<a href="<?php echo $_SERVER['PHP_SELF']."?w={$this_week}";?>"><img src="media/btns/thisweek.gif" width="65" height="18" border="0" align="absmiddle" /></a>&nbsp;<a href="<?php echo $_SERVER['PHP_SELF']."?m={$prev_month}";?>" title="previous month"><img src="media/btns/up.gif" alt="previous month" width="18" height="18" border="0" align="absmiddle" /></a>&nbsp;<a href="<?php echo $_SERVER['PHP_SELF']."?m={$next_month}";?>" title="next month"><img src="media/btns/down.gif" alt="next month" width="18" height="18" border="0" align="absmiddle" /></a></td></tr>
<tr><td align="center" valign="top">
<?php

//  Build Calendars //
$db = new db_mysql;

for ( $cal_count = 0; $cal_count < 2; $cal_count++ ) {

	if ($cal_count == 0) {

		$month = $start_month;
		$year = $start_year;
	} else {

		$month = date('m', strtotime("+".$cal_count." month" , strtotime("{$start_year}-{$start_month}-01 00:01:00 GMT")));
		$year = date('Y', strtotime("+".$cal_count." month" , strtotime("{$start_year}-{$start_month}-01 00:01:00 GMT")));
	}
	$start_date = "{$year}-{$month}-01";
	$end_date = date('Y-m-d', strtotime('+1 month' , strtotime("{$start_date} 00:01:00 GMT")));
	$end_date = date('Y-m-d', strtotime('-1 day' , strtotime("{$end_date} 00:01:00 GMT")));
	$cal = new activeCalendar($year, $month);
	$cal->add_url($_SERVER['PHP_SELF'].$extra_data_only);

	// add 1 day events //
	$db->query("SELECT DISTINCT e.event_date FROM events AS e WHERE e.visible=1 AND {$where_clause} (e.event_date BETWEEN '{$start_date}' AND '{$end_date}') ORDER BY e.event_date");
	while ($db->next_record()) {
		$date = split_date ($db->f("event_date"));
		$cal->setEvent($date['year'], $date['month'], $date['day']);
	}

	echo $cal->showMonth() . "<br/>";
}

echo "Click 'wk' for week view";

?>
</td></tr>
</table></td>
<td class="content" valign="top" width="700">
<?php

?><div id="weekview"><?php

//dump listings
if ( !empty( $_SESSION['w'] ) ) {

	$db_ex = new db_mysql;
	$ex_sql = "SELECT DISTINCT e.id, e.status, e.hosted_by, e.hosted_other, e.date_from, e.date_to, e.exhib_title, e.location, e.nla_suite, e.image_src FROM exhibitions AS e WHERE e.visible=1 AND {$where_clause} ( (e.date_from BETWEEN '{$week_start}' AND '{$week_end}') OR ( e.date_to BETWEEN '{$week_start}' AND '{$week_end}' ) ) ORDER BY e.date_from, e.exhib_title" ;
	$db_ex->query( $ex_sql ) ;

  $numExhibs = $db_ex->num_rows() ;

  echo '<h2>Exhibitions</h2><div class="tbl_content">' ;

  ?>
  <table class="wk_table">
  <?php

	if ( $numExhibs > 0 ) {

		while ( $db_ex->next_record( ) ) {

			$hosted_by = ( $db_ex->f("hosted_by")==OTHER_IDX ? $db->f("hosted_other") : $hosted_by_abr[$db_ex->f("hosted_by")]);
			$location = $locations[$db_ex->f("location")] ;
      if ( $db_ex->f("location") == NLA_SEMINAR ) {
        foreach( $nla_suite as $bit => $room ) {
          if ( query_bit( $db_ex->f("nla_suite") , $bit ) ) {
            $location .= " " . $room ;
          }
        }
      }
			print_exhibition($db_ex->f("id"), $db_ex->f("status"), $hosted_by, $db_ex->f("date_from"), $db_ex->f("date_to"), $location, $db_ex->f("exhib_title"));
		}
  } else {

    ?><tr><td class="nothing" colspan="4">No scheduled exhibitions.</td></tr><?php
  }
  ?></table></div><?php

	for ( $day = 0 ; $day < 7 ; $day++ ) {

		if ( $day == 0 ) {

			$day_date = $week_start;
		} else {

			$day_date = date( 'Y-m-d' , strtotime( "+" . $day . " day" , strtotime( "{$week_start} 00:01:00 GMT" ) ) ) ;
		}

    echo "<h2>" . date( 'l j' , strtotime($day_date) ) . "</h2><div class=\"tbl_content\">";

  	$db_ev = new db_mysql;
  	$db_ev->query("SELECT e.id, e.status, e.hosted_by, e.hosted_other, e.time_from, e.time_to, e.event_title, e.location, e.nla_suite, e.num_people, e.image_src FROM events AS e WHERE e.visible=1 AND {$where_clause} e.event_date='{$day_date}' ORDER BY e.time_from, e.event_title");

    $numEvents = $db_ev->num_rows() ;

	  ?>
	  <table class="wk_table">
    <?php

  	if ( $numEvents > 0 ) {

  		while ($db_ev->next_record()) {

  			$hosted_by = ($db_ev->f("hosted_by")==OTHER_IDX ? $db->f("hosted_other") : $hosted_by_abr[$db_ev->f("hosted_by")]);
  			$location = $locations[$db_ev->f("location")] ;
        if ( $db_ev->f("location") == NLA_SEMINAR ) {
          foreach( $nla_suite as $bit => $room ) {
            if ( query_bit( $db_ev->f("nla_suite") , $bit ) ) {
              $location .= " " . $room ;
            }
          }
        }
  			print_event($db_ev->f("id"), $db_ev->f("status"), $hosted_by, $db_ev->f("time_from"), $db_ev->f("time_to"), $location, $db_ev->f("event_title"), $db_ev->f("num_people"));
  		}
    } else {

      ?><tr><td class="nothing" colspan="5">No scheduled events.</td></tr><?php
    }
    ?></table></div><?php
	}
} else if ( !empty( $_SESSION['d'] ) ) {

  //if day view...
	$db_ex = new db_mysql;
	$ex_sql = "SELECT DISTINCT e.id, e.status, e.hosted_by, e.hosted_other, e.date_from, e.date_to, e.exhib_title, e.location, e.nla_suite, e.image_src FROM exhibitions AS e WHERE e.visible=1 AND {$where_clause} ( ( '{$_SESSION['d']}' BETWEEN e.date_from AND e.date_to ) OR ( '{$_SESSION['d']}' = e.date_from ) ) ORDER BY e.date_from, e.exhib_title" ;
	$db_ex->query( $ex_sql ) ;

  $numExhibs = $db_ex->num_rows() ;

  echo '<h2>Exhibitions</h2><div class="tbl_content">' ;

  ?>
  <table class="wk_table">
  <?php

	if ( $numExhibs > 0 ) {

		while ( $db_ex->next_record( ) ) {

			$hosted_by = ( $db_ex->f("hosted_by")==OTHER_IDX ? 'Other' : $hosted_by_abr[$db_ex->f("hosted_by")]);
			$location = $locations[$db_ex->f("location")] ;
      if ( $db_ex->f("location") == NLA_SEMINAR ) {
        foreach( $nla_suite as $bit => $room ) {
          if ( query_bit( $db_ex->f("nla_suite") , $bit ) ) {
            $location .= " " . $room ;
          }
        }
      }
			print_exhibition($db_ex->f("id"), $db_ex->f("status"), $hosted_by, $db_ex->f("date_from"), $db_ex->f("date_to"), $location, $db_ex->f("exhib_title"));
		}
  } else {

    ?><tr><td class="nothing" colspan="4">No scheduled exhibitions.</td></tr><?php
  }
  ?></table></div><?php

  echo '<h2>Events</h2><div class="tbl_content">';

	$db_ev = new db_mysql;
	$db_ev->query("SELECT e.id, e.status, e.hosted_by, e.hosted_other, e.time_from, e.time_to, e.event_title, e.location, e.nla_suite, e.num_people, e.image_src FROM events AS e WHERE e.visible=1 AND {$where_clause} e.event_date='{$day_date}' ORDER BY e.time_from, e.event_title");

  $numEvents = $db_ev->num_rows() ;

  ?>
  <table class="wk_table">
  <?php

	if ( $numEvents > 0 ) {

		while ($db_ev->next_record()) {

			$hosted_by = ($db_ev->f("hosted_by")==OTHER_IDX ? $db->f("hosted_other") : $hosted_by_abr[$db_ev->f("hosted_by")]);
			$location = $locations[$db_ev->f("location")] ;
      if ( $db_ev->f("location") == NLA_SEMINAR ) {
        foreach( $nla_suite as $bit => $room ) {
          if ( query_bit( $db_ev->f("nla_suite") , $bit ) ) {
            $location .= " " . $room ;
          }
        }
      }
			print_event($db_ev->f("id"), $db_ev->f("status"), $hosted_by, $db_ev->f("time_from"), $db_ev->f("time_to"), $location, $db_ev->f("event_title"), $db_ev->f("num_people"));
		}
  } else {

    ?><tr><td class="nothing" colspan="5">No scheduled events.</td></tr><?php
  }
  ?></table></div><?php
}

?></td>
</tr></table>

<?php
	include("includes/html_footer.php");

//////////////// PRINT FUNCS
function print_exhibition($id, $status, $hosted_by, $date_from, $date_to, $location, $title) {

  ?>
  <tr>
    <td class="title"><h3><a href="exhibition.php?id=<?php echo  $id ;?>"><?php echo  $title ;?></a></h3><?php

    if ( $status > 0 ) {

    	switch ($status) {

    		case 1: echo ' (Tentative)' ; break;
    		case 3: echo ' (Cancelled)' ; break;
    	}
    }

    ?></td>
    <td class="time"><?php

    if ( ( strlen( $date_to ) > 0 ) && ( $date_to != null ) ) {

      ?>Until <?php echo  format_date( $date_to ) ;?><?php
    } else {

      ?>On <?php echo  format_date( $date_from ) ;?><?php
    }

    ?></td>
    <td class="loc"><?php echo  $location ;?></td>
  </tr>
  <?php
}

function print_event($id, $status, $hosted_by, $time_from, $time_to, $location, $title, $num_people=0) {

  ?>
  <tr>
    <td class="title"><h3><a href="event.php?id=<?php echo  $id ;?>"><?php echo  $title ;?></a></h3><?php

    if ( $status > 0 ) {

    	switch ($status) {

    		case 1: echo ' (Tentative)' ; break;
    		case 3: echo ' (Cancelled)' ; break;
    	}
    }

    ?></td>
    <td class="time"><?php echo  format_time( $time_from ) . " - " . format_time( $time_to ) ;?></td>
    <td class="loc"><?php echo  $location ;?></td>
    <td class="ppl"><?php echo  ( $num_people > 0 ) ? $num_people . 'ppl' : '' ;?></td>
  </tr>
  <?php
}

///////////////////////////////////////////////////////////////////////////////////////

	function validate_querystring_dates() {
		if (!empty($_REQUEST['d'])) {
			$arr_date = split_date ($_REQUEST['d']);

			if (is_numeric($arr_date['day']) && is_numeric($arr_date['month']) && is_numeric($arr_date['year'])) {
				if (!checkdate ($arr_date['month'], $arr_date['day'], $arr_date['year'])) {
					$_REQUEST['d'] = "";
				}
			}
			else {
				$_REQUEST['d'] = "";
			}
		}

		if (!empty($_REQUEST['m'])) {
			$arr_date = split_date ($_REQUEST['m']);
			$arr_date['day'] = "01";

			if (is_numeric($arr_date['day']) && is_numeric($arr_date['month']) && is_numeric($arr_date['year'])) {
				if (!checkdate ($arr_date['month'], $arr_date['day'], $arr_date['year'])) {
					$_REQUEST['m'] = "";
				}
			}
			else {
				$_REQUEST['m'] = "";
			}
		}

		if (!empty($_REQUEST['w'])) {
			$arr_date = split_date ($_REQUEST['w']);

			if (is_numeric($arr_date['day']) && is_numeric($arr_date['month']) && is_numeric($arr_date['year'])) {

				if (!checkdate ($arr_date['month'], $arr_date['day'], $arr_date['year'])) {

					$_REQUEST['w'] = "";
				}
			} else {

				$_REQUEST['w'] = "";
			}
		}
	}

	function show_exhibition($id, $status, $hosted_by, $date_from, $date_to, $location, $nla_suite, $title, $image_src='') {
		$path = get_image_path();
		$has_thumb = is_file($path.$image_src);

		$event = "<tr><td".($has_thumb ? "":" colspan='2'")."><a href=\"exhibition.php?id={$id}\" class=\"bold\">{$title}</a><br />\n";
		$event .= "<span class='grey'>Date:</span> " . format_date($date_from) . " - " . format_date($date_to) . "<br />\n";
		$event .= "<span class='grey'>Hosted by:</span> {$hosted_by}<br />\n";
		$event .= "<span class='grey'>Location:</span> " . $GLOBALS['locations'][$location];
		if ($location == NLA_SEMINAR) {
			foreach ($GLOBALS['nla_suite'] as $bit => $room) {
				if (query_bit($nla_suite, $bit)) {
					$event .= " {$room}";
				}
			}
		}
		$event .= get_icons($status);
		$event .= "</td>";
		if ($has_thumb) {
			$event .= "<td width=\"".IMG_WIDTH."\"><img src=\"{$path}{$image_src}\" width=\"".IMG_WIDTH."\" height=\"".IMG_HEIGHT."\" border=\"0\" /></td>";
		}
		$event .= "</tr>\n";

		return $event;
	}

	function show_event($id, $status, $hosted_by, $time_from, $time_to, $location, $nla_suite, $title, $num_people=0, $image_src='') {
		$path = get_image_path();
		$has_thumb = is_file($path.$image_src);

		$event = "<tr><td".($has_thumb ? "":" colspan='2'")."><a href=\"event.php?id={$id}\" class=\"bold\">{$title}</a>";
		if ($num_people > 0) {
			$event .= " ({$num_people} people)";
		}
		$event .= "<br />\n<span class='grey'>Time:</span> " . format_time($time_from) . " - " . format_time($time_to) . "<br />\n";
		$event .= "<span class='grey'>Hosted by:</span> {$hosted_by}<br />\n";
		$event .= "<span class='grey'>Location:</span> " . $GLOBALS['locations'][$location];
		if ($location == NLA_SEMINAR) {
			foreach ($GLOBALS['nla_suite'] as $bit => $room) {
				if (query_bit($nla_suite, $bit)) {
					$event .= " {$room}";
				}
			}
		}
		$event .= get_icons($status);
		$event .= "</td>";
		if ($has_thumb) {
			$event .= "<td width=\"".IMG_WIDTH."\"><img src=\"{$path}{$image_src}\" width=\"".IMG_WIDTH."\" height=\"".IMG_HEIGHT."\" border=\"0\" /></td>";
		}
		$event .= "</tr>\n";

		return $event;
	}
?>
