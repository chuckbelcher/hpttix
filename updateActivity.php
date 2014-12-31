<?php
//
//  updateActivity.php is a PHP component of HPTTix
//  Copyright 2014 John Francis, HPT Solutions, Inc.  
//  Licensed under the Open Software License (OSL 3.0).
//  

$thing = "Activity";
$things = "Activities";
$tix = 0;

function updateActivity() {
	global $thing, $things, $tix;
	if (isset($_GET['activity'])) {
		printTop();
		print "<div id='profile'><h5><a href='index.php?func=newpass'>Change Your Password</a></h5></div>\n";
		print "<div id='nav'><h5><a href='index.php'>Dashboard</a> | <a href='logout.php'>Logout</a></h5></div>\n";
		print "<br /><br /><br />";
		print "<h3>Edit $things</h3><br />";
		print "<div id=\"newList\">";
		editThingForm();
		print "</div>\n";
	}
	elseif (isset($_POST['ething'])) { 
		editUpdateThing();
		Redirect302("Location: index.php?func=incidentDetail&incident=$tix");
	}
}

function editUpdateThing() {
	global $tix;
	$id = $_POST['ething'];
// THE NAMES PAST HERE MUST BE CHANGED TO FIT THE PARTICULAR ROUTINE.  DON'T FORGET FUNC NAMES, ETC.
	$tix = $_POST['etix'];
	$reportable = $_POST['ereportable'];
	$billable = $_POST['ebillable'];
	$startMonth = $_POST['estartMonth'];
	$startDay = $_POST['estartDay'];
	$startYear = $_POST['estartYear'];
	$startHour = $_POST['estartHour'];
	$startMinute = $_POST['estartMinute'];
	$startAMind = $_POST['estartAMind'];
	if (($startAMind == "PM") && ($startHour < 12)) {
		$startHour += 12;
	}
	$startTime = mktime($startHour,$startMinute,0,$startMonth,$startDay,$startYear);
	$duration = escQuotes(trim($_POST['eduration']));
	if ($duration == "") {
		$duration = 0;
	}
	$desc = escQuotes(trim($_POST['edesc']));
	$oper = $_POST['eoper'];
	// Connect to database
	include('dbconn.php');
// CHANGE TO FIT PARTICULAR TABLE. Post edited item to database
	$sql = "UPDATE activities SET reportable = $reportable, billable = $billable, `start` = $startTime, duration = $duration, ";
	$sql .= " `desc` = '$desc', `oper` = $oper ";
	$sql .= " WHERE id = $id";
	$result = mysql_query($sql);
  	if (! $result) {
		$_SESSION['msg'] = "$sql<br />DATABASE UPDATE ERROR 420";
		mysql_close($conn); 
	}
	mysql_close($conn); 
	unset($_POST['ething']);
}

function editThingForm() {
	global $thing, $things, $tix;
	$monthArray= array(" ", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
	$activityId = $_GET['activity'];
	// Connect to database
	include('dbconn.php');
// THE NAMES PAST HERE MUST BE CHANGED TO FIT THE PARTICULAR ROUTINE.  DON'T FORGET FUNC NAMES, ETC.
	$query = "SELECT id, `oper`, `start`, duration, `desc`, reportable, billable, incident ";
	$query .= " FROM activities ";
	$query .= " WHERE id = $activityId";
	$result = mysql_query($query);
	if ($result) {
		$row = mysql_fetch_array($result);	
		$id = $row["id"];
		$tix = $row["incident"];
		$reportable = $row["reportable"];
		$billable = $row["billable"];
		$start = $row["start"];
		$currMonth = date('m', $start);
		$currMonthDisp = date('M', $start);
		$currDay = date('d', $start);
		$currYear = date('Y', $start);
		$lastYear = $currYear - 1;
		$nextYear = $currYear + 1;
		$currHour = date('g', $start);
		$currMinute = date('i', $start);
		$currAMind = date('A', $start);
		$duration = $row["duration"];
		$desc = $row["desc"];
		$oper = $row["oper"];
		print "<form action=\"index.php?func=updateActivity\" method=\"post\">\n";
		print "<table width=\"90%\" align=\"center\" cellpadding=\"2\" border=\"1\" rules=\"none\">";
		print "<tr><td align=\"right\"><b>$thing:</b> </td><td align=\"left\">";
		print "<textarea name=\"edesc\" rows=\"5\" cols=\"60\">$desc</textarea></td></tr>";
		print "<tr><td align=\"right\"><b>Performed By: </b></td><td align=\"left\">";
		print "<select name=\"eoper\">";
		$query2 = "SELECT name from operators where id = $oper"; 
		$result2 = mysql_query($query2);
		if ($result2) {
			$row2 = mysql_fetch_array($result2);
			$name = $row2["name"];
			print "<option value=\"$oper\">$name</option>";
		}
		$query2 = "SELECT id, name from operators where id <> $oper and canLogin = 1 order by name";
		$result2 = mysql_query($query2);
		if ($result2) {
			while ($row2 = mysql_fetch_array($result2)) {
				$id = $row2["id"];
				$name = $row2["name"];
				print "<option value=\"$id\">$name</option>";
			}
		} 
		print "</select></td></tr>";
		print "<tr><td align=\"right\"><b>Reportable?: </b></td><td align=\"left\">";
		if ($reportable == 1) {
			print "<input type=\"radio\" name=\"ereportable\" value=\"1\" checked /> Visible to Customer ";
			print "<input type=\"radio\" name=\"ereportable\" value=\"0\"/> Internal Only </td></tr>\n";
		} else {
			print "<input type=\"radio\" name=\"ereportable\" value=\"1\" /> Visible to Customer ";
			print "<input type=\"radio\" name=\"ereportable\" value=\"0\" checked /> Internal Only </td></tr>\n";
		}
		print "<tr><td align=\"right\"><b>Billable?:</b></td><td align=\"left\">";
		if ($billable == 1) {
			print "<input type=\"radio\" name=\"ebillable\" value=\"1\" checked /> Yes ";
			print "<input type=\"radio\" name=\"ebillable\" value=\"0\"/> No </td></tr>\n";
		} else {
			print "<input type=\"radio\" name=\"ebillable\" value=\"1\" /> Yes ";
			print "<input type=\"radio\" name=\"ebillable\" value=\"0\" checked /> No </td></tr>\n";
		}
		print "<tr><td align=\"right\"><b>Start Time: </b></td><td align=\"left\">";
		print "<select name=\"estartMonth\"><option value=\"$currMonth\">$currMonthDisp</option>";
		for ($i = 1; $i <= 12; $i++) {
			if ($i <> $currMonth) {
				print "<option value=\"$i\">$monthArray[$i]</option>";
			}
		}
		print "</select> <select name=\"estartDay\"><option value=\"$currDay\">$currDay</option>";
		for ($i = 1; $i <= 31; $i++) {
			if ($i <> $currDay) {
				print "<option value=\"$i\">$i</option>";
			}
		}
		print "</select> <select name=\"estartYear\"><option value=\"$currYear\">$currYear</option>";
		print "<option value=\"$lastYear\">$lastYear</option>";
		print "<option value=\"$nextYear\">$nextYear</option>";
		print "</select> &nbsp &nbsp &nbsp <select name=\"estartHour\"><option value=\"$currHour\">$currHour</option>";
		for ($i = 1; $i <= 12; $i++) {
			if ($i <> $currHour) {
				print "<option value=\"$i\">$i</option>";
			}
		}
		print "</select> <select name=\"estartMinute\"><option value=\"$currMinute\">$currMinute</option>";
		for ($i = 0; $i <= 55; $i += 5) {
			if ($i <> $currMinute) {
				print "<option value=\"$i\">$i</option>";
			}
		}
		print "</select> &nbsp <select name=\"estartAMind\"><option value=\"$currAMind\">$currAMind</option>";
		if ($currAMind == "AM") {
			print "<option value=\"PM\">PM</option>";
		} else {
			print "<option value=\"AM\">AM</option>";
		}
		print "</select></td></tr>\n";
		print "<tr><td align=\"right\"><b>Activity Duration (min):</b></td><td align=\"left\"><input type=\"text\" name=\"eduration\" value=\"$duration\" size=\"5\"></td></tr>";
		print "<tr><td colspan=\"2\"><input type=\"submit\" name=\"submit\" value=\"Update Activity\"></td></tr>";
		print "<input type=\"hidden\" name=\"etix\" value=\"$tix\">";
		print "<input type=\"hidden\" name=\"ething\" value=\"$activityId\">";
		print "</table></form></div><br />";
	}
	mysql_close($conn);
}
					
include ('escQuotes.php');

include ('Redirect302.php');

?>