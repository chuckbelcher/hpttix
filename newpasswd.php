<?php
//
//  newpasswd.php is a PHP component of HPTTix
//  Copyright 2014 John Francis, HPT Solutions, Inc.  
//  Licensed under the Open Software License (OSL 3.0).
//  
session_start();
include ('Redirect302.php');

include('dbconn.php');

// old and new password sent from form
$myoldpass=$_POST['oldpass'];
$mynewpass1=$_POST['newpass1'];
$mynewpass2=$_POST['newpass2'];
// To protect MySQL injection (more detail about MySQL injection)
$oldpass = stripslashes($myoldpass);
$newpass1 = stripslashes($mynewpass1);
$newpass2 = stripslashes($mynewpass2);
$oldpass = mysql_real_escape_string($oldpass);
$newpass1 = mysql_real_escape_string($newpass1);
$newpass2 = mysql_real_escape_string($newpass2);

if (! ($newpass1 == $newpass2)) {
	$_SESSION['msg'] = "New passwords don't match, try again";
	Redirect302("Location: index.php?func=newpass");
	return;
}

if ($newpass1 == $oldpass) {
	$_SESSION['msg'] = "New password can't match old password, try again";
	Redirect302("Location: index.php?func=newpass");
	return;
}

$sql = "SELECT adminUser FROM operators WHERE id = ";
$sql .= $_SESSION['user'];
$sql .= " and password = '";
$sql .= sha1($oldpass);
$sql .= "'";
$result = mysql_query($sql);
// Mysql_num_row is counting table row
$count = mysql_num_rows($result);
// If result matched, table row must be 1 row

if (! ($count==1)) {
	$_SESSION['msg'] = "Wrong old password, try again";
	Redirect302("Location: index.php?func=newpass");
	return;
}

$sql = "UPDATE operators SET password = '";
$sql .= sha1($newpass1);
$sql .= "' ";
$sql .= " WHERE id = ";
$sql .= $_SESSION['user'];
$result = mysql_query($sql);

$_SESSION['msg'] = "Password Updated";
Redirect302("Location: index.php");
return;

?>