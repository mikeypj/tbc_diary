<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		event_equipment.php
//	Desc:		add/edit additional event data
//  Client:		BCD
//	Author:		Rob Curle
//	Date:		23 November 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////


		// equipment details //
		$dba = new db_mysql;
		
		if (!empty ($event_id)) {
			$dba->query("SELECT * FROM equipment WHERE event_id = {$event_id} LIMIT 1");
			$dba->next_record();
		}
?>
		<tr><td colspan="2"><strong>Equipment</strong><input name="equipment_id" type="hidden" value="<?php echo $dba->f("id");?>" /></td></tr>
		<tr><td class="row_title" valign="top">Provided by:</td><td class="row_value"><select name="equip_by" size="1"><option value="0"> </option><?php
			foreach ($hosted_by_abr as $id => $txt) {
				echo "<option value=\"$id\"";
				if (!isset($_POST['equip_by']) && $id==$dba->f("provided_by")) echo " selected='selected'";
				else if ($id==$_POST['equip_by']) echo " selected='selected'"; 
				echo "> $txt </option>";
			}
		?></select></td></tr>
		
		<tr><td class="row_title"> </td><td valign="top" class="row_value"><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><?php
		$cols = 3;
		$count = 0;
		foreach ($equipment as $bit => $txt) {
			if ($count == $cols) {
				echo "</tr><tr>";
				$count = 0;
			}
			
			echo "<td width=\"33%\"><input type=\"checkbox\" name=\"equipment[]\" value=\"{$bit}\"";
			if (is_array($_POST['equipment'])) {
				if (in_array($bit, $_POST['equipment'])) {
					echo " checked='checked'";
				}
			}
			else if (query_bit($dba->f("equipment"), $bit)) {
				echo " checked='checked'";
			}
			echo " /> $txt</td>";
			$count++;
		}		
		$remainder = $cols - $count;
		for ($i=0; $i < $remainder; $i++){
			echo "<td>&nbsp;</td>";
		}
		echo "</tr></table>\n";
?>
		</td></tr>
		<tr><td class="row_title" valign="top">Notes</td><td class="row_value"><textarea name="equip_notes" class="notes"><?php echo (isset($_POST['equip_notes']) ? $_POST['equip_notes']:$dba->f("equip_notes"));?></textarea></td></tr>
