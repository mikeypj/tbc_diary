<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		event_security.php
//	Desc:		add/edit additional event data
//  Client:		BCD
//	Author:		Rob Curle
//	Date:		23 November 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////


		// security details //
		$dba = new db_mysql;
		
		if (!empty ($event_id)) {
			$dba->query("SELECT * FROM security WHERE event_id = {$event_id} LIMIT 1");
			$dba->next_record();
		}
?>
		<tr><td colspan="2"><strong>Security</strong><input name="security_id" type="hidden" value="<?php echo $dba->f("id");?>" /></td></tr>
		<tr><td class="row_title" valign="top">Provided by:</td><td class="row_value"><select name="security_by" size="1"><option value="0"> </option><?php
			foreach ($hosted_by_abr as $id => $txt) {
				echo "<option value=\"$id\"";
				if (!isset($_POST['security_by']) && $id==$dba->f("provided_by")) echo " selected='selected'";
				else if ($id==$_POST['security_by']) echo " selected='selected'"; 
				echo "> $txt </option>";
			}
		?></select></td></tr>
		<tr><td class="row_title">Officers:</td><td class="row_value"><input name="officers" type="text" size="10" maxlength="5" value="<?php echo (isset($_POST['officers']) ? $_POST['officers']:$dba->f("officers"));?>" /></td></tr>
		<tr><td class="row_title">Total cost &pound;:</td><td class="row_value"><input type="text" name="security_cost" size="10" maxlength="5" onkeypress="return numerics(event)" value="<?php echo (isset($_POST['security_cost']) ? number_format($_POST['security_cost'], 2):number_format($dba->f("security_cost"), 2));?>" /></td></tr>
		<tr><td class="row_title" valign="top">Notes</td><td class="row_value"><textarea name="security_notes" class="notes"><?php echo (isset($_POST['security_notes']) ? $_POST['security_notes']:$dba->f("security_notes"));?></textarea></td></tr>
