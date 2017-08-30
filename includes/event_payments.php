<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		event_payments.php
//	Desc:		add/edit additional event data
//  Client:		BCD
//	Author:		Rob Curle
//	Date:		23 November 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////

		// payment details //
		$dba = new db_mysql;
		
		if (!empty ($event_id)) {
			$dba->query("SELECT * FROM payments WHERE event_id = {$event_id}");
		}
?>
		<tr><td colspan="2"><strong>Payment</strong></td></tr>
		<tr><td class="row_title" valign="top">Details:</td><td valign="middle" class="row_value"><table border="0" cellpadding="2" cellspacing="0">
<?php
		$i = 1;
		if ($dba->num_rows() > 0) {
			while ($dba->next_record()) {
				echo write_row($i, $dba->f("id"), $dba->f("service"), $dba->f("inv_by"), $dba->f("cost"), $dba->f("paid"), $dba->f("date"));
				$i++;
			}
		}
		
		for (;$i<6; $i++) {		// add remainder upto 5 rows
			echo write_row($i);
		}
		
?>
		<tr><td colspan="9"><a href="javascript:change_action('ap');">Add a Payment</a><input name="payment_num" type="hidden" value="<?=$i;?>" /></td></tr>
		</table></td></tr>
<?php
	
///////////////////////////////////////////////////////////////////////////////////////

	function write_row($row, $id='', $service='', $inv_by='', $cost=0, $paid=0, $date='') {
		$html = "<tr><td>Service </td><td><input name=\"service_{$row}\" type=\"text\" size=\"15\" maxlength=\"30\" value=\"".(isset($_POST["service_{$row}"]) ? $_POST["service_{$row}"]:$service)."\" /></td>";
		$html .= "<td> Inv. by </td><td><input name=\"inv_by_{$row}\" type=\"text\" size=\"15\" maxlength=\"30\" value=\"".(isset($_POST["inv_by_{$row}"]) ? $_POST["inv_by_{$row}"]:$inv_by)."\" /></td>";
		$html .= "<td> &pound; <input name=\"cost_{$row}\" type=\"text\" size=\"5\" maxlength=\"30\" onkeypress=\"return numerics(event)\" value=\"".(isset($_POST["cost_{$row}"]) ? $_POST["cost_{$row}"]:$cost)."\" /></td>";
		$html .= "<td> Paid </td><td><input name=\"paid_{$row}\" type=\"checkbox\" value=\"1\"".($_POST["paid_{$row}"] ? " checked='checked'":($paid ? " checked='checked'":""))." /></td>";
		$html .= "<td> Date </td><td>" . get_date_ctl("date_{$row}", $date) . "<input name=\"pay_id_{$row}\" type=\"hidden\" value=\"{$id}\" /></td></tr>\n";
		
		return $html;
	}
?>