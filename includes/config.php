<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		config.php
//	Desc:		defined constants
//  Client:		BCD
//	Author:		Rob Curle
//	Date:		18 November 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////

	define("DB_DEBUG", 0);					// define debug display mode

	define("DB_HOST", "10.0.10.10");
	define("DB_NAME", "buildingdiary");
	define("DB_USER", "bdiary_usr");
	define("DB_PWD", "pCp5weGmdcdvDDth");
	define("SITE_URL", "94.199.191.75");

	define ("SITE_EMAIL_NAME", "Building Centre Diary");
	define ("SITE_EMAIL", "info@buildingcentrediary.com");


	// filter types //
	define ("EVENTS", 1);
	define ("EXHIBITIONS", 2);
	define ("MESSAGES", 3);

	define ("IMG_WIDTH", 90);
	define ("IMG_HEIGHT", 60);
	define ("BANNER_WIDTH", 320);
	define ("BANNER_HEIGHT", 100);

	define ("YEAR_START", date('Y'));
	define ("YEAR_END", YEAR_START + 5);
	define ("SHORT_DATE", "d M y");			// 04 Jan 03
	define ("ABR_DATE", "D jS M Y");		// Sat 4th Jan 2005
	define ("LONG_DATE", "D jS F Y");		// Sat 4th January 2005
	define ("DATE_TIME", "d-M-y, H:i");		// 04-Jan-03, 12:32
	define ("DAY_MONTH", "d M");
	define ("MONTH_YEAR", "F Y");
	define ("TIME_12", "g:ia");
	define ("TIME_24", "G:i");

	define ("NUM", "%1.2f");				// 1.00
	define ("NUM_CATERING", 4);
	define ("HOME", "planner.php");
	define ("PAGE_ITEMS", 50);				// number of items per page, used by paged nav

	$months = array("01" => "Jan", "02" => "Feb", "03" => "Mar", "04" => "Apr", "05" => "May", "06" => "Jun", "07" => "Jul", "08" => "Aug", "09" => "Sep", "10" => "Oct", "11" => "Nov", "12" => "Dec");

	$event_fields = array("f1" => "l.id", "f2" => "l.event_title", "f3" => "l.hosted_by", "f4" => "l.location", "f5" => "l.event_date", "f6" => "l.time_from", "f7" => "l.created_by", "f8" => "info_screen", "f9" => "msg", "f10" => "l.visible");
	$exhib_fields = array ("f1" => "l.id", "f2" => "l.exhib_title", "f3" => "l.hosted_by", "f4" => "l.location", "f5" => "l.date_from", "f6" => "l.created_by", "f7" => "info_screen", "f8" => "msg", "f9" => "l.visible");
	$msg_fields = array ("f1" => "l.id", "f2" => "l.message", "f3" => "attached_to", "f4" => "l.active", "f5" => "l.date_from", "f6" => "l.time_from", "f7" => "current");

	$event_status = array(1 => "Tentative", 2 => "Confirmed", 3 => "Cancelled");

	$hosted_by = array(1 => "The Building Centre", 2 => "Building Centre Trust", 3 => "New London Architecture", 4 => "Other...");
	$hosted_by_abr = array(1 => "BC", 2 => "BCT", 3 => "NLA", 4 => "Other");
	define ("OTHER_IDX", 4);

	$nla_suite = array (1=>"A", 2=>"B", 4=>"C");
	define ("NLA_SEMINAR", 13);

	$equipment = array(1 => "SVGA Projector", 2 => "Slide Projector", 4 => "OHP", 8 => "VHS & Monitor", 16 => "Flipchart", 32 => "Lectern", 64 => "PC", 128 => "Laser Pointer", 256 => "Powerpoint remote", 512 => "PA system");
	$setup_times = array (1 => "30 mins before", 2 => "1 hr before", 3 => "Day before");
?>
