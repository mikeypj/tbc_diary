<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		event_drinks.php
//	Desc:		add/edit additional event data
//  Client:		BCD
//	Author:		Rob Curle
//	Date:		23 November 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////


		// drinks details //
		$dba = new db_mysql;
		
		if (!empty ($event_id)) {
			$dba->query("SELECT * FROM drinks WHERE event_id = {$event_id} LIMIT 1");
			$dba->next_record();
		}
?>
		<tr><td colspan="2"><strong>Catering: Drinks</strong><input name="drinks_id" type="hidden" value="<?php echo $dba->f("id");?>" /></td></tr>
		<tr><td class="row_title" valign="top">Provided by:</td><td class="row_value"><select name="drinks_by" size="1"><option value="0"> </option><?php
			foreach ($hosted_by_abr as $id => $txt) {
				echo "<option value=\"$id\"";
				if (!isset($_POST['drinks_by']) && $id==$dba->f("provided_by")) echo " selected='selected'";
				else if ($id==$_POST['drinks_by']) echo " selected='selected'"; 
				echo "> $txt </option>";
			}
		?></select></td></tr>
		
		<tr><td class="row_title">Coffee:</td><td class="row_value"><input name="coffee" type="text" size="45" maxlength="255" value="<?php echo (isset($_POST['coffee']) ? $_POST['coffee']:$dba->f("coffee"));?>" /></td></tr>
		<tr><td class="row_title">Tea:</td><td class="row_value"><input name="tea" type="text" size="45" maxlength="255" value="<?php echo (isset($_POST['tea']) ? $_POST['tea']:$dba->f("tea"));?>" /></td></tr>
		<tr><td class="row_title">Biscuits:</td><td class="row_value"><input name="biscuits" type="text" size="45" maxlength="255" value="<?php echo (isset($_POST['biscuits']) ? $_POST['biscuits']:$dba->f("biscuits"));?>" /></td></tr>
		<tr><td class="row_title">Orange juice:</td><td class="row_value"><input name="orange" type="text" size="45" maxlength="255" value="<?php echo (isset($_POST['orange']) ? $_POST['orange']:$dba->f("orange"));?>" /></td></tr>
		<tr><td class="row_title">Water:</td><td class="row_value"><input name="water" type="text" size="45" maxlength="255" value="<?php echo (isset($_POST['water']) ? $_POST['water']:$dba->f("water"));?>" /></td></tr>
		<tr><td class="row_title">Wine / Champagne:</td><td class="row_value"><input name="wine" type="text" size="45" maxlength="255" value="<?php echo (isset($_POST['wine']) ? $_POST['wine']:$dba->f("wine"));?>" /></td></tr>
		<tr><td class="row_title">Beer:</td><td class="row_value"><input name="beer" type="text" size="45" maxlength="255" value="<?php echo (isset($_POST['beer']) ? $_POST['beer']:$dba->f("beer"));?>" /></td></tr>
		<tr><td class="row_title">Total cost:</td><td class="row_value">&pound; <input type="text" name="drinks_cost" size="10" maxlength="6" onkeypress="return numerics(event)" value="<?php echo (isset($_POST['drinks_cost']) ? number_format($_POST['drinks_cost'], 2):number_format($dba->f("drinks_cost"), 2));?>" /></td></tr>
		<tr><td class="row_title" valign="top">Notes</td><td class="row_value"><textarea name="drinks_notes" class="notes"><?php echo (isset($_POST['drinks_notes']) ? $_POST['drinks_notes']:$dba->f("drinks_notes"));?></textarea></td></tr>
