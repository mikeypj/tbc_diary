<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		event_setup.php
//	Desc:		add/edit additional event data
//  Client:		BCD
//	Author:		Rob Curle
//	Date:		23 November 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////


		// setup details //
		$dba = new db_mysql;
		
		if (!empty ($event_id)) {
			$dba->query("SELECT * FROM setup WHERE event_id = {$event_id} LIMIT 1");
			$dba->next_record();
		}
?>
		<tr><td colspan="2"><strong>Setup</strong><input name="setup_id" type="hidden" value="<?php echo $dba->f("id");?>" /></td></tr>
		<tr><td class="row_title" valign="top">Provided by:</td><td class="row_value"><select name="setup_by" size="1"><option value="0"> </option><?php
			foreach ($hosted_by_abr as $id => $txt) {
				echo "<option value=\"$id\"";
				if (!isset($_POST['setup_by']) && $id==$dba->f("provided_by")) echo " selected='selected'";
				else if ($id==$_POST['setup_by']) echo " selected='selected'"; 
				echo "> $txt </option>";
			}
		?></select></td></tr>
		<tr><td class="row_title" valign="top">Setup time:</td><td class="row_value"><select name="setup_time" size="1"><option value="0"> </option><?php
			foreach ($setup_times as $id => $txt) {
				echo "<option value=\"$id\"";
				if (!isset($_POST['setup_time']) && $id==$dba->f("setup_time")) echo " selected='selected'";
				else if ($id==$_POST['setup_time']) echo " selected='selected'"; 
				echo "> $txt </option>";
			}
		?></select></td></tr>
		<tr><td class="row_title">Layout:</td><td class="row_value"><input name="layout" type="text" size="45" maxlength="255" value="<?php echo (isset($_POST['layout']) ? $_POST['layout']:$dba->f("layout"));?>" /></td></tr>
		<tr><td class="row_title">Theme style:</td><td class="row_value"><input name="theme" type="text" size="45" maxlength="255" value="<?php echo (isset($_POST['theme']) ? $_POST['theme']:$dba->f("theme"));?>" /></td></tr>
		<tr><td class="row_title" valign="top">Notes</td><td class="row_value"><textarea name="setup_notes" class="notes"><?php echo (isset($_POST['setup_notes']) ? $_POST['setup_notes']:$dba->f("setup_notes"));?></textarea></td></tr>
