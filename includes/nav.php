<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		nav.php
//	Desc:		write out navigation
//  Client:		LAD
//	Author:		Rob Curle
//	Date:		13 June 2005
//	Notes:		expects a $section var, indicating which nav item is set on
//
///////////////////////////////////////////////////////////////////////////////////////

	${"nav".$section} = "_on";
	
	if (!$hide_nav) {
?><table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td valign="baseline"><a href="planner.php" onmouseover="changeImages('planner', 'media/btns_menu/planner_on.gif')" onmouseout="changeImages('planner', 'media/btns_menu/planner<?php echo $nav1;?>.gif')" title=""><img src="media/btns_menu/planner<?php echo $nav1;?>.gif" name="planner" alt="" width="57" height="18" border="0" align="bottom" /></a><?php
	if ($_SESSION['loc_rights']) {
		echo "<img src=\"media/trans.gif\" width=\"2\" height=\"1\" border=\"0\" alt=\"\" /><a href=\"admin_events.php\" onmouseover=\"changeImages('events', 'media/btns_menu/events_on.gif')\" onmouseout=\"changeImages('events', 'media/btns_menu/events{$nav2}.gif')\" title=\"\"><img src=\"media/btns_menu/events{$nav2}.gif\" name=\"events\" alt=\"\" width=\"49\" height=\"18\"  border=\"0\" align=\"bottom\" /></a>";
		echo "<img src=\"media/trans.gif\" width=\"2\" height=\"1\" border=\"0\" alt=\"\" /><a href=\"admin_exhibitions.php\" onmouseover=\"changeImages('exhibitions', 'media/btns_menu/exhibitions_on.gif')\" onmouseout=\"changeImages('exhibitions', 'media/btns_menu/exhibitions{$nav3}.gif')\" title=\"\"><img src=\"media/btns_menu/exhibitions{$nav3}.gif\" name=\"exhibitions\" alt=\"\" width=\"74\" height=\"18\"  border=\"0\" align=\"bottom\" /></a>";
	}
	if ($_SESSION['msg_rights']) {
		echo "<img src=\"media/trans.gif\" width=\"2\" height=\"1\" border=\"0\" alt=\"\" /><a href=\"admin_messages.php\" onmouseover=\"changeImages('messages', 'media/btns_menu/messages_on.gif')\" onmouseout=\"changeImages('messages', 'media/btns_menu/messages{$nav4}.gif')\" title=\"\"><img src=\"media/btns_menu/messages{$nav4}.gif\" name=\"messages\" alt=\"\" width=\"70\" height=\"18\"  border=\"0\" align=\"bottom\" /></a>";
	}
	
?></td>
<td align="right" valign="baseline"><?php 
	if ($_SESSION['admin_rights']) {
		echo "<a href=\"admin_locations.php\" onmouseover=\"changeImages('locations', 'media/btns_menu/locations_on.gif')\" onmouseout=\"changeImages('locations', 'media/btns_menu/locations{$nav7}.gif')\" title=\"\"><img src=\"media/btns_menu/locations{$nav7}.gif\" name=\"locations\" alt=\"\" width=\"65\" height=\"18\" border=\"0\" align=\"bottom\" /></a><img src=\"media/trans.gif\" width=\"2\" height=\"1\" border=\"0\" alt=\"\" />";
		echo "<a href=\"admin_users.php\" onmouseover=\"changeImages('users', 'media/btns_menu/users_on.gif')\" onmouseout=\"changeImages('users', 'media/btns_menu/users{$nav5}.gif')\" title=\"\"><img src=\"media/btns_menu/users{$nav5}.gif\" name=\"users\" alt=\"\" width=\"42\" height=\"18\" border=\"0\" align=\"bottom\" /></a><img src=\"media/trans.gif\" width=\"2\" height=\"1\" border=\"0\" alt=\"\" />";	
	}
	
?><a href="logout.php" onmouseover="changeImages('logout', 'media/btns_menu/logout_on.gif')" onmouseout="changeImages('logout', 'media/btns_menu/logout<?php echo $nav6;?>.gif')" title=""><img src="media/btns_menu/logout<?php echo $nav6;?>.gif" name="logout" alt="" width="49" height="18" border="0" align="bottom" /></a></td></tr>
</table>
<?php
	}
?>
