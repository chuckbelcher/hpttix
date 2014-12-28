<?php

$tix = 0;

function newIncident() {
	global $tix;
	if (isset($_POST['dashButton'])) {
		Redirect302("Location: index.php");
	}
	if (isset($_POST['caller'])) {
		if (validInsert()) {
			if (insertTix()) {
				Redirect302("Location: index.php?func=incidentDetail&incident=$tix");
			}
		} else {
			printTop();
			print "<div id='profile'><h5><a href='index.php?func=newpass'>Change Your Password</a></h5></div>\n";
			print "<div id='nav'><h5><a href='index.php'>Dashboard</a> | <a href='logout.php'>Logout</a></h5></div>\n";
			print "<br /><br /><br />";
			print "<h3>Create a New Incident Ticket</h3><br />";
			print "<div id=\"newList\">";
			print "<br /><div id='msg'><h4>Caller, Short Desc and Description are all required.</h4><br /><br /> \n";
			reDisplayTixForm();
		}
	} else {
		printTop();
		print "<div id='profile'><h5><a href='index.php?func=newpass'>Change Your Password</a></h5></div>\n";
		print "<div id='nav'><h5><a href='index.php'>Dashboard</a> | <a href='logout.php'>Logout</a></h5></div>\n";
		print "<br /><br /><br />";
		print "<h3>Create a New Incident Ticket</h3><br />";
		print "<div id=\"newList\">";
		displayTixForm();
	}
	print "</div>\n";
}

function validInsert() {
	$CALLER = $_POST['caller'];
	$SHORTDESC = escQuotes(trim($_POST['shortDesc']));
	$DESC = escQuotes(trim($_POST['desc']));
	if (($CALLER == 0) || ($SHORTDESC == "") || ($DESC == "")) {
		return FALSE;
	} else {
	    return TRUE;
	}	  	
}

function insertTix() {
	global $tix;
	$start = time();
	$openedBy = $_SESSION['user'];
	$caller = $_POST['caller'];
	$shortDesc = escQuotes(trim($_POST['shortDesc']));
	$desc = escQuotes(trim($_POST['desc']));
	$status = $_POST['status'];
	$severity = $_POST['severity'];
	$priority = $_POST['priority'];
	$assignedTo = $_POST['assignedTo'];
	$category = $_POST['category'];
	$billable = $_POST['billable'];
	$openInd = 1;
	// Connect to database
	include('dbconn.php');
	$query = "INSERT INTO incidents (id, caller, `start`, `stop`, shortDesc, `desc`, `status`, severity, ";
	$query .= " priority, openedBy, assignedTo, category, billable, openInd) ";
	$query .= " VALUES ('', $caller, $start, NULL, '$shortDesc', '$desc', $status, $severity, ";
	$query .= " $priority, $openedBy, $assignedTo, $category, $billable, $openInd)";
  	$result = mysql_query($query);
  	if (! $result) {
		$_SESSION['msg'] = "$query<br /><h4>DATABASE INSERT ERROR 693</h4>";
		mysql_close($conn); 
		return FALSE;
	}
	$query = "SELECT id FROM incidents WHERE start = $start AND openedBy = $openedBy";
	$result = mysql_query($query);
	if ($result) {
		$row = mysql_fetch_array($result);	
		$tix = $row["id"];
	} else {
		$_SESSION['msg'] = "$query<br /><h4>DATABASE INSERT ERROR 793</h4>";
		mysql_close($conn); 
		return FALSE;
	}
	$query = "INSERT INTO activities (id, incident, `oper`, `start`, duration, `desc`, reportable, billable) ";
	$query .= " VALUES ('', $tix, $openedBy, $start, 0, 'Ticket Opened', 1, 0)";
  	$result = mysql_query($query);
  	if (! $result) {
		$_SESSION['msg'] = "$query<br /><h4>DATABASE INSERT ERROR 893</h4>";
		mysql_close($conn); 
		return FALSE;
	}
	mysql_close($conn); 
	return TRUE;
}

function reDisplayTixForm() {
	$caller = $_POST['caller'];
	$category = $_POST['category'];
	$assignedTo = $_POST['assignedTo'];
	$status = $_POST['status'];
	$severity = $_POST['severity'];
	$priority = $_POST['priority'];
	$shortDesc = escQuotes(trim($_POST['shortDesc']));
	$desc = escQuotes(trim($_POST['desc']));
	$billable = $_POST['billable'];
	// Connect to database
	include('dbconn.php');
	// Now display form with the previously entered values
	print "<form action=\"index.php?func=newIncident\" method=\"post\">\n";
	print "<table width=\"80%\" align=\"center\" cellpadding=\"1\" border=\"1\">\n";
	print "<tr>";
	print "<td><table cellpadding=\"2\" border=\"0\">";
	print "<tr><td align=\"right\"><b>Caller: </b></td><td align=\"left\">";
	print "<select name=\"caller\">";
	if ($caller == 0) {
		print "<option value=\"0\">(Select a Caller)</option>";
	} else {
		$query = "SELECT a.name as CUST, b.id as ID, b.name as CALLER ";
		$query .= " FROM customers a, callers b ";
		$query .= " WHERE a.id = b.cust AND b.id = $caller"; 
		$result = mysql_query($query);
		if ($result) {
			$row = mysql_fetch_array($result);
			$id = $row["ID"];
			$customer = $row["CUST"];
			$caller = $row["CALLER"];
			print "<option value=\"$id\">$caller &nbsp &nbsp | &nbsp &nbsp $customer</option>";
		}
	}
	$query = "SELECT a.name as CUST, b.id as ID, b.name as CALLER ";
	$query .= " FROM customers a, callers b ";
	$query .= " WHERE a.id = b.cust AND b.id <> $caller"; 
	$query .= " ORDER BY CUST, CALLER";
	$result = mysql_query($query);
	if ($result) {
		while ($row = mysql_fetch_array($result)) {
			$id = $row["ID"];
			$customer = $row["CUST"];
			$caller = $row["CALLER"];
			print "<option value=\"$id\">$caller &nbsp &nbsp | &nbsp &nbsp $customer</option>";
		}
	}
	print "</select></td></tr>";
	print "<tr><td align=\"right\"><b>Category: </b></td><td align=\"left\">";
	print "<select name=\"category\">";
	$query = "SELECT name FROM categories where id = $category";
	$result = mysql_query($query);
	if ($result) {
		$row = mysql_fetch_array($result);
		$name = $row["name"];
		print "<option value=\"$category\">$name</option>";
	}
	$query = "SELECT id, name FROM categories where id <> $category ORDER BY name";
	$result = mysql_query($query);
	if ($result) {
		while ($row = mysql_fetch_array($result)) {
			$id = $row["id"];
			$name = $row["name"];
			print "<option value=\"$id\">$name</option>";
		}
	}
	print "</select></td></tr>";
	print "<tr><td align=\"right\"><b>Assign To: </b></td><td align=\"left\">";
	print "<select name=\"assignedTo\">";
	$query = "SELECT name FROM operators where id = $assignedTo";
	$result = mysql_query($query);
	if ($result) {
		$row = mysql_fetch_array($result);
		$name = $row["name"];
		print "<option value=\"$assignedTo\">$name</option>";
	}
	$query = "SELECT id, name FROM operators where id <> $assignedTo ORDER BY name";
	$result = mysql_query($query);
	if ($result) {
		while ($row = mysql_fetch_array($result)) {
			$id = $row["id"];
			$name = $row["name"];
			print "<option value=\"$id\">$name</option>";
		}
	}
	print "</select></td></tr>";
	print "</table></td>";
	print "<td><table cellpadding=\"2\" border=\"0\">";
	print "<tr><td align=\"right\"><b>Status: </b></td><td align=\"left\">";
	print "<select name=\"status\">";
	$query = "SELECT name FROM statuses where id = $status";
	$result = mysql_query($query);
	if ($result) {
		$row = mysql_fetch_array($result);
		$name = $row["name"];
		print "<option value=\"$status\">$name</option>";
	}
	$query = "SELECT id, name FROM statuses where id <> $status ORDER BY name";
	$result = mysql_query($query);
	if ($result) {
		while ($row = mysql_fetch_array($result)) {
			$id = $row["id"];
			$name = $row["name"];
			print "<option value=\"$id\">$name</option>";
		}
	}
	print "</select></td></tr>";
	print "<tr><td align=\"right\"><b>Severity: </b></td><td align=\"left\">";
	print "<select name=\"severity\">";
	$query = "SELECT name FROM severities where id = $severity";
	$result = mysql_query($query);
	if ($result) {
		$row = mysql_fetch_array($result);
		$name = $row["name"];
		print "<option value=\"$severity\">$name</option>";
	}
	$query = "SELECT id, name FROM severities where id <> $severity ORDER BY name";
	$result = mysql_query($query);
	if ($result) {
		while ($row = mysql_fetch_array($result)) {
			$id = $row["id"];
			$name = $row["name"];
			print "<option value=\"$id\">$name</option>";
		}
	}
	print "</select></td></tr>";
	print "<tr><td align=\"right\"><b>Priority: </b></td><td align=\"left\">";
	print "<select name=\"priority\">";
	$query = "SELECT name FROM priorities where id = $priority";
	$result = mysql_query($query);
	if ($result) {
		$row = mysql_fetch_array($result);
		$name = $row["name"];
		print "<option value=\"$priority\">$name</option>";
	}
	$query = "SELECT id, name FROM priorities where id <> $priority ORDER BY name";
	$result = mysql_query($query);
	if ($result) {
		while ($row = mysql_fetch_array($result)) {
			$id = $row["id"];
			$name = $row["name"];
			print "<option value=\"$id\">$name</option>";
		}
	}
	print "</select></td></tr>";
	if ($_SESSION['accesslvl'] == "ADMIN") {
		print "<tr><td align=\"right\"><b>Billable? </b></td><td align=\"left\">";
		if ($billable == 1) {
			print "<input type=\"radio\" name=\"billable\" value=\"1\" checked /> Yes ";
			print "<input type=\"radio\" name=\"billable\" value=\"0\"/> No </td></tr>\n";
		} else {
			print "<input type=\"radio\" name=\"billable\" value=\"1\"/> Yes ";
			print "<input type=\"radio\" name=\"billable\" value=\"0\" checked /> No </td></tr>\n";
		}
	} else {
		print "<input type=\"hidden\"  name=\"billable\" value=\"1\">";
	}
	print "</table></td>";
	print "</tr></table>\n";
	print "<table width=\"80%\" align=\"center\" cellpadding=\"4\" border=\"1\">\n";
	print "<tr><td align=\"right\"><b>Short Desc: </b></td>";
	print "<td align=\"left\"><input type=\"text\" size=\"86\" name=\"shortDesc\" value=\"$shortDesc\"</td></tr>\n";
	print "<tr><td align=\"right\"><b>Details: </b></td>";
	print "<td align=\"left\"><textarea name=\"desc\" rows=\"10\" cols=\"65\">$desc</textarea></td></tr>\n";
	print "</table>";
	print "<br />";
	print "<table width=\"50%\" align=\"center\" cellpadding=\"0\" border=\"0\">\n";
	print "<tr><td><input type=\"submit\" name=\"dashButton\" value=\"Cancel\">";
	print "</td><td><input type=\"submit\" name=\"submit\" value=\"Create Ticket\"></td></tr></table>";
	print "</form>";
	print "<br />\n"; 
	mysql_close($conn);
	}

function displayTixForm() {
	// Connect to database
	include('dbconn.php');
	// Get defaults for category, status, severity and priority
	$query = "SELECT defCategory, defStatus, defSeverity, defPriority ";
	$query .= "FROM defaults WHERE id = 1";
	$result = mysql_query($query);
	if ($result) {
		$row = mysql_fetch_array($result);
		$defCat = $row["defCategory"];
		$defStat = $row["defStatus"];
		$defSev = $row["defSeverity"];
		$defPri = $row["defPriority"];
	}
	$defAssignTo = $_SESSION['user'];
	// Now display form with defaults
	print "<form action=\"index.php?func=newIncident\" method=\"post\">\n";
	print "<table width=\"80%\" align=\"center\" cellpadding=\"1\" border=\"1\">\n";
	print "<tr>";
	print "<td><table cellpadding=\"2\" border=\"0\">";
	print "<tr><td align=\"right\"><b>Caller: </b></td><td align=\"left\">";
	print "<select name=\"caller\">";
	print "<option value=\"0\">(Select a Caller)</option>";
	$query = "SELECT a.name as CUST, b.id as ID, b.name as CALLER ";
	$query .= " FROM customers a, callers b ";
	$query .= " WHERE a.id = b.cust "; 
	$query .= " ORDER BY CUST, CALLER";
	$result = mysql_query($query);
	if ($result) {
		while ($row = mysql_fetch_array($result)) {
			$id = $row["ID"];
			$customer = $row["CUST"];
			$caller = $row["CALLER"];
			print "<option value=\"$id\">$caller &nbsp &nbsp | &nbsp &nbsp $customer</option>";
		}
	}
	print "</select></td></tr>";
	print "<tr><td align=\"right\"><b>Category: </b></td><td align=\"left\">";
	print "<select name=\"category\">";
	$query = "SELECT name FROM categories where id = $defCat";
	$result = mysql_query($query);
	if ($result) {
		$row = mysql_fetch_array($result);
		$name = $row["name"];
		print "<option value=\"$defCat\">$name</option>";
	}
	$query = "SELECT id, name FROM categories where id <> $defCat ORDER BY name";
	$result = mysql_query($query);
	if ($result) {
		while ($row = mysql_fetch_array($result)) {
			$id = $row["id"];
			$name = $row["name"];
			print "<option value=\"$id\">$name</option>";
		}
	}
	print "</select></td></tr>";
	print "<tr><td align=\"right\"><b>Assign To: </b></td><td align=\"left\">";
	print "<select name=\"assignedTo\">";
	$query = "SELECT name FROM operators where id = $defAssignTo";
	$result = mysql_query($query);
	if ($result) {
		$row = mysql_fetch_array($result);
		$name = $row["name"];
		print "<option value=\"$defAssignTo\">$name</option>";
	}
	$query = "SELECT id, name FROM operators where id <> $defAssignTo ORDER BY name";
	$result = mysql_query($query);
	if ($result) {
		while ($row = mysql_fetch_array($result)) {
			$id = $row["id"];
			$name = $row["name"];
			print "<option value=\"$id\">$name</option>";
		}
	}
	print "</select></td></tr>";
	print "</table></td>";
	print "<td><table cellpadding=\"2\" border=\"0\">";
	print "<tr><td align=\"right\"><b>Status: </b></td><td align=\"left\">";
	print "<select name=\"status\">";
	$query = "SELECT name FROM statuses where id = $defStat";
	$result = mysql_query($query);
	if ($result) {
		$row = mysql_fetch_array($result);
		$name = $row["name"];
		print "<option value=\"$defStat\">$name</option>";
	}
	$query = "SELECT id, name FROM statuses where id <> $defStat ORDER BY name";
	$result = mysql_query($query);
	if ($result) {
		while ($row = mysql_fetch_array($result)) {
			$id = $row["id"];
			$name = $row["name"];
			print "<option value=\"$id\">$name</option>";
		}
	}
	print "</select></td></tr>";
	print "<tr><td align=\"right\"><b>Severity: </b></td><td align=\"left\">";
	print "<select name=\"severity\">";
	$query = "SELECT name FROM severities where id = $defSev";
	$result = mysql_query($query);
	if ($result) {
		$row = mysql_fetch_array($result);
		$name = $row["name"];
		print "<option value=\"$defSev\">$name</option>";
	}
	$query = "SELECT id, name FROM severities where id <> $defSev ORDER BY name";
	$result = mysql_query($query);
	if ($result) {
		while ($row = mysql_fetch_array($result)) {
			$id = $row["id"];
			$name = $row["name"];
			print "<option value=\"$id\">$name</option>";
		}
	}
	print "</select></td></tr>";
	print "<tr><td align=\"right\"><b>Priority: </b></td><td align=\"left\">";
	print "<select name=\"priority\">";
	$query = "SELECT name FROM priorities where id = $defPri";
	$result = mysql_query($query);
	if ($result) {
		$row = mysql_fetch_array($result);
		$name = $row["name"];
		print "<option value=\"$defPri\">$name</option>";
	}
	$query = "SELECT id, name FROM priorities where id <> $defPri ORDER BY name";
	$result = mysql_query($query);
	if ($result) {
		while ($row = mysql_fetch_array($result)) {
			$id = $row["id"];
			$name = $row["name"];
			print "<option value=\"$id\">$name</option>";
		}
	}
	print "</select></td></tr>";
	if ($_SESSION['accesslvl'] == "ADMIN") {
		print "<tr><td align=\"right\"><b>Billable? </b></td><td align=\"left\">";
		print "<input type=\"radio\" name=\"billable\" value=\"1\" checked /> Yes ";
		print "<input type=\"radio\" name=\"billable\" value=\"0\"/> No </td></tr>\n";
	} else {
		print "<input type=\"hidden\"  name=\"billable\" value=\"1\">";
	}
	print "</table></td>";
	print "</tr></table>\n";
	print "<table width=\"80%\" align=\"center\" cellpadding=\"4\" border=\"1\">\n";
	print "<tr><td align=\"right\"><b>Short Desc: </b></td>";
	print "<td align=\"left\"><input type=\"text\" size=\"86\" name=\"shortDesc\"</td></tr>\n";
	print "<tr><td align=\"right\"><b>Details: </b></td>";
	print "<td align=\"left\"><textarea name=\"desc\" rows=\"10\" cols=\"65\"></textarea></td></tr>\n";
	print "</table>";
	print "<br />";
	print "<table width=\"50%\" align=\"center\" cellpadding=\"0\" border=\"0\">\n";
	print "<tr><td><input type=\"button\" name=\"backbutton\" value=\"Cancel\" onclick=\"javascript: history.back();\">";
	print "</td><td><input type=\"Submit\" value=\"Create Ticket\"></td></tr></table>";
	print "</form>";
	print "<br />\n"; 
	mysql_close($conn);
}

include ('escQuotes.php');

include ('Redirect302.php');

?>
