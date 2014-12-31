<?php 
//
//  escQuotes.php is a PHP component of HPTTix
//  Copyright 2014 John Francis, HPT Solutions, Inc.  
//  Licensed under the Open Software License (OSL 3.0).
//  

function escQuotes($inString) {
	// This function escapes single or double quotes (and backslashes) in strings to keep SQL happy
	$outString = "";
	for ($i = 0; $i < strlen($inString); $i++) {
		if ($inString[$i] == "'") {
			$outString .= "\'";
		} elseif ($inString[$i] == "\"") {
			$outString .= '\"';
		} elseif ($inString[$i] == '\\') {
			$outString .= '\\\\';
		} else {
			$outString .= $inString[$i];
		}
	}
	return $outString;
}

?>