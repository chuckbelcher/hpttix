<?php 
//
//  dollars.php is a PHP component of HPTTix
//  Copyright 2014 John Francis, HPT Solutions, Inc.  
//  Licensed under the Open Software License (OSL 3.0).
//  

function dollars($inString) {
	// This function drops everything but numbers and the first decimal point and returns dollars and cents
	$outString = "";
	$gotDecimal = 0;
	for ($i = 0; $i < strlen($inString); $i++) {
		if (($inString[$i] == ".") && ($gotDecimal == 0)) { 
			$outString .= $inString[$i];
			$gotDecimal = 1;
		} elseif (($inString[$i] >= "0") && ($inString[$i] <= "9")) {
			$outString .= $inString[$i];
		}
	}
	$outFmtString = sprintf("%6.2f",$outString);
	return $outFmtString;
}

?>