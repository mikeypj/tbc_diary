<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		event_catering.php
//	Desc:		add/edit additional event data
//  Client:		BCD
//	Author:		Rob Curle
//	Date:		23 November 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////

		// food details //
		$dba = new db_mysql;
		
		if (!empty ($event_id)) {
			$dba->query("SELECT * FROM catering WHERE event_id = {$event_id} LIMIT 1");
			$dba->next_record();
		}
?>
		<tr><td colspan="2"><strong>Catering: Food</strong><input name="catering_id" type="hidden" value="<?php echo $dba->f("id");?>" /></td></tr>
		<tr><td class="row_title" valign="top">Provided by:</td><td class="row_value"><select name="catering_by" size="1"><option value="0"> </option><?php
			foreach ($hosted_by_abr as $id => $txt) {
				echo "<option value=\"$id\"";
				if (!isset($_POST['catering_by']) && $id==$dba->f("provided_by")) echo " selected='selected'";
				else if ($id==$_POST['catering_by']) echo " selected='selected'"; 
				echo "> $txt </option>";
			}
		?></select></td></tr>
		
		<tr><td class="row_title">Set Menus:</td><td valign="middle" class="row_value"><table border="0" cellpadding="2" cellspacing="0">
		<tr><td>Menu <input name="menu_1" type="text" size="15" maxlength="30" value="<?php echo (isset($_POST['menu_1']) ? $_POST['menu_1']:$dba->f("menu_1"));?>" /></td><td> for <input name="people_1" type="text" size="5" maxlength="3" onkeypress="return numerics(event)" value="<?php echo (isset($_POST['people_1']) ? $_POST['people_1']:$dba->f("people_1"));?>" /></td><td> people at <?php echo get_time_ctl ("time_1", $dba->f("time_1"));?></td></tr>
		<tr><td>Menu <input name="menu_2" type="text" size="15" maxlength="30" value="<?php echo (isset($_POST['menu_2']) ? $_POST['menu_2']:$dba->f("menu_2"));?>" /></td><td> for <input name="people_2" type="text" size="5" maxlength="3" onkeypress="return numerics(event)" value="<?php echo (isset($_POST['people_2']) ? $_POST['people_2']:$dba->f("people_2"));?>" /></td><td> people at <?php echo get_time_ctl ("time_2", $dba->f("time_2"));?></td></tr>
		</table></td></tr>
		
		<tr><td class="row_title" valign="top">Custom menu / Notes:</td><td class="row_value"><textarea name="catering_notes" class="notes"><?php echo (isset($_POST['catering_notes']) ? $_POST['catering_notes']:$dba->f("catering_notes"));?></textarea></td></tr>
		
		<tr><td class="row_title">Cost per person &pound;:</td><td class="row_value"><input type="text" name="person_cost" size="10" value="<?php echo (isset($_POST['person_cost']) ? number_format($_POST['person_cost'], 2):number_format($dba->f("person_cost"), 2));?>" onkeypress="return numerics(event)" /></td></tr>
		<tr><td class="row_title">Total cost &pound;:</td><td class="row_value"><input type="text" name="catering_cost" size="10" value="<?php echo (isset($_POST['catering_cost']) ? number_format($_POST['catering_cost'], 2):number_format($dba->f("catering_cost"), 2));?>" onkeypress="return numerics(event)" /></td></tr>		
