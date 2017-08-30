<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		email.php
//	Desc:		general function for sending emails
//  Client:		NLA
//	Author:		Rob Curle
//	Date:		28 May 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////
	
	include_once ("includes/html_mime_mail.php");
	
	
	function send_edit_notifiy_emails($url, $title, $type, $action, $who) {
		$db = new db_mysql;
		
		$db->query("SELECT id, email FROM admin_users WHERE email_notify = 1");
		while ($db->next_record()) {
			if ($db->f("id") == $_SESSION['admin_id']) continue;	// don't add the current user into the list
			$email[] = $db->f("email");
		}
		
		$txt = "{$who} has just {$action} the following {$type}:\n\n";
		$txt .= addslashes($title) . "\n\n";
		$body = $txt . "Please <a href=\"http://#SERVER#/index.php?url={$url}\">click here to view it</a>.\n\n";
		$txt .= "Please click here to view it: http://#SERVER#/index.php?url={$url}\n\n";
	
		send_email($email, "BCD: {$type} {$action}", $txt, $body, $admin);
	}
	
	function send_email($to, $subject, $txt, $body, $admin=false) {
		$mail = new htmlMimeMail();
		
		$from = SITE_EMAIL_NAME. " <".SITE_EMAIL.">";		
		
		$text = $txt;
		$text .= "\n\n--------------------------------------------\n" . SITE_URL;
				
		$html = get_html($body, $admin);
		
		$mail->setHtml($html, $text);
		$mail->setFrom($from);
		$mail->setSubject($subject);
		$mail->setHeader("X-Mailer", SITE_EMAIL_NAME);
		
		$email = $to;
		if  (!is_array($email)) $email=array($email);
		return $mail->send($email);
	}
	
	
	function admin_send_mailshot($list, $subject, $txt, $html) {	
		$mail = new htmlMimeMail();
		
		$from = SITE_EMAIL_NAME. " <".SITE_EMAIL.">";		
		
		$text = stripslashes($txt);
		$text .= "\n\n--------------------------------------------\n" . SITE_URL;
		
		$mail->setHtml($html, $text);
		$mail->setFrom($from);
		$mail->setSubject(stripslashes($subject));
		$mail->setBcc($list);
		$mail->setHeader("X-Mailer", SITE_EMAIL_NAME);
		
		return $mail->send(array($from));
	}
	
	function get_html($body, $admin=false) {
		$mail = new htmlMimeMail();

		$path = ($admin ? "../":"");
		$html = $mail->getFile($path."email_template.html");
		$html = str_replace("#CONTENT#", nl2br($body), $html);
		$html = str_replace("#SERVER#", SITE_URL, $html);
		
		return $html;
	}
?>