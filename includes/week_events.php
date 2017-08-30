<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		week_events.php
//	Desc:		display a weeks events.
//  Client:		LAD
//	Author:		Rob Curle
//	Date:		13 June 2005
//	Notes:		expects: $week_start
//
///////////////////////////////////////////////////////////////////////////////////////

	for ($day=0; $day<7; $day++) {
		if ($day == 0) {
			$day_date = $week_start;
		}
		else {
			$day_date = date('Y-m-d', strtotime("+".$day." day" , strtotime("{$week_start} 00:01:00 GMT")));
		}
		
		include("includes/day_events.php");
	}
?>