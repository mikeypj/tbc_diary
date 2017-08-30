<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		functions.php
//	Desc:		general useful functions
//  Client:		NLA
//	Author:		Rob Curle
//	Date:		18 May 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////
	
	
	function redirect($url) {
		header("Location: $url");
		exit;
	}
	
	function validate_email($email) {
		$regular_expression="^([-!#\$%&'*+./0-9=?A-Z^_`a-z{|}~ ])+@([-!#\$%&'*+/0-9=?A-Z^_`a-z{|}~ ]+\\.)+[a-zA-Z]{2,4}\$";
		return (eregi($regular_expression, $email) !=0);
	}

	function format_date($date_str) {
		return ($date_str==null ? "" : date(SHORT_DATE, strtotime($date_str)));
	}
	
	function format_time($time_str) {
		return ($time_str==null ? "" : date(TIME_12, strtotime($time_str)));
	}
	
	function format_time24($time_str) {
		return ($time_str==null ? "" : date(TIME_24, strtotime($time_str)));
	}
	
	function format_daymonth($date_str) {
		return ($date_str==null ? "" : date(DAY_MONTH, strtotime($date_str)));
	}
	
	function format_monthyear($date_str) {
		return ($date_str==null ? "" : date(MONTH_YEAR, strtotime($date_str)));
	}
	
	function format_datetime($date_str) {
		return ($date_str==null ? "" : date(DATE_TIME, strtotime($date_str)));
	}
	
	function format_abrdate($date_str) {
		return ($date_str==null ? "" : date(ABR_DATE, strtotime($date_str)));
	}
	
	function format_longdate($date_str) {
		return ($date_str==null ? "" : date(LONG_DATE, strtotime($date_str)));
	}
	
	function format_date_title($date_from, $date_to) {
		if (date('Y-m', strtotime($date_from)) == date('Y-m', strtotime($date_to))) {		// same month and year
			$title = date('d', strtotime($date_from))." - ".format_daymonth($date_to);
		}
		else if (date('Y', strtotime($date_from)) == date('Y', strtotime($date_to))) {	// same year
			$title = format_daymonth($date_from)." - ".format_daymonth($date_to);
		}
		else {
			$title = date('jS M', strtotime($date_from))." - ".date('jS M', strtotime($date_to));
		}
		
		return $title;
	}
	
	function get_image_path() {
		return "images/";
	}
	
	function split_date ($date_str) {
		$date['year'] = substr($date_str, 0, 4);
		$date['month'] = substr($date_str, 5, 2);
		$date['day'] = substr($date_str, 8, 2);
		
		return $date;
	}
	
	function get_next_id($table) {
		$db = new db_mysql;
		
		$db->query("SHOW TABLE STATUS LIKE '{$table}'");
		$db->next_record();
			
		return $db->f("Auto_increment");
	}
	
	function get_date_ctl($name, $value='') {
		if ($value=='now' || $value=='NOW') $val = date ('Y-m-d'); 
		else $val = $value;
		
		$html = "<input  type=\"text\" name=\"{$name}\" maxlength=\"10\" size=\"10\" value=\"".(isset($_POST[$name]) ? $_POST[$name]:$val) . "\" /> <a onClick=\"open_calendar('{$name}', '{$name}_cal');\" id=\"{$name}_cal\" name=\"{$name}_cal\"><img src=\"media/cal.gif\" width=\"16\" height=\"16\" border=\"0\" align=\"absmiddle\" /></a></label>";
		
		return $html;
	}
	
	function get_time_ctl($name, $value='') {
		if ($value=='now' || $value=='NOW') $val = date ('G:i'); 
		else if (empty($value)) $val = "hh:mm";
		else $val = $value;
	
		return "<input type=\"text\" name=\"{$name}\" maxlength=\"5\" size=\"6\" value=\"".(isset($_POST[$name]) ? $_POST[$name]:$val) . "\" onFocus=\"time_focus(this)\" />";
	}
	
	function validate_date($date_str) {
		$valid = false;
		
		if (!empty($date_str)) {
			if (($timestamp = strtotime($date_str)) !== -1) {
				$valid = true;
			}
		}
		
		return $valid;
	}
	
	function validate_time($time_str) {
		$valid = false;
		
		if (preg_match("/[0-9]{2}+[:]+[0-9]{2}/", $time_str)) {
			if (($timestamp = strtotime($time_str)) !== -1) {
				$valid = true;
			}
		}
		return $valid;
	}
	
	function summary($txt, $chars=200) {
		if (strlen($txt) > $chars) {
			$cut_text = substr($txt, 0, $chars); 					// cut string
			$pos = strrpos($cut_text, " ");							// find last space
			$summary_txt = substr($cut_text, 0, $pos) . "...";		// cut string before last space
		}
		else {
			 $summary_txt = $txt;
		}
		return $summary_txt;		
	}
	
	function find_start_of_week() {
		$day = date ('w', strtotime('now'));
		if ($day == 0) $minus_days = 6;
		else $minus_days = $day - 1;
		$week_start = date('Y-m-d', strtotime("-{$minus_days} day" , strtotime("now")));
		
		return $week_start;
	}
	
	function query_bit($var, $bit) {
	    return $var & $bit;
	}
	
	function set_bit(&$var, $bit, $set=true) {
	   $var = $set ? ($var | $bit) : ($var & ~$bit);
	}
	
	function get_icons($status) {
		switch ($status) {
			case 1: $img="<img src=\"media/btns_type/tentative.gif\" alt=\"\" width=\"50\" height=\"20\" border=\"0\" />"; break;
			case 3: $img="<img src=\"media/btns_type/cancelled.gif\" alt=\"\" width=\"57\" height=\"20\" border=\"0\" />"; break;
		}
		if (!empty($img)) {
			$icons = "<p class='type_label'>{$img}</p>";
		}	
			
		return $icons;
	}
	
	function logged_in() {
		return $_SESSION['sess_bcd'];
	}
	
	function last_modified($date, $name) {
		return date(DATE_TIME, strtotime($date)) . " | ".$name;
	}
	
	function format_id($id) {
		return  sprintf("%05u", $id);
	}
	
	function get_users() {
		$db = new db_mysql;
		$users = array();
		
		$db->query("SELECT name FROM admin_users WHERE location_rights > 0 ORDER BY name");
		while ($db->next_record()) {
			$users[] = $db->f("name");
		}
	
		return $users;
	}
	
	function get_locations() {
		$db = new db_mysql;
		$locations = array();
		
		$db->query("SELECT id, name FROM locations ORDER BY name");
		while ($db->next_record()) {
			$locations[$db->f("id")] = $db->f("name");
		}
		
		return $locations;
	}
	
	function get_locations_floors() {
		$db = new db_mysql;
		$floors = array();
		
		$db->query("SELECT id, floor FROM locations ORDER BY id");
		while ($db->next_record()) {
			$floors[$db->f("id")] = $db->f("floor");
		}
		
		return $floors;
	}
	
	function get_user_locations() {
		$user_locs = array();
		$locations = get_locations();
		$locs = explode("|", $_SESSION['loc_rights']);
		
		foreach ($locations as $id => $txt) {
			if (in_array ($id, $locs)) {
				$user_locs[$id] = $txt;
			}
		}
		
		return $user_locs;
	}
	
	function msg($title, $msg, $url="") {
		$hide_nav = true;
		$metatag = "<meta http-equiv=\"refresh\" content=\"1; url=$url\" />";
		include("html_header.php");
	?>
	<table width="750" border="0" cellpadding="7" cellspacing="0">
	<tr><td width="350" valign="top" class="content">
	<h1><?php echo $title;?></h1>
	<p><?php echo $msg;?></p>
	<p><img src="media/trans.gif" alt="" width="1" height="40" border="0"/></p>
	</td><td class="content_right">&nbsp;</td></tr>
	</table>
	<?php
		include("html_footer.php");
		exit;
	}
	
	function question_msg($title, $question, $yes_url, $no_url) {
		$hide_nav = true;
		include("html_header.php");
	?>
	<table width="750" border="0" cellpadding="7" cellspacing="0">
	<tr><td width="350" valign="top" class="content">
	<h1><?php echo $title;?></h1>
	<p><?php echo $question;?></p>
	<p><a href="<?php echo $yes_url;?>" title="Yes"><img src="media/btns/yes.gif" alt="" width="31" height="18" border="0" /></a>&nbsp;&nbsp;
	<a href="<?php echo $no_url;?>" title="No"><img src="media/btns/no.gif" alt="" width="26" height="18" border="0" /></a></p>
	<p><img src="media/trans.gif" alt="" width="1" height="40" border="0"/></p>
	</td><td class="content_right">&nbsp;</td></tr>
	</table>
	<?php
		include("html_footer.php");
		exit;
	}
	
	function get_paged_nav($page_items, $num_rows, $offset, $extra_data='') {	
		$nav = "";
		$num_pages = intval($num_rows / $page_items);	// get total pages
		if ($num_rows % $page_items) {
			$num_pages++;
		}
		
		if ($num_pages > 1) {
			$nav = "<div id=\"paged_nav\">\n<ul>\n";
			
			if ($offset >= $page_items) {	// check to see if need a previous button
				$prevoffset=$offset-$page_items;
				$nav .= "<li><a href=\"" . $_SERVER['PHP_SELF'] . "?o=$prevoffset$extra_data\" title=\"Previous page\"><img src=\"media/btns/left.gif\" name=\"prev\" width=\"14\" height=\"18\" border=\"0\" /></a></li>";
			}

			for ($i=1; $i<=$num_pages; $i++) {
				$newoffset = $page_items * ($i-1);
				
				if (($offset < $i*$page_items) && ($offset >= ($i-1)*$page_items)) {
					$nav .=  "<li class=\"current\">$i</li>";	// current page
				}
				else {
					$nav .= "<li><a href=\"" . $_SERVER['PHP_SELF'] . "?o=$newoffset$extra_data\">$i</a></li>";
				}
			}
	
			if (($offset+$page_items < $num_rows) && $num_pages != 1) {	// check to see if need a next button
				$newoffset = $offset + $page_items;
				$nav .= "<li><a href=\"" . $_SERVER['PHP_SELF'] . "?o=$newoffset$extra_data\" title=\"Next page\"><img src=\"media/btns/right.gif\" name=\"next\" width=\"14\" height=\"18\" border=\"0\" /></a></li>";
			}
			$nav .= "</ul>\n</div>\n";
		}

		return $nav;
	}
?>