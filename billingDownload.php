<?php
	session_start();

	include('Redirect302.php');

	// Logic to ensure that only logged in ADMINs can run the billing report
	if (!isset($_SESSION['accesslvl'])) {
		$_SESSION['msg'] = "SYSTEM ERROR: BAD CREDENTIALS 1A1.";
		Redirect302("Location: index.php");
	} elseif (!isset($_SESSION['appl'])) {
		$_SESSION['msg'] = "SYSTEM ERROR: BAD CREDENTIALS 1S2.";
		Redirect302("Location: index.php");
	} elseif (!isset($_SESSION['user'])) {
		$_SESSION['msg'] = "SYSTEM ERROR: BAD CREDENTIALS 7M3.";
		Redirect302("Location: index.php");
	} elseif (!($_SESSION['appl'] == "HPTTIX")) {
		$_SESSION['msg'] = "SYSTEM ERROR: BAD CREDENTIALS 4X3.";
		Redirect302("Location: index.php");
	} elseif ($_SESSION['accesslvl'] <> "ADMIN") {
		$_SESSION['msg'] = "SYSTEM ERROR: BAD CREDENTIALS L3F.";
		Redirect302("Location: index.php");
	}
// Logic to gather the billing detail and create the billing report and download it as a text file
#
#  We will only print summary reports for now.  Detail report = activity level detail
#
#	if (isset($_GET['detailFlag']) && ($_GET['detailFlag'] <> '')) {
#		$detailFlag = $_GET['detailFlag'];
#	} else {
#		$_SESSION['msg'] = "SYSTEM ERROR: NO DETAIL FLAG.";
#		Redirect302("Location: index.php");
#	}
	$detailFlag = 0;
#
	if (isset($_GET['billingMonth']) && ($_GET['billingMonth'] <> '')) {
		$billingMonth = $_GET['billingMonth'];
	} else {
		$_SESSION['msg'] = "SYSTEM ERROR: NO BILLING MONTH.";
		Redirect302("Location: index.php");
	}
	if (isset($_GET['billingYear']) && ($_GET['billingYear'] <> '')) {
		$billingYear = $_GET['billingYear'];
	} else {
		$_SESSION['msg'] = "SYSTEM ERROR: NO BILLING YEAR.";
		Redirect302("Location: index.php");
	}
	$monthName = array(1 => 'Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
	$incr = 15;
	$freeMinutes = 0;
	$billingStart = mktime(0,0,0,$billingMonth,1,$billingYear);
	$stopMonth = $billingMonth + 1;
	if ($stopMonth == 13) {
		$stopMonth = 1;
		$stopYear = $billingYear + 1;
	} else {
		$stopYear = $billingYear;
	}
	$billingStop = mktime(0,0,0,$stopMonth,1,$stopYear);
	$prevCust = 0;
	$prevCustName = '';
	$prevIncident = '';
	$incidentTotal = 0;
	$incidentPrint = '';
	$custTotal = 0;
	$custPrint = '';
	// Connect to database
	include('dbconn.php');
	$workfile = "tmp/" . rand(1000001,9999999) . "_billing_work";
	$OUTFILE = fopen("$workfile", "w");
	fprintf($OUTFILE, "Billing Report for:  %s %d \r\n\r\n", $monthName[$billingMonth], $billingYear);
	fprintf($OUTFILE, " Summary Listing\r\n\r\n");
#
#  We will only print summary reports for now.  Detail report = activity level detail
#
#	if ($detailFlag == 1) {
#		fprintf($OUTFILE, " Detailed Listing\r\n\r\n");
#	} else {
#		fprintf($OUTFILE, " Summary Listing\r\n\r\n");
#	}
#
	// We get at least one row for every active customer
	$query = "SELECT customers.id as custId, acts.incident, acts.incDesc, acts.actStart, acts.actId, acts.actDuration, ";
	$query .= " customers.name AS custName, customers.active AS custActive from customers ";
	$query .= " LEFT JOIN (SELECT c.cust AS cId, a.incident AS incident, b.shortDesc AS incDesc, a.start AS actStart, a.id AS actId,  ";
	$query .= " a.duration AS actDuration ";
	$query .= " FROM activities a, incidents b, callers c ";
	$query .= " WHERE a.start >= $billingStart ";
	$query .= " AND a.start < $billingStop ";
	$query .= " AND a.billable = 1 ";
	$query .= " AND a.incident = b.id ";
	$query .= " AND b.caller = c.id) as acts ";
	$query .= " ON customers.id = acts.cId ";
	$query .= " ORDER BY custName, incident, actStart";
	$result = mysql_query($query);
	if($result) {
		while($row = mysql_fetch_array($result)) {
			$actId = $row["actId"];
			$custId = $row["custId"];
			$custName = $row["custName"];
			$custActive = $row["custActive"];
			$actStart = $row["actStart"];
			$actDuration = $row["actDuration"];
			$incident = $row["incident"];
			$incDesc = $row["incDesc"];
	// Control break logic
		//   WHEN THE CUSTOMER CHANGES
			if ($custId <> $prevCust) {
				if ($prevCust <> 0) {
					if ($incidentTotal > 0) {
						$incidentPrint .= sprintf (": $ %-5.2f \r\n", $incidentTotal);
						$custPrint .= $incidentPrint;
					}
					$prevIncident = $incident;
					$incidentTotal = 0;
					$incidentPrint = sprintf("  %d: %-35s ", $incident, $incDesc);
					$custPrint .= sprintf("Total for %-47s ==> $ %-5.2f \r\n",$prevCustName, $custTotal);
					if ($custTotal > 0) {
						fprintf($OUTFILE, "$custPrint");
					}
				}
				$custTotal = 0;
				$prevCust = $custId;
				$prevCustName = $custName;
				if ($custActive == 1) {
					$custPrint = " \r\n\r\n";
				} else {
					$custPrint = " \r\n\r\n****INACTIVE**** ";
				}
				$custPrint .= "$custName \r\n";	
				// Logic to get plan info for new customer
				$subQuery = "SELECT a.annualFeeMonth, b.incr, b.annualFee, b.baseFee, b.baseHours, b.overFee ";
				$subQuery .= " FROM customers a, plans b ";
				$subQuery .= " WHERE a.id = $custId ";
				$subQuery .= " AND a.plan = b.id";
				$subResult = mysql_query($subQuery);
				if ($subResult) {
					$subRow = mysql_fetch_array($subResult);
					$annualFeeMonth = $subRow['annualFeeMonth'];
					$incr = $subRow['incr'];
					$annualFee = $subRow['annualFee'];
					$baseFee = $subRow['baseFee'];
					$baseHours = $subRow['baseHours'];
					$overFee = $subRow['overFee'];
				} else {
					die ("Click, click, BOOM!");
				}
				$chargePerMinute = $overFee / 60;
				$freeMinutes = $baseHours * 60;
				if ($billingMonth == $annualFeeMonth) {
					if ($annualFee > 0) {
						$custPrint .= sprintf("    Annual Support Plan Fee :    $ %-5.2f \r\n", $annualFee);
						$custTotal += $annualFee;
					}
				}
				if ($baseFee > 0) {
					$custPrint .= sprintf("    Monthly Support Plan Fee:    $ %-5.2f \r\n", $baseFee);
					$custTotal += $baseFee;
				}
			// Find any other billing items for this customer for this billing period:
				$biQuery = "SELECT `desc`, amount ";
				$biQuery .= " FROM billingItems ";
				$biQuery .= " WHERE cust = $custId ";
				$biQuery .= " AND `date` >= $billingStart ";
				$biQuery .= " AND `date` <= $billingStop";
				$biResult = mysql_query($biQuery);
				if ($biResult) {
					while ($biRow = mysql_fetch_array($biResult)) {
						$desc = $biRow['desc'];
						$amount = $biRow['amount'];
						$custPrint .= sprintf("  %-42s : $ %5.2f \r\n", $desc, $amount);
						$custTotal += $amount;
					}
				}
			}
		// Now report on any incidents with billable activity for the customer
			if ($incident <> $prevIncident) {
				if ($incidentTotal > 0) {
					$incidentPrint .= sprintf (": $ %-5.2f \r\n", $incidentTotal);
					$custPrint .= $incidentPrint;
				}
				$prevIncident = $incident;
				$incidentTotal = 0;
				$incidentPrint = sprintf("  %d: %-35s ", $incident, $incDesc);
			}	
		// Logic to calculate $actFees
			if ($actId <> '') {
				$actIntervals = (int)($actDuration / $incr);
				if (($actDuration % $incr) > 0) {
					$actIntervals++;
				}
				$actMinutes = $actIntervals * $incr;
				if ($freeMinutes >= $actMinutes) {
					$freeMinutes -= $actMinutes;
					$actFee = 0;
				} else {
					if ($freeMinutes > 0) {
						$actMinutes -= $freeMinutes;
						$freeMinutes = 0;
					}
					$actFee = $actMinutes * $chargePerMinute;
				}
				$incidentTotal += $actFee;
				$custTotal += $actFee;
			}			
		}
		if ($prevCust <> 0) {
			if ($incidentTotal > 0) {
				$incidentPrint .= sprintf (": $ %-5.2f \r\n", $incidentTotal);
				$custPrint .= $incidentPrint;
			}
			$custPrint .= sprintf("Total for %-47s ==> $ %-5.2f \r\n",$prevCustName, $custTotal);
			if ($custTotal > 0) {
				fprintf($OUTFILE, "$custPrint");
			}
		}
	} else {
		$_SESSION['msg'] = "SELECT ERROR: " . $query . "\n billingStart is " . $billingStart . "\n billingMonth is " . $billingMonth . "\n billingYear is " . $billingYear;
		Redirect302("Location: index.php");
	}
	fclose($OUTFILE);
	// Having created the file, download it
	$fd = fopen ($workfile, "r");
	$fsize = filesize($workfile);
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	header("Content-Type: text/plain" );
	header("Content-Disposition: attachment; filename=\"billing.txt\"");
	header("Content-Transfer-Encoding:­ binary");
	header("Content-Length: " . $fsize);
	while(!feof($fd)) {
		$buffer = fread($fd, 2048);
		echo $buffer;
		flush();
	}
	fclose ($fd);
	unlink ($workfile);
	
?>
