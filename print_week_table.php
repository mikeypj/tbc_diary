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
// |  1.0 - Original print view for weekly planner.             |
// +------------------------------------------------------------+
// | Known Bugs And Issues                                      |
// |                                                            |
// |  None.                                                     |
// +------------------------------------------------------------+


include("includes/startup.php");

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

$where_clause = "";

if (empty($_REQUEST['w'])) {

	redirect ( 'planner.php' );
}

if (!empty($_REQUEST['r'])) {

	$where_clause = "e.location={$_SESSION['r']} AND ";
}

$week_start = $_REQUEST['w'];
$week_end = date('Y-m-d', strtotime('+6 day' , strtotime("{$_SESSION['w']} 00:01:00 GMT")));
$title = custom_format_date_title($week_start, $week_end);

$locations = get_locations();

include("includes/print_header.php");

?><div id="weekview"><?php

//dump listings
if ( !empty( $_REQUEST['w'] ) ) {

  echo '<div id="wk_title">Building Centre Diary - Week ' . $title . '</div>' ;

	$week_end = date( 'Y-m-d' , strtotime( "+6 day" , strtotime( "{$week_start} 00:01:00 GMT" ) ) ) ;

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

  			$hosted_by = ($db_ev->f("hosted_by")==OTHER_IDX ? 'Other' : $hosted_by_abr[$db_ev->f("hosted_by")]);
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
}

?></div><?php


//////////////// PRINT FUNCS
function print_exhibition($id, $status, $hosted_by, $date_from, $date_to, $location, $title) {

  ?>
  <tr>
    <td class="title"><h3><?= $title ;?></h3><?php

    if ( $status > 0 ) {

    	switch ($status) {

    		case 1: echo ' (Tentative)' ; break;
    		case 3: echo ' (Cancelled)' ; break;
    	}
    }

    ?></td>
    <td class="time"><?php

    if ( ( strlen( $date_to ) > 0 ) && ( $date_to != null ) ) {

      ?>Until <?= format_date( $date_to ) ;?><?php
    } else {

      ?>On <?= format_date( $date_from ) ;?><?php
    }

    ?></td>
    <td class="loc"><?= $location ;?></td>
  </tr>
  <?php
}

function print_event($id, $status, $hosted_by, $time_from, $time_to, $location, $title, $num_people=0) {

  ?>
  <tr>
    <td class="title"><h3><?= $title ;?></h3><?php

    if ( $status > 0 ) {

    	switch ($status) {

    		case 1: echo ' (Tentative)' ; break;
    		case 3: echo ' (Cancelled)' ; break;
    	}
    }

    ?></td>
    <td class="time"><?= format_time( $time_from ) . " - " . format_time( $time_to ) ;?></td>
    <td class="loc"><?= $location ;?></td>
    <td class="ppl"><?= ( $num_people > 0 ) ? $num_people . 'ppl' : '' ;?></td>
  </tr>
  <?php
}

function custom_format_date_title($date_from, $date_to) {
	if (date('Y-m', strtotime($date_from)) == date('Y-m', strtotime($date_to))) {		// same month and year
		$title = date('d', strtotime($date_from))." - ".format_daymonthfull($date_to);
	}
	else if (date('Y', strtotime($date_from)) == date('Y', strtotime($date_to))) {	// same year
		$title = format_daymonthfull($date_from)." - ".format_daymonthfull($date_to);
	}
	else {
		$title = date('jS M', strtotime($date_from))." - ".date('jS M', strtotime($date_to));
	}

	return $title;
}


function format_daymonthfull($date_str) {

	return ($date_str==null ? "" : date('j F', strtotime($date_str)));
}


?>