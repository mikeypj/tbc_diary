<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		db_backup.php
//	Desc:		dumps a zip of the database
//	Author:		Rob Curle
//	Date:		9 Feburary 2006
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////

	include("includes/startup.php");
		
	if (!$_SESSION['admin_rights']) {
		msg("Access Denied", "Sorry you do not have access to this page.", HOME);
	}
	
	$path = dirname(__FILE__);
	$filename = DB_NAME . "-" . date("d-m-y").".sql.gzip";
	
	if (system("mysqldump --opt -h".DB_HOST." -u".DB_USER." -p".DB_PWD." ".DB_NAME." | gzip > {$path}/{$filename}", $sys_error)) {
		if ($sys_error) {
			show_error($sys_error);
		}
		else {
			show_error("Can not start the backup.");
		}
	}
	

	// Send headers //
	header ("Content-Type: application/x-gzip");
	header ("Content-Disposition: attachment; filename=" . $filename);
	header('Content-Length: '. filesize($path."/".$filename));
	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
	header ("Cache-Control: no-store, no-cache, must-revalidate");  
	header ("Pragma: no-cache");
	header ("Expires: 0");
	
	// send file //
	readfile ($path."/".$filename);
	
	function show_error($error) {
		include("includes/html_header.php");
?>	
		<table width="750" border="0" cellpadding="7" cellspacing="0">
		<tr><td valign="middle" class="title_bar">Database backed failed</td><tr>
		<tr><td width="750" valign="top" class="content">
		<p>Sorry, the following error occured whilst backing up the database <?=DB_NAME;?></p>
		<p><?=$error;?></p>
		</td></tr>
		</table>
<?php
		include("includes/html_footer.php");
		exit;
	}
?>