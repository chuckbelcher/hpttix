<?php
//
//  dbconn.php is a PHP component of HPTTix
//  Copyright 2014 John Francis, HPT Solutions, Inc.  
//  Licensed under the Open Software License (OSL 3.0).
//  
	$hostname="server.name.here.com";
	$username="hpttix_user";
	$password="hpttix_pass";
	$dbname="hpttix_db";
	$conn = mysql_connect($hostname, $username, $password) OR DIE ("Unable to connect to database! Please try again later.");
	mysql_select_db($dbname);
?>
