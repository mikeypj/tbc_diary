<?php
// +------------------------------------------------------------+
// | Copyright (c) 2008 Mark Yasuda @ Manha                     |
// +------------------------------------------------------------+
// | THIS CODE IS PROTECTED BY COPYRIGHT LAW.                   |
// |                                                            |
// | Unauthorised re-use of this code is a breach of copyright. |
// | To request the use of the code herein, please contact:     |
// |                                           ---------------- |
// |                                            info@manha.com  |
// +------------------------------------------------------------+
// | Author:  Mark Yasuda <info@manha.com>                      |
// +------------------------------------------------------------+
// | Version: 1.0                                               |
// | Created: 17/09/2008                                        |
// +------------------------------------------------------------+
// | Version History                                            |
// |                                                            |
// |  1.0 - Original print view for events and exhibitions.     |
// +------------------------------------------------------------+
// | Known Bugs And Issues                                      |
// |                                                            |
// |  None.                                                     |
// +------------------------------------------------------------+


include("includes/startup.php");

$db = new db_mysql ;
$dbcontact = new db_mysql ;

if ( strlen( $_REQUEST['type'] ) > 0 ) {

  $type = $_REQUEST['type'] ;
  if ( !empty( $_REQUEST['id'] ) && is_numeric( $_REQUEST['id'] ) ) {

    $id = $_REQUEST['id'] ;
  	$db->query( "SELECT * FROM " . $type . " WHERE id = " . $id ) ;
  	$db->next_record();
  	if ( $db->num_rows() == 0 ) {

			redirect( $type . '.php?id=' . $id ) ;
  	}
  }
}

$locations = get_locations();

$createdAndModified = '<hr />
<table border="0" cellpadding="0" cellspacing="1">
<tr><td>Event created:&nbsp;</td><td>' . last_modified($db->f("created_date"), $db->f("created_by")) . '</td></tr>
<tr><td>Event updated:&nbsp;</td><td>' . last_modified($db->f("modified_date"), $db->f("modified_by")) . '</td></tr>
</table>' ;

include("includes/print_header.php");

if ( $type == 'events' ) {

  ?><h1><?= $db->f("event_title") ;?></h1>
  <div id="essentials">
  <?php

  if ( $db->f("status") > 0 ) {

    ?><div id="status"><?php
  	switch ($db->f("status")) {

  		case 1:
  		  echo 'Tentative' ;
  		  break;
  		case 3:
  		  echo 'Cancelled' ;
  		  break;
  	}
  	?></div><?php
  }

  ?>
  <table class="table_data">
    <tr>
      <td class="grey">Event id:</td>
      <td class="data"><?= $db->f("id") ;?></td>
    </tr>
    <tr>
      <td class="grey">Organised by:</td>
      <td class="data"><?= ( $db->f("hosted_by") == OTHER_IDX ) ? $db->f("hosted_other") : $hosted_by[$db->f("hosted_by")] ;?></td>
    </tr>
    <tr>
      <td class="grey">Location: </td>
      <td class="data"><?= $locations[$db->f("location")] ;?><?php

  if ( $db->f("location") == NLA_SEMINAR ) {

  	foreach( $nla_suite as $bit => $room ) {

  		if ( query_bit( $db->f("nla_suite") , $bit ) ) {

  			echo " " . $room ;
  		}
  	}
  }

      ?></td>
    </tr>
    <tr>
      <td class="grey">Date: </td>
      <td class="data"><?= format_longdate( $db->f("event_date") ) ;?></td>
    </tr>
    <tr>
      <td class="grey">Time: </td>
      <td class="data"><?= format_time($db->f("time_from")) . " - " . format_time($db->f("time_to")) ;?></td>
    </tr>
    <?php

  if ($db->f("period_from")!= null && $db->f("period_to")!= null) {

    ?><tr>
      <td class="grey">Period of occupation: </td>
      <td class="data"><?= format_time($db->f("period_from")) . " - " . format_time($db->f("period_to")) ;?></td>
    </tr>
    <?php
  }

  if ($db->f("num_people") > 0) {

    ?><tr>
      <td class="grey">Number of people: </td>
      <td class="data"><?= $db->f("num_people") ;?></td>
    </tr>
    <?php
  }
  ?>
  </table>
  </div>
  <?php

  // general notes //
  if ( $db->f('notes') != '' ) {

    ?><div id="notes">
  	<hr />
  	<h2>General Notes</h2>
  	<?= nl2br( $db->f('notes') ) ;?>
    </div><?php
  }

  // equipment details //
  if ( $db->f('equipment') != '' ) {

    ?><div id="equipment">
  	<hr />
  	<h2>Equipment</h2>
  	<?= nl2br( $db->f('equipment') ) ;?>
    </div><?php
  }

  // catering details //
  if ( $db->f('catering') != '' ) {

    ?><div id="catering">
  	<hr />
  	<h2>Catering</h2>
  	<?= nl2br( $db->f('catering') ) ;?>
    </div><?php
  }

  // security details //
  if ( $db->f('security') != '' ) {

    ?><div id="security">
  	<hr />
  	<h2>Security</h2>
  	<?= nl2br( $db->f('security') ) ;?>
    </div><?php
  }

/* OLD NOTES FORMATS */

	// setup details //
	$db->query("SELECT * FROM setup WHERE event_id = {$_REQUEST['id']}");
	if ($db->num_rows() > 0) {
		$db->next_record();

    ?><div id="setup">
  	<hr />
  	<h2>Setup</h2>
  	<?php
		if ($db->f("provided_by")) echo "<span class='provided_by_".$db->f("provided_by")."'>Provided by: ".$hosted_by_abr[$db->f("provided_by")]."</span><br />\n";
		if ($db->f("setup_time")) echo "<span class='grey'>Setup time: </span>".$setup_times[$db->f("setup_time")]."<br />\n";
		if ($db->f("layout")) echo "<span class='grey'>Layout: </span>".$db->f("layout")."<br />\n";
		if ($db->f("theme")) echo "<span class='grey'>Theme: </span>".$db->f("theme")."<br />\n";
		if ($db->f("setup_notes")) echo "<span class='grey'>Notes</span><br />".nl2br($db->f("setup_notes"))."<br />\n";
    ?>
    </div><?php

	}

	// equipment details //
	$db->query("SELECT * FROM equipment WHERE event_id = {$_REQUEST['id']}");
	if ($db->num_rows() > 0) {
		$db->next_record();

    ?><div id="equipment">
  	<hr />
  	<h2>Equipment</h2>
  	<?php
		if ($db->f("provided_by")) echo "<span class='provided_by_".$db->f("provided_by")."'>Provided by: ".$hosted_by_abr[$db->f("provided_by")]."</span><br />\n";
		if ($db->f("equipment") > 0) {
			foreach ($equipment as $bit => $txt) {
				if (query_bit($db->f("equipment"), $bit)) {
					echo " - {$txt}<br />\n";
				}
			}
		}
		if ($db->f("equip_notes")) echo "<span class='grey'>Notes</span><br />".nl2br($db->f("equip_notes"))."<br />\n";
    ?>
    </div><?php
	}

	// drinks details //
	$db->query("SELECT * FROM drinks WHERE event_id = {$_REQUEST['id']}");
	if ($db->num_rows() > 0) {
		$db->next_record();

    ?><div id="catering_drinks">
  	<hr />
  	<h2>Catering Drinks</h2>
  	<?php
		if ($db->f("provided_by")) echo "<span class='provided_by_".$db->f("provided_by")."'>Provided by: ".$hosted_by_abr[$db->f("provided_by")]."</span><br />\n";
		if ($db->f("coffee")) echo "<span class='grey'>Coffee: </span>".$db->f("coffee")."<br />\n";
		if ($db->f("tea")) echo "<span class='grey'>Tea: </span>".$db->f("tea")."<br />\n";
		if ($db->f("biscuits")) echo "<span class='grey'>Biscuits: </span>".$db->f("biscuits")."<br />\n";
		if ($db->f("orange")) echo "<span class='grey'>Orange juice: </span>".$db->f("orange")."<br />\n";
		if ($db->f("water")) echo "<span class='grey'>Water: </span>".$db->f("water")."<br />\n";
		if ($db->f("wine")) echo "<span class='grey'>Wine / Champagne: </span>".$db->f("wine")."<br />\n";
		if ($db->f("beer")) echo "<span class='grey'>Beer: </span>".$db->f("beer")."<br />\n";
		if ($db->f("drinks_cost")) echo "<span class='grey'>Cost: </span>&pound; ".number_format($db->f("drinks_cost"),2)."<br />\n";
		if ($db->f("drinks_notes")) echo "<span class='grey'>Notes</span><br />".nl2br($db->f("drinks_notes"))."<br />\n";
    ?>
    </div><?php
	}

	// catering details //
	$db->query("SELECT * FROM catering WHERE event_id = {$_REQUEST['id']}");
	if ($db->num_rows() > 0) {
		$db->next_record();

    ?><div id="catering_food">
  	<hr />
  	<h2>Catering Food</h2>
  	<?php
		if ($db->f("provided_by")) echo "<span class='provided_by_".$db->f("provided_by")."'>Provided by: ".$hosted_by_abr[$db->f("provided_by")]."</span><br />\n";
		if ($db->f("menu_1")) echo "- Menu ".$db->f("menu_1")." for ".$db->f("people_1")." people at ".$db->f("time_1")."<br />\n";
		if ($db->f("menu_2")) echo "- Menu ".$db->f("menu_2")." for ".$db->f("people_2")." people at ".$db->f("time_2")."<br />\n";
		if ($db->f("person_cost")) echo "<span class='grey'>Cost per person: </span>&pound; ".number_format($db->f("person_cost"),2)."<br />\n";
		if ($db->f("catering_cost")) echo "<span class='grey'>Total cost: </span>&pound; ".number_format($db->f("catering_cost"),2)."<br />\n";
		if ($db->f("catering_notes")) echo "<span class='grey'>Notes</span><br />".nl2br($db->f("catering_notes"))."<br />\n";
    ?>
    </div><?php
	}

	// staff details //
	$db->query("SELECT * FROM staff WHERE event_id = {$_REQUEST['id']}");
	if ($db->num_rows() > 0) {
		$db->next_record();

    ?><div id="catering_staff">
  	<hr />
  	<h2>Catering Staff</h2>
  	<?php
		if ($db->f("provided_by")) echo "<span class='provided_by_".$db->f("provided_by")."'>Provided by: ".$hosted_by_abr[$db->f("provided_by")]."</span><br />\n";
		if ($db->f("reg_attendants")) echo "<span class='grey'>Registration attendants: </span>".$db->f("reg_attendants")."<br />\n";
		if ($db->f("food_waiters")) echo "<span class='grey'>Food waiters: </span>".$db->f("food_waiters")."<br />\n";
		if ($db->f("drinks_waiters")) echo "<span class='grey'>Drinks waiters: </span>".$db->f("drinks_waiters")."<br />\n";
		if ($db->f("cloak_attendants")) echo "<span class='grey'>Cloakroom attendants: </span>".$db->f("cloak_attendants")."<br />\n";
		if ($db->f("staff_cost")) echo "<span class='grey'>Cost: </span>&pound; ".number_format($db->f("staff_cost"),2)."<br />\n";
		if ($db->f("staff_notes")) echo "<span class='grey'>Notes</span><br />".nl2br($db->f("staff_notes"))."<br />\n";
    ?>
    </div><?php
	}

	// security details //
	$db->query("SELECT * FROM security WHERE event_id = {$_REQUEST['id']}");
	if ($db->num_rows() > 0) {
		$db->next_record();

    ?><div id="security">
  	<hr />
  	<h2>Security</h2>
  	<?php
		if ($db->f("provided_by")) echo "<span class='provided_by_".$db->f("provided_by")."'>Provided by: ".$hosted_by_abr[$db->f("provided_by")]."</span><br />\n";
		if ($db->f("officers")) echo "<span class='grey'>Officers: </span>".$db->f("officers")."<br />\n";
		if ($db->f("security_cost")) echo "<span class='grey'>Cost: </span>&pound; ".number_format($db->f("security_cost"), 2)."<br />\n";
		if ($db->f("security_notes")) echo "<span class='grey'>Notes</span><br />".nl2br($db->f("security_notes"))."<br />\n";
    ?>
    </div><?php
	}

/*

	// payments details //
	if (!isset($_REQUEST['hp'])) {
		$db->query("SELECT *, IF (paid, 'Yes', 'No') AS paid_ans FROM payments WHERE event_id = {$_REQUEST['id']}");
		if ($db->num_rows() > 0) {
			echo "<p><img src=\"media/line_gry1.gif\" width=\"100%\" height=\"1\"border=\"0\" /></p>\n";
			echo "<p><strong>Payment</strong> <span class='grey'>[<a href=\"{$_SERVER['PHP_SELF']}?id={$_REQUEST['id']}&hp=1\">Hide</a>]</span><br />\n";

			while ($db->next_record()) {
				echo "<span class='grey'>Service:</span>".$db->f("service")."<span class='grey'> | Inv. by:</span>".$db->f("inv_by")." |</span> &pound; ".number_format($db->f("cost"),2)."<span class='grey'> | Paid:</span>".$db->f("paid_ans")."<span class='grey'> | Date:</span>".format_date($db->f("date"))."<br />\n";
			}

			echo "</p>\n";
		}
	}

*/


  // contact details //
  $dbcontact->query( 'SELECT * FROM contacts WHERE event_id = ' . $id ) ;
  if ( $dbcontact->num_rows() > 0 ) {

  	$dbcontact->next_record();
    ?><div id="contact">
  	<hr />
    <h2>Contact details</h2>
    <table class="table_data">
    <?php

  	if ($dbcontact->f("contact_name")) {

  	  ?><tr>
        <td class="grey">Name: </td>
        <td class="data"><?= $dbcontact->f("contact_name") ;?></td>
      </tr><?php
    }
  	if ($dbcontact->f("tel")) {

  	  ?><tr>
        <td class="grey">Tel: </td>
        <td class="data"><?= $dbcontact->f("tel") ;?></td>
      </tr><?php
    }
  	if ($dbcontact->f("fax")) {

  	  ?><tr>
        <td class="grey">Fax: </td>
        <td class="data"><?= $dbcontact->f("fax") ;?></td>
      </tr><?php
    }
  	if ($dbcontact->f("email")) {

  	  ?><tr>
        <td class="grey">Email: </td>
        <td class="data"><?= $dbcontact->f("email") ;?></td>
      </tr><?php
    }
  	if ($dbcontact->f("company")) {

  	  ?><tr>
        <td class="grey">Company name: </td>
        <td class="data"><?= $dbcontact->f("company") ;?></td>
      </tr><?php
    }
  	if ($dbcontact->f("address_1")) {

  	  ?><tr>
        <td class="grey">Address: </td>
        <td class="data"><?= $dbcontact->f("address_1")." ".$dbcontact->f("address_2") ;?></td>
      </tr><?php
    }
  	?>
    </table>
    </div><?php
  }

//or show exhibition
} else {

  ?><h1><?= $db->f("exhib_title") ;?></h1>
  <div id="essentials">
  <table class="table_data">
    <tr>
      <td class="grey">Exhibition id:</td>
      <td class="data"><?= $db->f("id") ;?></td>
    </tr>
    <tr>
      <td class="grey">Organised by:</td>
      <td class="data"><?= ( $db->f("hosted_by") == OTHER_IDX ) ? $db->f("hosted_other") : $hosted_by[$db->f("hosted_by")] ;?></td>
    </tr>
    <tr>
      <td class="grey">Location: </td>
      <td class="data"><?= $locations[$db->f("location")] ;?><?php

  if ( $db->f("location") == NLA_SEMINAR ) {

  	foreach( $nla_suite as $bit => $room ) {

  		if ( query_bit( $db->f("nla_suite") , $bit ) ) {

  			echo " " . $room ;
  		}
  	}
  }

      ?></td>
    </tr>
    <tr>
      <td class="grey">Date: </td>
      <td class="data"><?= format_longdate( $db->f("date_from") ) . " - " . format_longdate( $db->f("date_to") ) ;?></td>
    </tr>
  </table>
  </div>
  <?php

  // general notes //
  if ( $db->f('notes') != '' ) {

    ?><div id="notes">
  	<hr />
  	<h2>General Notes</h2>
  	<?= nl2br( $db->f('notes') ) ;?>
    </div><?php
  }
}


echo $createdAndModified ;

?>