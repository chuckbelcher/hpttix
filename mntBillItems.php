<?php

$thing = "Billing Item";
$things = "Billing Items";
$passPage = 1;
$cust = 0;
$monthName = array(1 => 'Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
$currYear = date('Y');
$recurName["O"] = "One Time Only";
$recurName["A"] = "Annually";
$recurName["Q"] = "Quarterly";
$recurName["M"] = "Monthly";

function mntBillItems() {
	global $thing, $things, $passPage, $cust;
	if (isset($_GET['page'])) {
		$passPage = $_GET['page'];
	}
	if (isset($_GET['cust'])) {
		$cust = $_GET['cust'];
	}
	if (isset($_POST['cust'])) {
		$cust = $_POST['cust'];
	}
	printTop();
	print "<div id='profile'><h5><a href='index.php?func=newpass'>Change Your Password</a></h5></div>\n";
	print "<div id='nav'><h5><a href='index.php?func=mntCustomers&page=$passPage'>Cust. Maint.</a> | <a href='logout.php'>Logout</a></h5></div>\n";
	print "<br /><br /><br />";
	print "<h3>Maintain $things</h3><br />";
	include ("dbconn.php");
	$sql = "SELECT name FROM customers WHERE id = $cust";
	$result = mysql_query($sql);
	if ($result) {
		$row = mysql_fetch_array($result);	
		$custName = $row["name"];
	} else {
		$custName = "Unknown Customer";
	}
	print "<h4>for $custName</h4><br />";
	print "<div id=\"incList\">";
	if (isset($_POST['thing'])) {
		if (validInsert()) {
			insertThing();
			echo "<br /><div id='msg'><h4>New $thing Added</h4></div><br /><br /> \n";
		} else {
			echo "<br /><div id='msg'><h4>Can not add - required fields missing</h4><br /><br /> \n";
		}
		displayThingForm();
		displayThings();
	} elseif (isset($_GET['editThing'])) {
		editThingForm();
	} elseif (isset($_POST['ething'])) { 
		if (validUpdate()) {
			editUpdateThing();
			echo "<br /><div id='msg'><h4>$thing Updated</h4></div><br /><br /> \n";
		} else {
			echo "<br /><div id='msg'><h4>Can not update - required fields missing</h4><br /><br /> \n";
		}
		displayThingForm();
		displayThings();
	} else {
		displayThingForm();
		displayThings(); 
	}
	print "</div>\n";
}

function editUpdateThing() {
	global $cust;
	$id = $_POST['ething'];
// THE NAMES PAST HERE MUST BE CHANGED TO FIT THE PARTICULAR ROUTINE.  DON'T FORGET FUNC NAMES, ETC.
	$desc = escQuotes(trim($_POST['edesc']));
	$amount = dollars($_POST['eamount']);
	$startMonth = $_POST['estartMonth'];
	$startDay = $_POST['estartDay'];
	$startYear = $_POST['estartYear'];
	$startDate = mktime(0,1,0,$startMonth,$startDay,$startYear);
	$stopMonth = $_POST['estopMonth'];
	$stopDay = $_POST['estopDay'];
	$stopYear = $_POST['estopYear'];
	$recur = $_POST['erecur'];
	if ($recur == "O") {
		$stopDate = 0;
	} else {
		$stopDate = mktime(0,1,0,$stopMonth,$stopDay,$stopYear);
	}
	// Connect to database
	include('dbconn.php');
// CHANGE TO FIT PARTICULAR TABLE. Post edited item to database
	$sql = "UPDATE recurringItems SET cust = $cust, `desc` = '$desc', amount = $amount, startDate = $startDate, ";
	$sql .= "stopDate = $stopDate, recur = '$recur' ";
	$sql .= "WHERE id = $id";
	$result = mysql_query($sql);
	// Close database connection and unset eitem
	mysql_close($conn);
	unset($_POST['ething']);
	recalcItems($id);
}

function validUpdate() {
	$desc = escQuotes(trim($_POST['edesc']));
	$amount = dollars($_POST['eamount']);
	$startMonth = $_POST['estartMonth'];
	$startDay = $_POST['estartDay'];
	$startYear = $_POST['estartYear'];
	$stopMonth = $_POST['estopMonth'];
	$stopDay = $_POST['estopDay'];
	$stopYear = $_POST['estopYear'];
	$recur = $_POST['erecur'];
	if ($desc == "") {
		return FALSE;
	}
	if ($recur == "O") {
		return TRUE;
	}
	if (($stopMonth == 0) || ($stopDay == 0) || ($stopYear == 0)) {
		return FALSE;
	}
	$startDate = mktime(0,0,0,$startMonth,$startDay,$startYear);
	$stopDate = mktime(0,0,0,$stopMonth,$stopDay,$stopYear);
	if ($stopDate < $startDate) {
		return FALSE;
	}
	return TRUE;
}

function editThingForm() {
	global $thing, $things, $monthName, $recurName, $currYear, $cust, $passPage;
	$id = $_GET['editThing'];
	// Connect to database
	include('dbconn.php');
// THE NAMES PAST HERE MUST BE CHANGED TO FIT THE PARTICULAR ROUTINE.  DON'T FORGET FUNC NAMES, ETC.
	$query = "SELECT `desc`, amount, startDate, stopDate, recur ";
	$query .= " FROM recurringItems where id = $id";
	$result = mysql_query($query);
	if ($result) {
		$row = mysql_fetch_array($result);	
		$desc = $row["desc"];
		$amount = $row["amount"];
		$recur = $row["recur"];
		$startDate = $row["startDate"];
		$startMonth = date('n',$startDate);
		$startDay = date('d',$startDate);
		$startYear = date('Y',$startDate);
		$stopDate = $row["stopDate"];
		if ($stopDate <> 0) {
			$stopMonth = date('n',$stopDate);
			$stopDay = date('d',$stopDate);
			$stopYear = date('Y',$stopDate);
		} else {
			$stopMonth = "";
			$stopDay = "";
			$stopYear = "";
		}
		print "<form action=\"index.php?func=mntBillItems&page=$passPage&cust=$cust\" method=\"post\">\n";
		print "<table width=\"80%\" align=\"center\">";
		print "<tr><td align=\"right\">$thing: </td>";
		print "<td align=\"left\"><input type=\"text\" size=\"50\" name=\"edesc\" value=\"$desc\"</td></tr>\n";
		print "<tr><td align=\"right\">Amount: </td>";
		print "<td align=\"left\"><input type=\"text\" size=\"30\" name=\"eamount\" value=\"$amount\"</td></tr>\n";
		print "<tr><td align=\"right\">Start Date: </td>";
		print "<td align=\"left\"><select name=\"estartMonth\">";
		print "<option value=\"$startMonth\">$monthName[$startMonth]</option>";
		for ($i = 1; $i <= 12; $i++) {
			if ($i <> $startMonth) {
				print "<option value=\"$i\">$monthName[$i]</option>";
			}
		}
		print "</select> &nbsp &nbsp <select name=\"estartDay\">";
		print "<option value=\"$startDay\">$startDay</option>";
		for ($i = 1; $i <= 31; $i++) {
			if ($i <> $startDay) {
				print "<option value=\"$i\">$i</option>";
			}
		}
		print "</select> &nbsp &nbsp <select name=\"estartYear\">";
		print "<option value=\"$startYear\">$startYear</option>";
		for ($i = $currYear - 2; $i <= $currYear + 2; $i++) {
			if ($i <> $startYear) {
				print "<option value=\"$i\">$i</option>";
			}
		}
		print "</select></td></tr>";
		print "<tr><td align=\"right\">Stop Date: </td>";
		print "<td align=\"left\"><select name=\"estopMonth\">";
		print "<option value=\"$stopMonth\">$monthName[$stopMonth]</option>";
		for ($i = 1; $i <= 12; $i++) {
			if ($i <> $stopMonth) {
				print "<option value=\"$i\">$monthName[$i]</option>";
			}
		}
		print "</select> &nbsp &nbsp <select name=\"estopDay\">";
		print "<option value=\"$stopDay\">$stopDay</option>";
		for ($i = 1; $i <= 31; $i++) {
			if ($i <> $stopDay) {
				print "<option value=\"$i\">$i</option>";
			}
		}
		print "</select> &nbsp &nbsp <select name=\"estopYear\">";
		print "<option value=\"$stopYear\">$stopYear</option>";
		for ($i = $currYear - 2; $i <= $currYear + 5; $i++) {
			if ($i <> $stopYear) {
				print "<option value=\"$i\">$i</option>";
			}
		}
		print "<option value=\"2020\">2020</option>";
		print "</select></td></tr>";
		print "<tr><td align=\"right\">Recurs: </td>";
		print "<td align=\"left\"><select name=\"erecur\">";
		print "<option value=\"$recur\">$recurName[$recur]</option>";
		foreach ($recurName as $key => $value) {
			if ($key <> $recur) {
				print "<option value=\"$key\">$value</option>";
			}
		}
		print "</select></td></tr>";
		print "</table>";
		print "<input type=\"Submit\" value=\"Update $thing\">";
		print "<input type=\"hidden\"  name=\"ething\" value=\"$id\">";
		print "</form>"; 
	}
}
					
function displayThings() {
	global $thing, $things, $cust, $passPage, $recurName;
	// Connect to database
	include('dbconn.php');
	// Select data by section and display it
	print "<table width=\"100%\" align=\"center\" cellpadding=\"0\" border=\"1\">\n";
	print "<tr><th>$thing</th><th>Amount</th><th>Start Date</th><th>Stop Date</th><th>Recurs</th></tr>\n";
	$query = "SELECT id, `desc`, amount, startDate, stopDate, recur ";
	$query .= " FROM recurringItems ";
	$query .= " WHERE cust = $cust ";
	$query .= " ORDER BY startDate";
	$result = mysql_query($query);
	if ($result) {
		while ($row = mysql_fetch_array($result)) {
			$id = $row["id"];
			$desc = $row["desc"];
			$amount = $row["amount"];
			$fmtAmount = sprintf("$ %6.2f",$amount);
			$recur = $row["recur"];
			$startDate = $row["startDate"];
			$fmtStartDate = date("M d, Y",$startDate);
			$stopDate = $row["stopDate"];
			if ($stopDate <> 0) {
				$fmtStopDate = date("M d, Y",$stopDate);
			} else {
				$fmtStopDate = " ";
			}
			print "<tr><td align=\"left\"> $desc </td>";
			print "<td align=\"center\"> $fmtAmount </td>";
			print "<td align=\"center\"> $fmtStartDate </td>";
			print "<td align=\"center\"> $fmtStopDate </td>";
			print "<td align=\"center\"> $recurName[$recur] </td>";
			print "<td><a href=\"index.php?func=mntBillItems&editThing=$id&cust=$cust&page=$passPage\">Edit</a></td>";
			print "</tr>\n";
		}
	}
	print "</table><br />\n";
	// how many rows we have in database
		$prev  = ' [Prev Page] ';       // we're on page one, don't enable 'previous' link
		$first = ' [First Page] ';      // nor 'first page' link

		$next = ' [Next Page] ';      // we're on the last page, don't enable 'next' link
		$last = ' [Last Page] ';      // nor 'last page' link

	// print the page navigation link
	print "<table width=\"100%\"><tr>";
	print "<td align=\"left\"> $first </td><td align=\"left\"> $prev </td><td> Page <strong>1</strong> of <strong>1</strong> </td>";
	print "<td align=\"right\"> $next </td><td align=\"right\"> $last </td></tr>";
	print "</table>\n";
	print "</form>\n";

	mysql_close($conn);
}

function validInsert() {
	$desc = escQuotes(trim($_POST['thing']));
	$amount = dollars($_POST['amount']);
	$startMonth = $_POST['startMonth'];
	$startDay = $_POST['startDay'];
	$startYear = $_POST['startYear'];
	$stopMonth = $_POST['stopMonth'];
	$stopDay = $_POST['stopDay'];
	$stopYear = $_POST['stopYear'];
	$recur = $_POST['recur'];
	if ($desc == "") {
		return FALSE;
	}
	if ($recur == "O") {
		return TRUE;
	}
	if (($stopMonth == 0) || ($stopDay == 0) || ($stopYear == 0)) {
		return FALSE;
	}
	$startDate = mktime(0,0,0,$startMonth,$startDay,$startYear);
	$stopDate = mktime(0,0,0,$stopMonth,$stopDay,$stopYear);
	if ($stopDate < $startDate) {
		return FALSE;
	}
	return TRUE;
	}

function insertThing() {
	global $cust;
	$desc = escQuotes(trim($_POST['thing']));
	$amount = dollars($_POST['amount']);
	$recur = $_POST['recur'];
	$startMonth = $_POST['startMonth'];
	$startDay = $_POST['startDay'];
	$startYear = $_POST['startYear'];
	$startDate = mktime(0,1,0,$startMonth,$startDay,$startYear);
	$stopMonth = $_POST['stopMonth'];
	$stopDay = $_POST['stopDay'];
	$stopYear = $_POST['stopYear'];
	if ($recur == "O") {
		$stopDate = 0;
	} else {
		$stopDate = mktime(0,1,0,$stopMonth,$stopDay,$stopYear);
	}
		// Connect to database
	include('dbconn.php');
	// Insert new data into database
	$query = "INSERT INTO recurringItems ";
  	$query .= " (id, cust, `desc`, amount, startDate, stopDate, recur) ";
  	$query .= " VALUES ";
  	$query .= " ('', $cust, '$desc', $amount, $startDate, $stopDate, '$recur')";
  	$result = mysql_query($query);
  	if (! $result) {
		echo "$query<br /><h4>DATABASE INSERT ERROR 884</h4>";
		mysql_close($conn); 
		return;
	}
	$sql = "SELECT max(id) as id FROM recurringItems where cust = $cust";
	$result = mysql_query($sql);
	if ($result) {
		$row = mysql_fetch_array($result);	
		$id = $row["id"];
		mysql_close($conn);
		recalcItems($id);
	} else {
		mysql_close($conn);
		echo "$query<br /><h4>RECALC ERROR 647</h4>";
	}
	unset($_POST['thing']);
}
		
function displayThingForm() {
	global $thing, $things, $monthName, $cust, $passPage, $recurName, $currYear;
	$currMonth = date('m');
	$currMonthDisp = date('M');
	$currDay = date('d');
	print "<form action=\"index.php?func=mntBillItems&cust=$cust&page=$passPage\" method=\"post\">\n";
	print "<table width=\"100%\">";
	print "<tr><td align=\"right\">$thing: </td>";
	print "<td align=\"left\"><input type=\"text\" size=\"50\" name=\"thing\"</td></tr>\n";
	print "<tr><td align=\"right\">Amount: </td>";
	print "<td align=\"left\"><input type=\"text\" size=\"30\" name=\"amount\"</td></tr>\n";
	print "<tr><td align=\"right\">Start Date: </td>";
	print "<td align=\"left\"><select name=\"startMonth\">";
	print "<option value=\"$currMonth\">$currMonthDisp</option>";
	for ($i = 1; $i <= 12; $i++) {
		if ($i <> $currMonth) {
			print "<option value=\"$i\">$monthName[$i]</option>";
		}
	}
	print "</select> &nbsp &nbsp <select name=\"startDay\">";
	print "<option value=\"$currDay\">$currDay</option>";
	for ($i = 1; $i <= 31; $i++) {
		if ($i <> $currDay) {
			print "<option value=\"$i\">$i</option>";
		}
	}
	print "</select> &nbsp &nbsp <select name=\"startYear\">";
	print "<option value=\"$currYear\">$currYear</option>";
	for ($i = $currYear - 2; $i <= $currYear + 2; $i++) {
		if ($i <> $currYear) {
			print "<option value=\"$i\">$i</option>";
		}
	}
	print "</select></td></tr>";
	print "<tr><td align=\"right\">Stop Date: </td>";
	print "<td align=\"left\"><select name=\"stopMonth\">";
	print "<option value=\"0\"> </option>";
	for ($i = 1; $i <= 12; $i++) {
		print "<option value=\"$i\">$monthName[$i]</option>";
	}
	print "</select> &nbsp &nbsp <select name=\"stopDay\">";
	print "<option value=\"0\"> </option>";
	for ($i = 1; $i <= 31; $i++) {
		print "<option value=\"$i\">$i</option>";
	}
	print "</select> &nbsp &nbsp <select name=\"stopYear\">";
	print "<option value=\"0\"> </option>";
	for ($i = $currYear - 2; $i <= $currYear + 5; $i++) {
		print "<option value=\"$i\">$i</option>";
	}
	print "<option value=\"2020\">2020</option>";
	print "</select></td></tr>";
	print "<tr><td align=\"right\">Recurs: </td>";
	print "<td align=\"left\"><select name=\"recur\">";
	foreach ($recurName as $key => $value) {
		print "<option value=\"$key\">$value</option>";
	}
	print "</select></td></tr>";
	print "</table>";
	print "<input type=\"Submit\" value=\"Add $thing\">";
	print "</form>";
	print "<br /><br />\n"; 
}

function recalcItems($parentItem) {
	// Connect to database
	include('dbconn.php');
	$query = "SELECT cust, `desc`, amount, startDate, stopDate, recur ";
	$query .= " FROM recurringItems WHERE id = $parentItem";
	$result = mysql_query($query);
	if ($result) {
		$row = mysql_fetch_array($result);	
		$cust = $row["cust"];
		$desc = $row["desc"];
		$amount = $row["amount"];
		$startDate = $row["startDate"];
		$stopDate = $row["stopDate"];
		$recur = $row["recur"];
	}
	$query = "DELETE FROM billingItems WHERE recurId = $parentItem";
  	$result = mysql_query($query);
  	if (! $result) {
		echo "$query<br /><h4>DATABASE RECALC DELETE ERROR 408</h4>";
		mysql_close($conn); 
		return;
	}
	$query = "INSERT INTO billingItems ";
  	$query .= " (id, cust, recurId, date, `desc`, amount) ";
  	$query .= " VALUES ";
  	$query .= " ('', $cust, $parentItem, $startDate, '$desc', $amount)";
  	$result = mysql_query($query);
  	if (! $result) {
		echo "$query<br /><h4>DATABASE INSERT ERROR 420</h4>";
		mysql_close($conn); 
		return;
	}
	if ($recur == "O") {
		// If this is a one time only, then we're done.
		return TRUE;
	}
	$wkDate = bumpUp($startDate, $recur);
	while ($wkDate <= $stopDate) {
		$query = "INSERT INTO billingItems ";
  		$query .= " (id, cust, recurId, date, `desc`, amount) ";
  		$query .= " VALUES ";
  		$query .= " ('', $cust, $parentItem, $wkDate, '$desc', $amount)";
  		$result = mysql_query($query);
  		if (! $result) {
			echo "$query<br /><h4>DATABASE INSERT ERROR 435</h4>";
			mysql_close($conn); 
			return;
		}
		$wkDate = bumpUp($wkDate, $recur);
	}
}

function bumpUp($inDate, $bumpCode) {
	$outDate = $inDate;
	if ($bumpCode == "A") {
		// annual is 365.25 days (with 86,400 seconds per day)
		$outDate += 31557600;
	} elseif ($bumpCode == "Q") {
		// quarterly is 91.35 days (with 86,400 seconds per day)
		$outDate += 7892640;
	} elseif ($bumpCode == "M") {
		// monthly is 30.5 days (with 86,400 seconds per day)
		$outDate += 2635200;
	} else {
		$outDate = 999999999999999999999;
		echo "<br /><h4>Illegal Bump Code passed!!!!!!!</h4>";
	}	
	return $outDate;
}

include ('escQuotes.php');
include ('dollars.php');

?>
