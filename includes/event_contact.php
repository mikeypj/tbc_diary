<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		event_contact.php
//	Desc:		add/edit additional event data
//  Client:		BCD
//	Author:		Rob Curle
//	Date:		23 November 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////

		// contact details //
		$dba = new db_mysql;

		if (!empty ($event_id)) {
			$dba->query("SELECT * FROM contacts WHERE event_id = {$event_id} LIMIT 1");
			$dba->next_record();
		}
?>
		<tr><td colspan="2"><strong>Contact Details</strong><input name="contact_id" type="hidden" value="<?php echo $dba->f("id");?>" /></td></tr>
		<tr><td class="row_title">Name:</td><td class="row_value"><input type="text" name="contact_name" size="45" maxlength="40" value="<?php echo (isset($_POST['contact_name']) ? $_POST['contact_name']:$dba->f("contact_name"));?>" /></td></tr>
		<tr><td class="row_title">Company name:</td><td class="row_value"><input name="company" type="text" maxlength="100" size="45" value="<?php echo (isset($_POST['company']) ? $_POST['company']:$dba->f("company"));?>" /></td></tr>
		<tr><td class="row_title">Tel:</td><td class="row_value"><input type="text" name="tel" size="45" maxlength="30" value="<?php echo (isset($_POST['tel']) ? $_POST['tel']:$dba->f("tel"));?>" /></td></tr>
		<tr><td class="row_title">Fax:</td><td class="row_value"><input type="text" name="fax" size="45" maxlength="30" value="<?php echo (isset($_POST['fax']) ? $_POST['fax']:$dba->f("fax"));?>" /></td></tr>
		<tr><td class="row_title">Email:</td><td class="row_value"><input type="text" name="email" size="45" maxlength="255" value="<?php echo (isset($_POST['email']) ? $_POST['email']:$dba->f("email"));?>" /></td></tr>
		<tr><td class="row_title" valign="top">Address:</td><td class="row_value"><input name="address_1" type="text" size="45" maxlength="255" value="<?php echo (isset($_POST['address_1']) ? $_POST['address_1']:$dba->f("address_1"));?>" /><br />
		<input name="address_2" type="text" size="45" maxlength="255" value="<?php echo (isset($_POST['address_2']) ? $_POST['address_2']:$dba->f("address_2"));?>" /></td></tr>
<!--
		<tr><td class="row_title">NLA partner:</td><td class="row_value"><input name="nla_partner" type="radio" value="1"<?php echo ($dba->f("nla_partner") ? " checked='checked'":"");?> /> Yes <input name="nla_partner" type="radio" value="0"<?php echo (!$dba->f("nla_partner") ? " checked='checked'":"");?> /> No</td></tr>
		<tr><td class="row_title" valign="top">Notes</td><td class="row_value"><textarea name="contact_notes" class="notes"><?php echo (isset($_POST['contact_notes']) ? $_POST['contact_notes']:$dba->f("contact_notes"));?></textarea></td></tr>
-->