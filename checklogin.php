<?php
//
//  checklogin.php is a PHP component of HPTTix
//  Copyright 2014 John Francis, HPT Solutions, Inc.  
//  Licensed under the Open Software License (OSL 3.0).
//  
session_start();
include('Redirect302.php');
include('dbconn.php');

// username and password sent from form
$myuserid = strtoupper($_POST['userid']);
$mypassword = $_POST['password'];
// To protect MySQL injection (more detail about MySQL injection)
$userid = stripslashes($myuserid);
$password = stripslashes($mypassword);
$userid = mysql_real_escape_string($userid);
$password = mysql_real_escape_string($password);

$sql = "SELECT id, adminUser, canLogin FROM operators WHERE userid = '$userid' and password = '";
$sql .= sha1($password);
$sql .= "'";
$result = mysql_query($sql);
// Mysql_num_row is counting table row
$count = mysql_num_rows($result);
// If result matched $myuserid and $mypassword, table row must be 1 row
if ($count == 1) {
	$row = mysql_fetch_array($result);
	if ($row["canLogin"] == 1) {
		$_SESSION['status'] = "OK";
		if ($row["adminUser"] == 1) {
			$_SESSION['accesslvl'] = "ADMIN";
		} else {
			$_SESSION['accesslvl'] = "USER";
		}
		$_SESSION['user'] = $row["id"];
		$_SESSION['appl'] = "HPTTIX";
		$_SESSION['userid'] = $userid;
		$_SESSION['filterOpen'] = 1;
		$_SESSION['filterStat'] = 0;
		$_SESSION['filterAssign'] = 0;
		$_SESSION['filterCat'] = 0;
		$_SESSION['filterCust'] = 0;
		Redirect302("Location: index.php");
	}
} else {
	$_SESSION['status'] = "INV";
	Redirect302("Location: index.php");
}

?>