<?php

function updateIncident() {
	$now = time();
	$doneBy = $_SESSION['user'];
	$openInd = $_POST['openInd'];
	$status = $_POST['status'];
	$severity = $_POST['severity'];
	$priority = $_POST['priority'];
	$billable = $_POST['billable'];
	$tix = $_POST['tix'];
	// Connect to database
	include('dbconn.php');
	// First, find out what changed and remember it
	$sql = "SELECT openInd, status, severity, priority, billable ";
	$sql .= " FROM incidents WHERE id = $tix";
	$result = mysql_query($sql);
	if ($result) {
		$row = mysql_fetch_array($result);
		$oldOpenInd = $row["openInd"];
		$oldStatus = $row["status"];
		$oldSeverity = $row["severity"];
		$oldPriority = $row["priority"];
		$oldBillable = $row['billable'];
	} else {
		$_SESSION['msg'] = "$sql<br />SYSTEM ERROR: BAD ACTIVITY RETRIEVE";
		$oldOpenInd = NULL;
		$oldStatus = NULL;
		$oldSeverity = NULL;
		$oldPriority = NULL;
		$oldBillable = NULL;
		
	}
	// Then update the incident with the new values
	$query = "UPDATE incidents ";
	$query .= " SET openInd = $openInd, status = $status, severity = $severity, priority = $priority, billable = $billable ";
	$query .= " WHERE id = $tix";
	$result = mysql_query($query);
	// Create an activity record to describe what was changed, for each change
	if ($oldOpenInd <> $openInd) {
		if ($oldOpenInd == 1) {
			$oldValue = "Open";
		} else {
			$oldValue = "Closed";
		}
		if ($openInd == 1) {
			$newValue = "Open";
		} else {
			$newValue = "Closed";
		}
		$query = "INSERT INTO activities (id, incident, `oper`, `start`, duration, `desc`, reportable, billable) ";
		$query .= " VALUES ('', $tix, $doneBy, $now, 0, 'Ticket Open/Closed Indicator changed from $oldValue to $newValue', 1, 0)";
  		$result = mysql_query($query);
  		if (! $result) {
			$_SESSION['msg'] = "$query<br />DATABASE INSERT ERROR 521";
			mysql_close($conn); 
			return;
		}
	}
	if ($oldStatus <> $status) {
		$sql1 = "SELECT name FROM statuses WHERE id = $oldStatus";
		$result1 = mysql_query($sql1);
		$row1 = mysql_fetch_array($result1);
		$oldValue = $row1["name"];
		$sql2 = "SELECT name FROM statuses WHERE id = $status";
		$result2 = mysql_query($sql2);
		$row2 = mysql_fetch_array($result2);
		$newValue = $row2["name"];
		$query = "INSERT INTO activities (id, incident, `oper`, `start`, duration, `desc`, reportable, billable) ";
		$query .= " VALUES ('', $tix, $doneBy, $now, 0, 'Ticket Status changed from $oldValue to $newValue', 1, 0)";
  		$result = mysql_query($query);
  		if (! $result) {
			$_SESSION['msg'] = "$query<br />DATABASE INSERT ERROR 522";
			mysql_close($conn); 
			return;
		}
	}
	if ($oldSeverity <> $severity) {
		$sql1 = "SELECT name FROM severities WHERE id = $oldSeverity";
		$result1 = mysql_query($sql1);
		$row1 = mysql_fetch_array($result1);
		$oldValue = $row1["name"];
		$sql2 = "SELECT name FROM severities WHERE id = $severity";
		$result2 = mysql_query($sql2);
		$row2 = mysql_fetch_array($result2);
		$newValue = $row2["name"];
		$query = "INSERT INTO activities (id, incident, `oper`, `start`, duration, `desc`, reportable, billable) ";
		$query .= " VALUES ('', $tix, $doneBy, $now, 0, 'Ticket Severity changed from $oldValue to $newValue', 1, 0)";
  		$result = mysql_query($query);
  		if (! $result) {
			$_SESSION['msg'] = "$query<br />DATABASE INSERT ERROR 523";
			mysql_close($conn); 
			return;
		}
	}
	if ($oldPriority <> $priority) {
		$sql1 = "SELECT name FROM priorities WHERE id = $oldPriority";
		$result1 = mysql_query($sql1);
		$row1 = mysql_fetch_array($result1);
		$oldValue = $row1["name"];
		$sql2 = "SELECT name FROM priorities WHERE id = $priority";
		$result2 = mysql_query($sql2);
		$row2 = mysql_fetch_array($result2);
		$newValue = $row2["name"];
		$query = "INSERT INTO activities (id, incident, `oper`, `start`, duration, `desc`, reportable, billable) ";
		$query .= " VALUES ('', $tix, $doneBy, $now, 0, 'Ticket Priority changed from from $oldValue to $newValue', 1, 0)";
  		$result = mysql_query($query);
  		if (! $result) {
			$_SESSION['msg'] = "$query<br />DATABASE INSERT ERROR 524";
			mysql_close($conn); 
			return;
		}
	}
	if ($oldBillable <> $billable) {
		if ($oldBillable == 1) {
			$oldValue = "Billable";
		} else {
			$oldValue = "Not Billable";
		}
		if ($billable == 1) {
			$newValue = "Billable";
		} else {
			$newValue = "Not Billable";
		}
		$query = "INSERT INTO activities (id, incident, `oper`, `start`, duration, `desc`, reportable, billable) ";
		$query .= " VALUES ('', $tix, $doneBy, $now, 0, 'Ticket Billable Indicator changed from $oldValue to $newValue', 0, 0)";
  		$result = mysql_query($query);
  		if (! $result) {
			$_SESSION['msg'] = "$query<br />DATABASE INSERT ERROR 525";
			mysql_close($conn); 
			return;
		}
	}
	// Close database connection and unset eitem
	mysql_close($conn);
	Redirect302("Location: index.php?func=incidentDetail&incident=$tix");
}

include ('Redirect302.php');

?>