<?php
//
//  deleteActivity.php is a PHP component of HPTTix
//  Copyright 2014 John Francis, HPT Solutions, Inc.  
//  Licensed under the Open Software License (OSL 3.0).
//  

function deleteActivity() {
	$activity = $_GET['activity'];
	// Connect to database
	include('dbconn.php');
	$query = "SELECT incident FROM activities WHERE id = $activity";
	$result = mysql_query($query);
	if ($result) {
		$row = mysql_fetch_array($result);
		$tix = $row["incident"];
	} else {
		$_SESSION['msg'] = "$query<br /><h4>DATABASE SELECT ERROR 796</h4>";
		return;
	}
	$query = "DELETE FROM activities WHERE id = $activity";
  	$result = mysql_query($query);
  	if (! $result) {
		$_SESSION['msg'] =  "$query<br /><h4>DATABASE DELETE ERROR 797</h4>";
		mysql_close($conn); 
	}
	mysql_close($conn); 
	Redirect302("Location: index.php?func=incidentDetail&incident=$tix");
}

include ('Redirect302.php');

?>