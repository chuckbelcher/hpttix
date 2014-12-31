<?php
//
//  mntPlans.php is a PHP component of HPTTix
//  Copyright 2014 John Francis, HPT Solutions, Inc.  
//  Licensed under the Open Software License (OSL 3.0).
//  

$thing = "Plan";
$things = "Plans";

function mntPlans() {
	global $thing, $things;
	printTop();
	print "<div id='profile'><h5><a href='index.php?func=newpass'>Change Your Password</a></h5></div>\n";
	print "<div id='nav'><h5><a href='index.php'>Dashboard</a> | <a href='logout.php'>Logout</a></h5></div>\n";
	print "<br /><br /><br />";
	print "<h3>Maintain $things</h3><br />";
	print "<div id=\"incList\">";
	if (isset($_POST['thing'])) {
	if (validInsert()) {
		insertThing();
		echo "<br /><div id='msg'><h4>New $thing Added</h4></div><br /><br /> \n";
	} else {
		echo "<br /><div id='msg'><h4>Can not add - $thing already exists or is blank</h4><br /><br /> \n";
	}
	displayThingForm();
	displayThings();
	}
	elseif (isset($_GET['editThing'])) {
		editThingForm();
	}
	elseif (isset($_POST['ething'])) { 
		editUpdateThing();
		echo "<br /><div id='msg'><h4>$thing Updated</h4></div><br /><br /> \n";
		displayThingForm();
		displayThings();
	}
	else {
		displayThingForm();
		displayThings(); 
	}
	print "</div>\n";
}

function editUpdateThing() {
	$ID = $_POST['ething'];
// THE NAMES PAST HERE MUST BE CHANGED TO FIT THE PARTICULAR ROUTINE.  DON'T FORGET FUNC NAMES, ETC.
	$NAME = escQuotes(trim($_POST['ename']));
	$INCR = escQuotes(trim($_POST['eincr']));
	$ANNUALFEE = escQuotes(trim($_POST['eannualFee']));
	$BASEFEE = escQuotes(trim($_POST['ebaseFee']));
	$BASEHOURS = escQuotes(trim($_POST['ebaseHours']));
	$OVERFEE = escQuotes(trim($_POST['eoverFee']));
	$ACTIVE = $_POST['eactive'];
	// No nulls for update SQL
	if ($INCR == "") {
		$INCR = 15;
	}
	if ($ANNUALFEE == "") {
		$ANNUALFEE = 0;
	}
	if ($BASEFEE == "") {
		$BASEFEE = 0;
	}
	if ($BASEHOURS == "") {
		$BASEHOURS = 0;
	}
	if ($OVERFEE == "") {
		$OVERFEE = 0;
	}
	// Connect to database
	include('dbconn.php');
// CHANGE TO FIT PARTICULAR TABLE. Post edited item to database
	$sql = "UPDATE plans SET incr = $INCR, annualFee = $ANNUALFEE, name = '$NAME', active = $ACTIVE, ";
	$sql .= "baseFee = $BASEFEE, baseHours = $BASEHOURS, overFee = $OVERFEE ";
	$sql .= "WHERE id = $ID";
	$result = mysql_query($sql);
	// Close database connection and unset eitem
	mysql_close($conn);
	unset($_POST['ething']);
}

function editThingForm() {
	global $thing, $things;
	$id = $_GET['editThing'];
	$pageNum = $_GET['page'];
	// Connect to database
	include('dbconn.php');
// THE NAMES PAST HERE MUST BE CHANGED TO FIT THE PARTICULAR ROUTINE.  DON'T FORGET FUNC NAMES, ETC.
	$query = "SELECT ID, NAME, INCR, ANNUALFEE, BASEFEE, BASEHOURS, OVERFEE, ACTIVE ";
	$query .= " from plans where id = $id";
	$result = mysql_query($query);
	if ($result) {
		$row = mysql_fetch_array($result);	
		$id = $row["ID"];
		$name = $row["NAME"];
		$incr = $row["INCR"];
		$annualFee = $row["ANNUALFEE"];
		$baseFee = $row["BASEFEE"];
		$baseHours = $row["BASEHOURS"];
		$overFee = $row["OVERFEE"];
		$active = $row["ACTIVE"];
		print "<form action=\"index.php?func=mntPlans&page=$pageNum\" method=\"post\">\n";
		print "<table width=\"80%\" align=\"center\">";
		print "<tr><td align=\"right\">$thing: </td>";
		print "<td align=\"left\"><input type=\"text\" size=\"50\" name=\"ename\" value=\"$name\"</td></tr>\n";
		print "<tr><td align=\"right\">Billing Increment (minutes): </td>";
		print "<td align=\"left\"><input type=\"text\" size=\"10\" name=\"eincr\" value=\"$incr\"</td></tr>\n";
		print "<tr><td align=\"right\">Annual Fee: </td>";
		print "<td align=\"left\">$<input type=\"text\" size=\"30\" name=\"eannualFee\" value=\"$annualFee\"</td></tr>\n";
		print "<tr><td align=\"right\">Monthly Fee: </td>";
		print "<td align=\"left\">$<input type=\"text\" size=\"30\" name=\"ebaseFee\" value=\"$baseFee\"</td></tr>\n";
		print "<tr><td align=\"right\">Hours Included per Month: </td>";
		print "<td align=\"left\"><input type=\"text\" size=\"30\" name=\"ebaseHours\" value=\"$baseHours\"</td></tr>\n";
		print "<tr><td align=\"right\">Hourly Overage Fee: </td>";
		print "<td align=\"left\">$<input type=\"text\" size=\"30\" name=\"eoverFee\" value=\"$overFee\"</td></tr>\n";
		print "<tr><td align=\"right\">Active?: </td><td align=\"left\">";
		if ($active == 1) {
			print "<input type=\"radio\" name=\"eactive\" value=\"1\" checked /> Yes ";
			print "<input type=\"radio\" name=\"eactive\" value=\"0\"/> No </td></tr>\n";
		} else {
			print "<input type=\"radio\" name=\"eactive\" value=\"1\" /> Yes ";
			print "<input type=\"radio\" name=\"eactive\" value=\"0\" checked /> No </td></tr>\n";
		}
		print "</table>";
		print "<input type=\"Submit\" value=\"Update $thing\"><input type=\"hidden\"  name=\"ething\" value=\"$id\">";
		print "</form>"; 
	}
	mysql_close($conn);
}
					
function displayThings() {
	global $thing, $things;
	$rowsPerPage = 24;
	$pageNum = 1;
	if (isset($_GET['page'])) {
		$pageNum = $_GET['page'];
	}
	$offset = ($pageNum - 1) * $rowsPerPage;
	// Connect to database
	include('dbconn.php');
	// Select data by section and display it
	print "<table width=\"100%\" align=\"center\" cellpadding=\"2\" border=\"1\">\n";
	print "<tr><th>$thing</th><th>Annual $</th><th>Monthly $</th><th>Hours Incl.</th><th>Over $</th><th>Active?</th></tr>\n";
	$query = "SELECT ID, NAME, ANNUALFEE, BASEFEE, BASEHOURS, OVERFEE, ACTIVE ";
	$query .= "from plans order by active desc, name LIMIT $offset, $rowsPerPage";
	$result = mysql_query($query);
	if ($result) {
		while ($row = mysql_fetch_array($result)) {
			$id = $row["ID"];
			$name = $row["NAME"];
			$annualFee = $row["ANNUALFEE"];
			$baseFee = $row["BASEFEE"];
			$baseHours = $row["BASEHOURS"];
			$overFee = $row["OVERFEE"];
			$active = $row["ACTIVE"];
			print "<tr><td align=\"center\"> $name </td>";
			print "<td align=\"center\"> $annualFee </td>";
			print "<td align=\"center\"> $baseFee </td>";
			print "<td align=\"center\"> $baseHours </td>";
			print "<td align=\"center\"> $overFee </td>";
			if ($active == 1) {
				print "<td>Yes</td>";
			} else {
				print "<td>No</td>";
			}
			print "<td><a href=\"index.php?func=mntPlans&editThing=$id&page=$pageNum\">edit</a></td>";
			print "</tr>\n";
		}
	}
	print "</table><br />\n";
	// how many rows we have in database
	$query = "SELECT COUNT(*) AS numrows FROM plans";
	$result = mysql_query($query) or die('Error, query failed');
	$row = mysql_fetch_array($result);
	$numrows = $row['numrows'];
	// how many pages we have when using paging?
	$maxPage = ceil($numrows/$rowsPerPage);
	$self = $_SERVER['PHP_SELF'];
	// creating 'previous' and 'next' link
	// plus 'first page' and 'last page' link

	// print 'previous' link only if we're not
	// on page one
	if ($pageNum > 1) {
		$page = $pageNum - 1;
		$prev = " <a href=\"$self?func=mntPlans&page=$page\">[Prev Page]</a> ";
		$first = " <a href=\"$self?func=mntPlans&page=1\">[First Page]</a> ";
	} else {
		$prev  = ' [Prev Page] ';       // we're on page one, don't enable 'previous' link
		$first = ' [First Page] ';      // nor 'first page' link
	}

	// print 'next' link only if we're not
	// on the last page
	if ($pageNum < $maxPage) {
		$page = $pageNum + 1;
		$next = " <a href=\"$self?func=mntPlans&page=$page\">[Next Page]</a> ";
		$last = " <a href=\"$self?func=mntPlans&page=$maxPage\">[Last Page]</a> ";
	} else {
		$next = ' [Next Page] ';      // we're on the last page, don't enable 'next' link
		$last = ' [Last Page] ';      // nor 'last page' link
	}

	// print the page navigation link
	print "<table width=\"100%\"><tr>";
	print "<td align=\"left\"> $first </td><td align=\"left\"> $prev </td><td> Page <strong>$pageNum</strong> of <strong>$maxPage</strong> </td>";
	print "<td align=\"right\"> $next </td><td align=\"right\"> $last </td></tr>";
	print "</table>\n";
	print "</form>\n";

	mysql_close($conn);
}

function validInsert() {
	$NAME = $_POST['thing'];
	if ($NAME == "") {
		return FALSE;
	}
	// Connect to database
	include('dbconn.php');
		
	// See if thing already exists
    $query = "SELECT id FROM plans WHERE name = '$NAME'";
    $result = mysql_query($query);
	$count = mysql_num_rows($result);
	if ($count == 0) {
	    mysql_close($conn); 
	    return TRUE;
	} else {
	    mysql_close($conn); 
	    return FALSE;
	}	  	
}

function insertThing() {
	$NAME = escQuotes(trim($_POST['thing']));
	$INCR = escQuotes(trim($_POST['incr']));
	$ANNUALFEE = escQuotes(trim($_POST['annualFee']));
	$BASEFEE = escQuotes(trim($_POST['baseFee']));
	$BASEHOURS = escQuotes(trim($_POST['baseHours']));
	$OVERFEE = escQuotes(trim($_POST['overFee']));
	$ACTIVE = $_POST['active'];
	// Convert any nulls
	if ($INCR == "") {
		$INCR = 15;
	}
	if ($ANNUALFEE == "") {
		$ANNUALFEE = 0;
	}
	if ($BASEFEE == "") {
		$BASEFEE = 0;
	}
	if ($BASEHOURS == "") {
		$BASEHOURS = 0;
	}
	if ($OVERFEE == "") {
		$OVERFEE = 0;
	}
	// Connect to database
	include('dbconn.php');
	// Insert new data into database
	$query = "INSERT INTO plans ";
  	$query .= " (id, name, incr, annualFee, baseFee, baseHours, overFee, active) ";
  	$query .= " VALUES ";
  	$query .= " ('', '$NAME', $INCR, $ANNUALFEE, $BASEFEE, $BASEHOURS, $OVERFEE, $ACTIVE)";
  	$result = mysql_query($query);
  	if (! $result) {
		echo "$query<br /><h4>DATABASE INSERT ERROR 271</h4>";
 	   mysql_close($conn); 
		return;
	}
  mysql_close($conn); 
  unset($_POST['thing']);
}
		
function displayThingForm() {
	global $thing, $things;
	print "<form action=\"index.php?func=mntPlans\" method=\"post\">\n";
	print "<table width=\"100%\">";
	print "<tr><td align=\"right\">$thing: </td>";
	print "<td align=\"left\" colspan=\"2\">&nbsp<input type=\"text\" size=\"50\" name=\"thing\"></td></tr>\n";
	print "<tr><td align=\"right\">Billing Increment (minutes): </td>";
	print "<td align=\"left\">&nbsp<input type=\"text\" size=\"10\" name=\"incr\"</td></tr>\n";
	print "<tr><td align=\"right\">Annual Fee ($): </td>";
	print "<td align=\"left\"><input type=\"text\" size=\"30\" name=\"annualFee\"</td></tr>\n";
	print "<tr><td align=\"right\">Monthly Fee ($): </td>";
	print "<td align=\"left\"><input type=\"text\" size=\"30\" name=\"baseFee\"</td></tr>\n";
	print "<tr><td align=\"right\">Hours Included per Month: </td>";
	print "<td align=\"left\">&nbsp<input type=\"text\" size=\"30\" name=\"baseHours\"</td></tr>\n";
	print "<tr><td align=\"right\">Hourly Overage Fee ($): </td>";
	print "<td align=\"left\"><input type=\"text\" size=\"30\" name=\"overFee\"</td></tr>\n";
	print "<tr><td align=\"right\">Active?: </td><td align=\"left\">\n";
	print "<input type=\"radio\" name=\"active\" value=\"1\" checked /> Yes ";
	print "<input type=\"radio\" name=\"active\" value=\"0\"/> No </td></tr>\n";
	print "</table>";
	print "<input type=\"Submit\" value=\"Add $thing\">";
	print "</form>";
	print "<br /><br />\n"; 
}

include ('escQuotes.php');

?>
