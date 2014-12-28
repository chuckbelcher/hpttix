<?php
	$hostname="server.name.here.com";
	$username="hpttix_user";
	$password="hpttix_pass";
	$dbname="hpttix_db";
	$conn = mysql_connect($hostname, $username, $password) OR DIE ("Unable to connect to database! Please try again later.");
	mysql_select_db($dbname);
?>
