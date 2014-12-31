<?php
//
//  index.php is a PHP component of HPTTix
//  Copyright 2014 John Francis, HPT Solutions, Inc.  
//  Licensed under the Open Software License (OSL 3.0).
// 
 
session_start();
$func = "";
$funcname = "";
$puser = "";
$ptix = "";
putenv("TZ=US/Eastern");
$bannerTime = date ('M j, Y') . " &nbsp &nbsp " . date ('g:i A');

include('printTop.php');

if (isset($_GET['func'])) {
	$funcname = $_GET['func'];
	$funcname .= ".php";
	include($funcname);
} else {
	$funcname = "";
}
if (isset($_SESSION['user'])) {
	$user = $_SESSION['user'];
	$puser = $_SESSION['userid'];
	include('dbconn.php');
	$queryx = "SELECT count(*) as COUNT from incidents ";
	$queryx .= "WHERE openInd = 1 and assignedTo = $user";
	$resultx = mysql_query($queryx);
	if($resultx) {
		$rowx = mysql_fetch_array($resultx);	
		$ptix = $rowx["COUNT"];
	} else {
		$ptix = "D/B Error";
	}
 	mysql_close($conn); 
} else {
	$puser = "Not Logged In";
	$ptix = "N/A";
}
	if (!isset($_SESSION['accesslvl'])) {
		login();
	} elseif (!isset($_SESSION['appl'])) {
		login();
	} elseif (!isset($_SESSION['user'])) {
		login();
	} elseif (!($_SESSION['appl'] == "HPTTIX")) {
		login();
	} else {
		if (isset($_GET['func'])) {
 			$func = $_GET['func'];	
 		}
// THIS IS WHERE TO CHANGE THE FUNCTIONS (eg. new incident, maintain plans, etc. 		
			if (($func == 'incidentDetail') && (($_SESSION['accesslvl'] == "ADMIN") || ($_SESSION['accesslvl'] == "USER"))) {
				incidentDetail();
			} elseif (($func == 'newIncident') && (($_SESSION['accesslvl'] == "ADMIN") || ($_SESSION['accesslvl'] == "USER"))) {
				newIncident();
			} elseif (($func == 'updateIncident') && (($_SESSION['accesslvl'] == "ADMIN") || ($_SESSION['accesslvl'] == "USER"))) {
				updateIncident();
			} elseif (($func == 'newActivity') && (($_SESSION['accesslvl'] == "ADMIN") || ($_SESSION['accesslvl'] == "USER"))) {
				newActivity();
			} elseif (($func == 'updateActivity') && ($_SESSION['accesslvl'] == "ADMIN")) {
				updateActivity();
			} elseif (($func == 'deleteActivity') && ($_SESSION['accesslvl'] == "ADMIN")) {
				deleteActivity();
			} elseif (($func == 'mntCallers') && (($_SESSION['accesslvl'] == "ADMIN") || ($_SESSION['accesslvl'] == "USER"))) {
				mntCallers();
			} elseif (($func == 'newpass') && (($_SESSION['accesslvl'] == "ADMIN") || ($_SESSION['accesslvl'] == "USER"))) {
				newpass();
			} elseif (($func == 'mntCustomers') && ($_SESSION['accesslvl'] == "ADMIN")) {
				mntCustomers();
			} elseif (($func == 'mntBillItems') && ($_SESSION['accesslvl'] == "ADMIN")) {
				mntBillItems();
			} elseif (($func == 'mntOperators') && ($_SESSION['accesslvl'] == "ADMIN")) {
				mntOperators();
			} elseif (($func == 'mntPlans') && ($_SESSION['accesslvl'] == "ADMIN")) {
				mntPlans();
			} elseif (($func == 'mntSeverities') && ($_SESSION['accesslvl'] == "ADMIN")) {
				mntSeverities();
			} elseif (($func == 'mntStatuses') && ($_SESSION['accesslvl'] == "ADMIN")) {
				mntStatuses();
			} elseif (($func == 'mntPriorities') && ($_SESSION['accesslvl'] == "ADMIN")) {
				mntPriorities();
			} elseif (($func == 'mntCategories') && ($_SESSION['accesslvl'] == "ADMIN")) {
				mntCategories();
			} elseif (($func == 'billingReport') && ($_SESSION['accesslvl'] == "ADMIN")) {
				billingReport();
			} elseif (($_SESSION['accesslvl'] == "ADMIN") || ($_SESSION['accesslvl'] == "USER")) {
// THIS IS THE DASHBOARD.  REMEMBER, WE KEEP FILTERS FOR THE DASHBOARD (ONLY) IN SESSION VARIABLES
				printTop();
				print "<div id='profile'><h5><a href='index.php?func=newpass'>Change Your Password</a></h5></div>\n";
				print "<div id='nav'><h5>Dashboard | <a href='logout.php'>Logout</a></h5></div>\n";
 				print "<div id='titles'><br /><br />\n";
				maintLinks();
				print "<hr />\n";
				incidentList();
 				print "</div>\n";
 			}
 		}
		print "<br /><br /><div id=\"footer\"><h5>&copy;2011 <a href=\"http://www.PateoGroup.com\">Pateo Group, Inc.</a> ";
		print "All Rights Reserved.</h5></div>";
		print "</div>\n";
  	print "</body>\n";

function login() {
	printTop();
	print "<div id=\"logindiv\"><br />\n";
	print " <form action=\"checklogin.php\" name=\"logform\" method=\"post\"> \n";
	print "	<table width=\"100\" align=\"left\" class=\"logintable\"><tr><td class=\"little\" colspan=\"2\">Operators Login Here</th></tr> \n";
	if (isset($_SESSION['status'])) {
		if  ($_SESSION['status'] == "INV") {
			print "  <tr><td class=\"red\" colspan=\"2\">Invalid Userid or Password.</td></tr> \n";
		} else {
			print "  <tr><td class=\"red\" colspan=\"2\"> &nbsp; </td></tr> \n";
		}
	} else {
		print "  <tr><td class=\"red\" colspan=\"2\"> &nbsp; </td></tr> \n";
	}
	print "    <tr><td class=\"little\" align=\"right\">Userid:</td><td class=\"little\" align=\"left\"><input type=\"text\" name=\"userid\" /></td></tr> \n";
	print "    <tr><td class=\"little\" align=\"right\">Password:</td><td class=\"little\" align=\"left\"><input type=\"password\" name=\"password\" /></td></tr> \n";
	print "    <tr><td class=\"little\" colspan=\"2\"><input type=\"submit\" name=\"submit\" value=\"Login\" /></td></tr> \n";
	print "  </table>";
	print " </form> \n";
	print " <script type=\"text/javascript\"> \n";
	print "  document.logform.userid.focus(); \n";
	print " </script> \n";
	include('sysDescription.php');
	print "</div>\n";
}

function maintLinks() {
	print "<div id=\"maintNavRight\">";
	print "<div class=\"addButton\"><a class=\"add\" href=\"index.php?func=newIncident\">New<br />Ticket</a></div>";
	print "<div class=\"navButton\"><a class=\"roll\" href=\"index.php?func=mntCallers\">Maintain<br />Callers</a></div>";
	if ($_SESSION['accesslvl'] == "ADMIN") {
		print "<div class=\"navButton\"><a class=\"roll\" href=\"index.php?func=mntCustomers\">Maintain Customers</a></div>";
		print "<div class=\"navButton\"><a class=\"roll\" href=\"index.php?func=mntPlans\">Maintain<br />Plans</a></div>";
		print "<div class=\"navButton\"><a class=\"roll\" href=\"index.php?func=mntOperators\">Maintain Operators</a></div>";
		print "<div class=\"navButton\"><a class=\"roll\" href=\"index.php?func=mntSeverities\">Maintain Severities</a></div>";
		print "<div class=\"navButton\"><a class=\"roll\" href=\"index.php?func=mntStatuses\">Maintain Statuses</a></div>";
		print "<div class=\"navButton\"><a class=\"roll\" href=\"index.php?func=mntPriorities\">Maintain Priorities</a></div>";
		print "<div class=\"navButton\"><a class=\"roll\" href=\"index.php?func=mntCategories\">Maintain Categories</a></div>";
		print "<div class=\"billButton\"><a class=\"bill\" href=\"index.php?func=billingReport\">Billing<br />Report</a></div>";
	}
	print "</div><div id=\"clear\">&nbsp</div>";
}

function incidentList() {

// FIRST, DISPLAY AND MANAGE THE FILTERS FOR WHICH TICKETS TO LIST

	include('dbconn.php');
	if (isset($_POST['filterOpen']) && $_POST['filterOpen'] <> "") {
		$filterOpen = $_POST['filterOpen'];
		$_SESSION['filterOpen'] = $filterOpen;
	} elseif (isset($_GET['filterOpen'])) {
		$filterOpen = $_GET['filterOpen'];
		$_SESSION['filterOpen'] = $filterOpen;
	} else {
		$filterOpen = $_SESSION['filterOpen'];
	}
	if (isset($_POST['filterStat']) && $_POST['filterStat'] <> "") {
		$filterStat = $_POST['filterStat'];
		$_SESSION['filterStat'] = $filterStat;
	} elseif (isset($_GET['filterStat'])) {
		$filterStat = $_GET['filterStat'];
		$_SESSION['filterStat'] = $filterStat;
	} else {
		$filterStat = $_SESSION['filterStat'];
	}
	if (isset($_POST['filterAssign']) && $_POST['filterAssign'] <> "") {
		$filterAssign = $_POST['filterAssign'];
		$_SESSION['filterAssign'] = $filterAssign;
	} elseif (isset($_GET['filterAssign'])) {
		$filterAssign = $_GET['filterAssign'];
		$_SESSION['filterAssign'] = $filterAssign;
	} else {
		$filterAssign = $_SESSION['filterAssign'];
	}
	if (isset($_POST['filterCat']) && $_POST['filterCat'] <> "") {
		$filterCat = $_POST['filterCat'];
		$_SESSION['filterCat'] = $filterCat;
	} elseif (isset($_GET['filterCat'])) {
		$filterCat = $_GET['filterCat'];
		$_SESSION['filterCat'] = $filterCat;
	} else {
		$filterCat = $_SESSION['filterCat'];
	}
	if (isset($_POST['filterCust']) && $_POST['filterCust'] <> "") {
		$filterCust = $_POST['filterCust'];
		$_SESSION['filterCust'] = $filterCust;
	} elseif (isset($_GET['filterCust'])) {
		$filterCust = $_GET['filterCust'];
		$_SESSION['filterCust'] = $filterCust;
	} else {
		$filterCust = $_SESSION['filterCust'];
	}
	$rowsPerPage = 50;
	$pageNum = 1;
	if(isset($_GET['page'])) {
		$pageNum = $_GET['page'];
	}
	$offset = ($pageNum - 1) * $rowsPerPage;
	
	print "<form name=\"incidentList\" id=\"incidentList\" method=\"post\" action=\"index.php\">\n";
	print "<div id=\"filters\"><table width=\"100%\" align=\"center\" cellpadding=\"2\" border=\"1\">";
	print "<tr>";
	print "<td align=\"right\"><b>Assign: </b> ";
	print "<select name=\"filterAssign\">";
	if ($filterAssign == 0) {
		print "<option value=\"0\">ALL</option>";
	} else {
		$query = "SELECT name from operators where id = $filterAssign"; 
		$result = mysql_query($query);
		if ($result) {
			while ($row = mysql_fetch_array($result)) {
				$id = $row["id"];
				$name = $row["name"];
				print "<option value=\"$id\">$name</option>";
			}
		}
	}
	$query = "SELECT id, name from operators where id <> $filterAssign and canLogin = 1 order by name";
	$result = mysql_query($query);
	if ($result) {
		while ($row = mysql_fetch_array($result)) {
			$id = $row["id"];
			$name = $row["name"];
			print "<option value=\"$id\">$name</option>";
		}
	} 
	if ($filterAssign != 0) {
		print "<option value=\"0\">ALL</option>";
	}	
	print "</select></td>";
	print "<td align=\"right\"><b>Stat: </b>";
	print "<select name=\"filterStat\">";
	if ($filterStat == 0) {
		print "<option value=\"0\">ALL</option>";
	} else {
		$query = "SELECT name from statuses where id = $filterStat"; 
		$result = mysql_query($query);
		if ($result) {
			while ($row = mysql_fetch_array($result)) {
				$id = $row["id"];
				$name = $row["name"];
				print "<option value=\"$id\">$name</option>";
			}
		}
	}
	$query = "SELECT id, name from statuses where id <> $filterStat order by name";
	$result = mysql_query($query);
	if ($result) {
		while ($row = mysql_fetch_array($result)) {
			$id = $row["id"];
			$name = $row["name"];
			print "<option value=\"$id\">$name</option>";
		}
	} 
	if ($filterStat != 0) {
		print "<option value=\"0\">ALL</option>";
	}
	print "</select> &nbsp ";
	print "<select name=\"filterOpen\">";
	if ($filterOpen == 0) {
		print "<option value=\"0\">ALL</option>";
		print "<option value=\"1\">OPEN</option>";
		print "<option value=\"2\">CLOSED</option>";
	} elseif ($filterOpen == 1) {
		print "<option value=\"1\">OPEN</option>";
		print "<option value=\"2\">CLOSED</option>";
		print "<option value=\"0\">ALL</option>";
	} else {
		print "<option value=\"2\">CLOSED</option>";
		print "<option value=\"1\">OPEN</option>";
		print "<option value=\"0\">ALL</option>";
	}
	print "</td>";
	print "<td align=\"right\"><b>Cust: </b>";
	print "<select name=\"filterCust\">";
	if ($filterCust == 0) {
		print "<option value=\"0\">ALL</option>";
	} else {
		$query = "SELECT name from customers where id = $filterCust"; 
		$result = mysql_query($query);
		if ($result) {
			while ($row = mysql_fetch_array($result)) {
				$id = $row["id"];
				$name = $row["name"];
				print "<option value=\"$id\">$name</option>";
			}
		}
	}
	$query = "SELECT id, name from customers where id <> $filterCust order by name";
	$result = mysql_query($query);
	if ($result) {
		while ($row = mysql_fetch_array($result)) {
			$id = $row["id"];
			$name = $row["name"];
			print "<option value=\"$id\">$name</option>";
		}
	} 
	if ($filterCust != 0) {
		print "<option value=\"0\">ALL</option>";
	}
	print "</select></td>";
	print "<td align=\"right\"><b>Cat: </b>";
	print "<select name=\"filterCat\">";
	if ($filterCat == 0) {
		print "<option value=\"0\">ALL</option>";
	} else {
		$query = "SELECT name from categories ";
		$query .= "WHERE id = $filterCat ";
		$query .= "AND cust in (0, $filterCust)";
		$result = mysql_query($query);
		if ($result) {
			while ($row = mysql_fetch_array($result)) {
				$id = $row["id"];
				$name = $row["name"];
				print "<option value=\"$id\">$name</option>";
			}
		}
	}
	$query = "SELECT id, name from categories ";
	$query .= "WHERE id <> $filterCat ";
	$query .= "AND cust in (0, $filterCust)";
	$query .= "order by name";
	$result = mysql_query($query);
	if ($result) {
		while ($row = mysql_fetch_array($result)) {
			$id = $row["id"];
			$name = $row["name"];
			print "<option value=\"$id\">$name</option>";
		}
	} 
	if ($filterCat != 0) {
		print "<option value=\"0\">ALL</option>";
	}
	print "</select></td>";
	print "<td><input type=\"Submit\" value=\"Refresh\"></td></tr>";
	print "</table>";
	print "</div>\n";

// THIS IS WHERE WE CREATE THE LIST OF INCIDENT TICKETS

	print "<div id=\"incList\"><table width=\"100%\" align=\"center\" cellpadding=\"2\" border=\"1\">\n";
	print "<tr><th>TIX</th><th>Customer</th><th>Desc</th><th>Sev</th><th>Pri</th><th>Status</th></tr>\n";
	$query2 = "SELECT a.id as INC, b.name as CUST, a.shortDesc as SDESC, d.name as SEV, e.name as PRI, f.name as STAT ";
	$query2 .= " FROM incidents a, customers b, callers c, severities d, priorities e, statuses f ";
	$query2 .= " WHERE a.caller = c.id AND c.cust = b.id AND a.severity = d.id AND a.priority = e.id AND a.status = f.id ";
	if ($filterAssign != 0) {
		$query2 .= " AND a.assignedTo = $filterAssign ";
	}
	if ($filterStat != 0) {
		$query2 .= " AND a.status = $filterStat ";
	}
	if ($filterOpen == 1) {
		$query2 .= " AND a.openInd = 1 ";
	} elseif ($filterOpen == 2) {
		$query2 .= " AND a.openInd = 0 ";
	}
	if ($filterCust != 0) {
		$query2 .= " AND c.cust = $filterCust ";
	}
	if ($filterCat != 0) {
		$query2 .= " AND a.category = $filterCat ";
	}
	$query2 .= " ORDER BY a.id ";
	$query2 .= " LIMIT $offset, $rowsPerPage";
	$result2 = mysql_query($query2);
	if($result2) {
		while ($row2 = mysql_fetch_array($result2)) {	
			$inc = $row2["INC"];
			$cust = $row2["CUST"];
			$desc = $row2["SDESC"];
			$sev = $row2["SEV"];
			$pri = $row2["PRI"];
			$stat = $row2["STAT"];
			print "<tr><td><a href=\"index.php?func=incidentDetail&incident=$inc\">$inc</a></td>";
			print "<td>$cust</td><td>$desc</td><td>$sev</td><td>$pri</td><td>$stat</td></tr>";
		}
	}
	print "</table><br />\n";
	// how many rows we have in database
	$query = "SELECT a.id as INC, b.name as CUST, a.shortDesc as SDESC, d.name as SEV, e.name as PRI, f.name as STAT ";
	$query .= " FROM incidents a, customers b, callers c, severities d, priorities e, statuses f ";
	$query .= " WHERE a.caller = c.id AND c.cust = b.id AND a.severity = d.id AND a.priority = e.id AND a.status = f.id ";
	if ($filterAssign != 0) {
		$query .= " AND a.assignedTo = $filterAssign ";
	}
	if ($filterStat != 0) {
		$query .= " AND a.status = $filterStat ";
	}
	if ($filterCust != 0) {
		$query .= " AND c.cust = $filterCust ";
	}
	if ($filterCat != 0) {
		$query .= " AND a.category = $filterCat ";
	}
	$result  = mysql_query($query) or die('Error, query failed');
	$numrows = mysql_num_rows($result);
	// how many pages we have when using paging?
	$maxPage = ceil($numrows/$rowsPerPage);
	$self = $_SERVER['PHP_SELF'];
	// creating 'previous' and 'next' link
	// plus 'first page' and 'last page' link

	// print 'previous' link only if we're not
	// on page one
	if ($pageNum > 1) {
		$page = $pageNum - 1;
		$prev = " <a href=\"$self?page=$page\">[Prev Page]</a> ";
		$first = " <a href=\"$self?page=1\">[First Page]</a> ";
	} else {
		$prev  = ' [Prev Page] ';       // we're on page one, don't enable 'previous' link
		$first = ' [First Page] '; // nor 'first page' link
	}

	// print 'next' link only if we're not
	// on the last page
	if ($pageNum < $maxPage) {
		$page = $pageNum + 1;
		$next = " <a href=\"$self?page=$page\">[Next Page]</a> ";
		$last = " <a href=\"$self?page=$maxPage\">[Last Page]</a> ";
	} else {
		$next = ' [Next Page] ';      // we're on the last page, don't enable 'next' link
		$last = ' [Last Page] '; // nor 'last page' link
	}

	// print the page navigation link
	print "<table width=\"100%\"><tr>";
	print "<td> $first </td><td> $prev </td><td> Page <strong>$pageNum</strong> of <strong>$maxPage</strong> </td>";
	print "<td align=\"right\"> $next </td><td align=\"right\"> $last </td></tr>";
	print "</table>\n";
	print "</div></form>\n";
 	mysql_close($conn); 
}	
?>