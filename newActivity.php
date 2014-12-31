<?php
//
//  newActivity.php is a PHP component of HPTTix
//  Copyright 2014 John Francis, HPT Solutions, Inc.  
//  Licensed under the Open Software License (OSL 3.0).
//  

function newActivity() {
	$doneBy = $_SESSION['user'];
	$desc = escQuotes(trim($_POST['desc']));
	$reportable = $_POST['reportable'];
	$billable = $_POST['billable'];
	$startMonth = $_POST['startMonth'];
	$startDay = $_POST['startDay'];
	$startYear = $_POST['startYear'];
	$startHour = $_POST['startHour'];
	$startMinute = $_POST['startMinute'];
	$startAMind = $_POST['startAMind'];
	if (($startAMind == "PM") && ($startHour < 12)) {
		$startHour += 12;
	}
	$startTime = mktime($startHour,$startMinute,0,$startMonth,$startDay,$startYear);
	$duration = escQuotes(trim($_POST['duration']));
	if ($duration == "") {
		$duration = 0;
	}
	$tix = $_POST['tix'];
	// Connect to database
	include('dbconn.php');
	$query = "INSERT INTO activities (id, incident, `oper`, `start`, duration, `desc`, reportable, billable) ";
	$query .= " VALUES ('', $tix, $doneBy, $startTime, $duration, '$desc', $reportable, $billable)";
  	$result = mysql_query($query);
  	if (! $result) {
		$_SESSION['msg'] = "$query<br />DATABASE INSERT ERROR 597";
		mysql_close($conn); 
	}
	mysql_close($conn); 
	Redirect302("Location: index.php?func=incidentDetail&incident=$tix");
}

include ('escQuotes.php');

include ('Redirect302.php');

?>