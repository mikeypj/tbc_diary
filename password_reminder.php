<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		password_reminder.php
//	Desc:		email password to registered user
//  Client:		BCD
//	Author:		Rob Curle
//	Date:		13 June 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////

	include("includes/startup.php");
	include("includes/email.php");
	
	$db = new db_mysql;
	$thankyou = false;
	$msg = 0;
	
	if (!empty($_POST['email'])) {
		$msg = send_password();
		if (empty($msg)) {
			$thankyou = true;
		}
	}

	$hide_nav = true;
	include("includes/html_header.php");
?>
<table width="750" border="0" cellpadding="7" cellspacing="0">
<tr><td width="350" valign="top" class="content">
<?php
	if ($thankyou) {
		echo "<p>Your login details have been emailed to you.</p>";
		echo "<p><a href=\"index.php\"><img src=\"media/btns/ok.gif\" width=\"26\" height=\"18\" border=\"0\" /></a></p>";
	}
	else {
?>
<p>Please enter your registered email address.</p>

<?php
	if (!empty($msg)) {
		echo "<p class=\"error\">$msg</p>\n";
	}
?>
<form name="userdata" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<table border="0" cellpadding="3" cellspacing="1" class="frame">
<tr><td class="row_title">Email</td><td class="row_value"><input type="text" name="email" size="25" value="<?php echo $_POST['email'];?>" /></td>
<tr><td colspan="2" class="row_title" align="right"><input type="image" src="media/btns/ok.gif" width="26" height="18" name="send" border="0" /> <a href="index.php"><img src="media/btns/back.gif" width="42" height="18" border="0" /></a></td></tr>
</table>
</form>
<?php
	}
?>
</td>
<td class="content_right">&nbsp;</td></tr>
</table>
<?php
	include("includes/html_footer.php");
	
///////////////////////////////////////////////////////////////////////////////////////

	function send_password() {
		$db = new db_mysql;
		$msg = 0;
		
		if (!validate_email($_POST['email'])) {
			$msg = "The Email address you provided is not valid.";
		}
		else {
			$db->query("SELECT username, password, name FROM admin_users WHERE email=\"{$_POST['email']}\"");
			if ($db->num_rows() > 0) {
				$db->next_record();
			}
			else {
				$msg = "Sorry, your Email address was not found.";
			}
		}
		
		if (empty($msg)) {
			$txt .= "Dear " .$db->f("name"). ",\n\n";
			$txt .= "As requested, here are your login details.\n\n";
			
			$html = $txt . "<table border=\"0\" cellpadding=\"2\" cellspacing=\"3\">";
			$html .= "<tr><td bgcolor=\"#EEEEEE\"><font color=\"#333333\" size=\"1\" face=\"Verdana\">Username:</font></td><td><font color=\"#333333\" size=\"1\" face=\"Verdana\"><b>" .$db->f("username"). "</b></font></td></tr>";
			$html .= "<tr><td bgcolor=\"#EEEEEE\"><font color=\"#333333\" size=\"1\" face=\"Verdana\">Password:</font></td><td><font color=\"#333333\" size=\"1\" face=\"Verdana\"><b>" .$db->f("password"). "</b></font></td></tr>";
			$html .= "</table><br/>";
			$html .= "Thanks,\nBCD Team\n<a href=\"http://" . SITE_URL . "\">" . SITE_URL . "</a>\n\n";
			
			$txt .= "Username: " .$db->f("username"). "\n";
			$txt .= "Password: " .$db->f("password"). "\n";
			$txt .= "Kind regards,\nLAD Team\n" . SITE_URL . "\n\n";
			
			if (!send_email($_POST['email'], "Building Centre Diary: Login details", $txt, $html)) {
				$msg = "Sorry, we are unable to email your details at the moment.";
			}
		}
		
		return $msg;
	}
?>
