<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		event_staff.php
//	Desc:		add/edit additional event data
//  Client:		BCD
//	Author:		Rob Curle
//	Date:		23 November 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////


		// staff details //
		$dba = new db_mysql;
		
		if (!empty ($event_id)) {
			$dba->query("SELECT * FROM staff WHERE event_id = {$event_id} LIMIT 1");
			$dba->next_record();
		}
?>
		<tr><td colspan="2"><strong>Catering: Staff</strong><input name="staff_id" type="hidden" value="<?php echo $dba->f("id");?>" /></td></tr>
		<tr><td class="row_title" valign="top">Provided by:</td><td class="row_value"><select name="staff_by" size="1"><option value="0"> </option><?php
			foreach ($hosted_by_abr as $id => $txt) {
				echo "<option value=\"$id\"";
				if (!isset($_POST['staff_by']) && $id==$dba->f("provided_by")) echo " selected='selected'";
				else if ($id==$_POST['staff_by']) echo " selected='selected'"; 
				echo "> $txt </option>";
			}
		?></select></td></tr>
		<tr><td class="row_title">Registration attendants:</td><td class="row_value"><input name="reg_attendants" type="text" size="10" maxlength="5" value="<?php echo (isset($_POST['reg_attendants']) ? $_POST['reg_attendants']:$dba->f("reg_attendants"));?>" /></td></tr>
		<tr><td class="row_title">Food waiters:</td><td class="row_value"><input name="food_waiters" type="text" size="10" maxlength="5"  value="<?php echo (isset($_POST['food_waiters']) ? $_POST['food_waiters']:$dba->f("food_waiters"));?>" /></td></tr>
		<tr><td class="row_title">Drinks waiters:</td><td class="row_value"><input name="drinks_waiters" type="text" size="10" maxlength="5"  value="<?php echo (isset($_POST['drinks_waiters']) ? $_POST['drinks_waiters']:$dba->f("drinks_waiters"));?>" /></td></tr>
		<tr><td class="row_title">Cloakroom attendants:</td><td class="row_value"><input name="cloak_attendants" type="text" size="10" maxlength="5" value="<?php echo (isset($_POST['cloak_attendants']) ? $_POST['cloak_attendants']:$dba->f("cloak_attendants"));?>" /></td></tr>
		<tr><td class="row_title">Total cost &pound;:</td><td class="row_value"><input type="text" name="staff_cost" size="10" maxlength="5" onkeypress="return numerics(event)" value="<?php echo (isset($_POST['staff_cost']) ? number_format($_POST['staff_cost'], 2):number_format($dba->f("staff_cost"), 2));?>" /></td></tr>
		<tr><td class="row_title" valign="top">Notes</td><td class="row_value"><textarea name="staff_notes" class="notes"><?php echo (isset($_POST['staff_notes']) ? $_POST['staff_notes']:$dba->f("staff_notes"));?></textarea></td></tr>
