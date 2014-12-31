<?php
//
//  billingReport.php is a PHP component of HPTTix
//  Copyright 2014 John Francis, HPT Solutions, Inc.  
//  Licensed under the Open Software License (OSL 3.0).
//  

function billingReport() {
	printTop();
	if (isset($_POST['billingMonth'])) {
		print "<h2> BILLING REPORT CALL ERROR 2. </h2>\n";
	} else {
		displayBillingForm();
	}
	print "</div>\n";
}

function displayBillingForm() {
	print "<div id='profile'><h5><a href='index.php?func=newpass'>Change Your Password</a></h5></div>\n";
	print "<div id='nav'><h5><a href='index.php'>Dashboard</a> | <a href='logout.php'>Logout</a></h5></div>\n";
	print "<br /><br /><br />";
	print "<h3>Billing Report</h3><br />";
	print "<div id=\"newList\">";
	// Get defaults to use
	$monthNames = array(1 => 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	$start = time();
	$currMonth = date('n', $start);
	$currYear = date('Y', $start);
	$lastMonth = $currMonth - 1;
	if ($lastMonth == 0) {
		$lastMonth = 12;
		$currYear--;
	}
	$lastYear = $currYear - 1;
	$nextYear = $currYear + 1;
	print "<form action=\"callBillRpt.php\" method=\"post\">\n";
	print "<table width=\"80%\" align=\"center\" cellpadding=\"2\" border=\"0\">\n";
	print "<tr><td><b>Billing Month: </b><select name=\"billingMonth\">";
	print "<option value=\"$lastMonth\">$monthNames[$lastMonth]</option>";
	for ($i = 1; $i <= 12; $i++) {
		if ($i <> $lastMonth) {
			print "<option value=\"$i\">$monthNames[$i]</option>";
		}
	}	
	print "</select></td><td><b>Billing Year: </b><select name=\"billingYear\">";
	print "<option value=\"$currYear\">$currYear</option>";
	print "<option value=\"$lastYear\">$lastYear</option>";
	print "<option value=\"$nextYear\">$nextYear</option></select>";
	print "</td></tr><tr><td colspan=\"2\">";
#
#  We will only print summary reports for now.  Detail report = activity level detail
#
#	print "<input type=\"radio\" name=\"detailFlag\" value=\"0\" /> Summary ";
#	print "<input type=\"radio\" name=\"detailFlag\" value=\"1\" checked /> Details </td></tr>";
	print "<input type=\"hidden\" name=\"detailFlag\" value=\"0\" />";
#
	print "<tr><td colspan=\"2\"><input type=\"Submit\" value=\"Create Billing Report\">";
	print "</td></tr></table></form>\n";
}

?>
