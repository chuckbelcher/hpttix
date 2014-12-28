<?php

$tix = 0;

function incidentDetail() {
	global $tix;
	printTop();
	print "<div id='profile'><h5><a href='index.php?func=newpass'>Change Your Password</a></h5></div>\n";
	print "<div id='nav'><h5><a href='index.php'>Dashboard</a> | <a href='logout.php'>Logout</a></h5></div>\n";
	print "<br /><br /><br />";
	print "<h3>View / Update Incident Ticket</h3><br />";
	print "<div id=\"incDetail\">";
	if (isset($_GET['incident'])) {
		$tix = $_GET['incident'];
		if (validTix($tix)) {
			incidentForm();
			newActivityForm();
			displayHistory();
		} else {
			print "<br /><h2>SYSTEM ERROR. BAD INCIDENT ($tix) PASSED.</h2><br /><br />\n";
		}
	} else {
		print "<br /><h2>SYSTEM ERROR. NO INCIDENT PASSED.</h2><br /><br />\n";
	}
	print "</div>\n";
}

function validTix($id) {
	// Connect to database
	include('dbconn.php');
	// See if ticket exists
    $query = "SELECT caller FROM incidents WHERE id = $id";
    $result = mysql_query($query);
	$count = mysql_num_rows($result);
	if ($count == 0) {
	   	mysql_close($conn); 
		return FALSE;
	} else {
	    mysql_close($conn); 
		return TRUE;
	}	  	
}

function incidentForm() {
	global $tix;
	print "<div id=\"incDet1\">";
	// Connect to database
	include('dbconn.php');
	$query = "SELECT a.id as ID, b.name as CALLER, c.name as OPERATOR, a.openInd, a.status,  ";
	$query .= " a.severity, a.priority, a.billable, a.shortDesc, a.desc, a.start, a.stop ";
	$query .= " FROM incidents a, callers b, operators c ";
	$query .= " WHERE a.id = $tix AND a.caller = b.id AND a.openedBy = c.id";
	$result = mysql_query($query);
	if ($result) {
		$row = mysql_fetch_array($result);	
		$caller = $row["CALLER"];
		$openedBy = $row["OPERATOR"];
		$openInd = $row["openInd"];
		$status = $row["status"];
		$severity = $row["severity"];
		$priority = $row["priority"];
		$billable = $row["billable"];
		$shortDesc = $row["shortDesc"];
		$desc = $row["desc"];
		$start = $row["start"];
		$stop = $row["stop"];
	} else {
		print "<br /><h2>SYSTEM ERROR. CANNOT RETRIEVE INCIDENT.</h2><h3>Query: $query</h3><br /><br />\n";
		return;
	}
	$fmtStart = date('M d, Y  g:i A',$start);
	$fmtStop = "N/A";
	if ($stop <> "") {
		$fmtStop = date('M d, Y  g:i A',$stop);
	}
	print "<form action=\"index.php?func=updateIncident\" method=\"post\">\n";
	print "<table cellpadding=\"1\" border=\"0\" width=\"80%\">";
	print "<tr><th colspan=\"2\"><b>Incident Ticket Information:</b></th></tr><tr><td colspan=\"2\"> &nbsp </td></tr>";
	print "<tr><td valign=\"top\" align=\"left\">";
	print "<table cellpadding=\"2\" border=\"0\">";
	print "<tr><td align=\"right\"><b>TIX: </b></td><td align=\"left\">$tix</td></tr>";
	print "<tr><td align=\"right\"><b>Caller: </b></td><td align=\"left\">$caller</td></tr>";
	print "<tr><td align=\"right\"><b>Opened: </b></td><td align=\"left\">$fmtStart</td></tr>";
	print "<tr><td align=\"right\"><b>Opened By: </b></td><td align=\"left\">$openedBy</td></tr>";
	print "<tr><td align=\"right\"><b>Closed: </b></td><td align=\"left\">$fmtStop</td></tr>";
	print "</table></td><td align=\"right\">";
	print "<table cellpadding=\"1\" border=\"1\" rules=\"none\">";
	print "<tr><td align=\"right\"><b>Open / Closed: </b></td><td align=\"left\">";
	if ($openInd == 1) {
		print "<input type=\"radio\" name=\"openInd\" value=\"1\" checked /> Open ";
		print "<input type=\"radio\" name=\"openInd\" value=\"0\"/> Closed </td></tr>\n";
	} else {
		print "<input type=\"radio\" name=\"openInd\" value=\"1\"/> Open ";
		print "<input type=\"radio\" name=\"openInd\" value=\"0\" checked /> Closed </td></tr>\n";
	}
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
		print "<input type=\"hidden\"  name=\"billable\" value=\"$billable\">";
	}
	print "<input type=\"hidden\"  name=\"tix\" value=\"$tix\">";
	print "<tr><td align=\"center\" colspan=\"2\"><input type=\"submit\" name=\"submit\" value=\"Update Ticket\"></td></tr>";
	print "</table></td></tr></table>\n";
	print "<br /><table cellpadding=\"4\" border=\"0\">\n";
	print "<tr><td align=\"right\" valign=\"top\"><b>Short Desc: </b></td><td align=\"left\" width=\"86%\">$shortDesc</td></tr>";
	print "<tr><td align=\"right\" valign=\"top\"><b>Description: </b></td><td align=\"left\" width=\"86%\">$desc</td></tr></table>";
	print "</form></div><br />\n";
	mysql_close($conn);
}

function newActivityForm() {
	global $tix;
	print "<div id=\"incDet1\">";
	$monthArray= array(" ", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
	print "<form action=\"index.php?func=newActivity\" method=\"post\">\n";
	print "<table width=\"80%\" align=\"center\" cellpadding=\"1\" border=\"0\" rules=\"none\">\n";
	print "<tr><th align=\"center\"><b>New Activity:</b></th></tr>";
	print "<tr><td align=\"left\"><textarea name=\"desc\" rows=\"5\" cols=\"80\"></textarea></td></tr>\n";
	print "<tr><td align=\"left\"><input type=\"radio\" name=\"reportable\" value=\"1\" checked /> <b>Visible to Customer</b> ";
	print "<input type=\"radio\" name=\"reportable\" value=\"0\"/> <b>Internal Only</b> </td></tr>";
	if ($_SESSION['accesslvl'] == "ADMIN") {
		print "<tr><td align=\"left\"><b>Billable? </b> &nbsp &nbsp ";
		print "<input type=\"radio\" name=\"billable\" value=\"1\" checked /> Yes ";
		print "<input type=\"radio\" name=\"billable\" value=\"0\"/> No </td></tr>\n";
	} else {
		print "<input type=\"hidden\"  name=\"billable\" value=\"1\">";
	}
	$currMonth = date('m');
	$currMonthDisp = date('M');
	$currDay = date('d');
	$currYear = date('Y');
	$lastYear = $currYear - 1;
	$nextYear = $currYear + 1;
	$currHour = date('g');
	$currMinute = date('i');
	$currAMind = date('A');
	print "<tr><td align=\"left\"><b>Start Time: </b> &nbsp &nbsp ";
	print "<select name=\"startMonth\"><option value=\"$currMonth\">$currMonthDisp</option>";
	for ($i = 1; $i <= 12; $i++) {
		if ($i <> $currMonth) {
			print "<option value=\"$i\">$monthArray[$i]</option>";
		}
	}
	print "</select> <select name=\"startDay\"><option value=\"$currDay\">$currDay</option>";
	for ($i = 1; $i <= 31; $i++) {
		if ($i <> $currDay) {
			print "<option value=\"$i\">$i</option>";
		}
	}
	print "</select> <select name=\"startYear\"><option value=\"$currYear\">$currYear</option>";
	print "<option value=\"$lastYear\">$lastYear</option>";
	print "<option value=\"$nextYear\">$nextYear</option>";
	print "</select> &nbsp &nbsp &nbsp <select name=\"startHour\"><option value=\"$currHour\">$currHour</option>";
	for ($i = 1; $i <= 12; $i++) {
		if ($i <> $currHour) {
			print "<option value=\"$i\">$i</option>";
		}
	}
	print "</select> <select name=\"startMinute\"><option value=\"$currMinute\">$currMinute</option>";
	for ($i = 0; $i <= 55; $i += 5) {
		if ($i <> $currMinute) {
			print "<option value=\"$i\">$i</option>";
		}
	}
	print "</select> &nbsp <select name=\"startAMind\"><option value=\"$currAMind\">$currAMind</option>";
	if ($currAMind == "AM") {
		print "<option value=\"PM\">PM</option>";
	} else {
		print "<option value=\"AM\">AM</option>";
	}
	print "</select></td></tr>\n";
	print "<tr><td align=\"left\"><b>Activity Duration (min): &nbsp &nbsp <input type=\"text\" name=\"duration\" size=\"5\"></td></tr>";
	print "<tr><td><input type=\"submit\" name=\"submit\" value=\"Add Activity\"></td></tr>";
	print "<input type=\"hidden\" name=\"tix\" value=\"$tix\">";
	print "</table></form></div><br /><hr /><br />";
}

function displayHistory() {
	global $tix;
	print "<div id=\"incDet1\">";
	print "<table width=\"80%\" align=\"center\" cellpadding=\"1\" border=\"0\" rules=\"none\">\n";
	print "<tr><th align=\"center\" colspan=\"3\"><b>Activity History:</b></th></tr>";
	// Connect to database
	include('dbconn.php');
	$query = "SELECT a.id, b.name as operName, a.start, a.duration, a.desc, a.reportable, a.billable ";
	$query .= " FROM activities a, operators b ";
	$query .= " WHERE a.incident = $tix AND a.oper = b.id ";
	$query .= " ORDER BY `start` desc";
	$result = mysql_query($query);
	if ($result) {
		while ($row = mysql_fetch_array($result)) {
			$id = $row["id"];
			$operName = $row["operName"];
			$start = $row["start"];
			$fmtStart = date('M d, Y  g:i A',$start);
			$duration = $row["duration"];
			$desc = $row["desc"];
			$reportable = $row["reportable"];
			$billable = $row["billable"];
			print "<tr><td align=\"left\"><b>Start: </b>$fmtStart</td>";
			print "<td align=\"left\"><b>Duration: </b>$duration minutes</td>";
			print "<td align=\"right\"><b>";
			if ($billable == 1) {
				print "BILLABLE";
			} else {
				print "NOT BILLABLE";
			}
			print "</b></td></tr>";
			print "<tr><td colspan=\"3\" align=\"left\"><b>Activity: </b>$desc</td></tr>";
			print "<tr><td colspan=\"3\" align=\"left\"><b>Performed By: </b>$operName</td></tr>";
			print "<tr><td align=\"left\"><b>";
			if ($reportable == 1) {
				print "Visible to customer";
			} else {
				print "Internal only";
			}
			print "</b></td><td colspan=\"2\" align=\"right\">";
			if ($_SESSION['accesslvl'] == "ADMIN") {
				print "<a href=\"index.php?func=updateActivity&activity=$id\"><img src=\"editButton.gif\"></a>";
				print "&nbsp &nbsp <a href=\"index.php?func=deleteActivity&activity=$id\"><img src=\"deleteButton.gif\"></a>";
			} else {
				print "&nbsp";
			}
			print "</td></tr><tr><td colspan=\"3\"><hr /></td></tr>";
		}
	} else {
		print "<br />SYSTEM ERROR: QUERY >$query< <br />";
	}
	print "</table></div><br />";
	mysql_close($conn);
}

?>
