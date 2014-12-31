<?php
//
//  logout.php is a PHP component of HPTTix
//  Copyright 2014 John Francis, HPT Solutions, Inc.  
//  Licensed under the Open Software License (OSL 3.0).
//  
session_start();
session_unset();
session_destroy(); 
header("Location: index.php");
?>
