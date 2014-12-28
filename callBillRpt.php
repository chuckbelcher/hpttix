<?php

include('Redirect302.php');

	if (isset($_POST['billingMonth'])) {
		$billingMonth = $_POST['billingMonth'];
		$billingYear = $_POST['billingYear'];
		$detailFlag = $_POST['detailFlag'];
		Redirect302("Location: billingDownload.php?billingMonth=$billingMonth&billingYear=$billingYear&detailFlag=$detailFlag");
	} else {
		print "<h2> BILLING REPORT CALL ERROR. </h2>\n";
	}

?>