<?php
//
//  mntuser.php is a PHP component of HPTTix
//  Copyright 2014 John Francis, HPT Solutions, Inc.  
//  Licensed under the Open Software License (OSL 3.0).
//  
function mntuser() {
	echo "<div id='nav'><h6><a href='index.php'>Main Menu</a> | <a href='logout.php'>Logout</a></h6></div>\n";
	echo "<br /><br /><h3>Maintain Users</h3><br />";
	if (isset($_POST['userid'])) {
		if (validInsert()) {
			insertUser();
			echo "<br /><div id='msg'><h4>New User Added</h4></div><br /><br /> \n";
		} else {
			echo "<br /><div id='msg'><h4>Can not add - userid already exists</h4><br /><br /> \n";
		}
		displayUserform();
		displayUsers();
	}
	elseif (isset($_GET['edituser'])) {
		editUserForm();
	}
	elseif (isset($_POST['euser'])) { 
		editUpdateUser();
		echo "<br /><div id='msg'><h4>User Updated</h4></div><br /><br /> \n";
		displayUserform();
		displayUsers();
	}
	else {
		displayUserform();
		displayUsers(); 
	}
}

function editUpdateUser() {
	$USER = $_POST['euser'];
	$USERID = $_POST['euserid'];
	$PASS = $_POST['epass'];
	$REGDATE = $_POST['eregdate'];
	$EXPDATE = $_POST['eexpdate'];
	$FNAME = $_POST['efname'];
	$LNAME = $_POST['elname'];
	$EMAIL = $_POST['eemail'];
	$DEFHOST = $_POST['edefhost'];
	$DEFUSER = $_POST['edefuser'];
	$DEFPASS = $_POST['edefpass'];
	$HFDOB = $_POST['ehfdob'];
	$HFDOD = $_POST['ehfdod'];
	$HFCOMM = $_POST['ehfcomm'];
	$HFCONT = $_POST['ehfcont'];
	// Connect to database
	include('dbconn.php');
	// See if expiration date was changed, if so log activity
	$queryx = "SELECT expdate from dpx_sys_users ";
	$queryx .= "WHERE ctlnum = $USER";
	$resultx = mysql_query($queryx);
	if($resultx) {
		$rowx = mysql_fetch_array($resultx);	
		$oldExpDate = $rowx["expdate"];
		if ($EXPDATE <> $oldExpDate) {
				putenv("TZ=US/Eastern");
				$todayYY = date ('Y');
				$todayMM = date ('m');
				$todayDD = date ('d');
				$todayHr = date ('H');
				$todayMn = date ('i');
				$todaySc = date ('s');
				$alDate = sprintf("%04d-%02d-%02d", $todayYY, $todayMM, $todayDD);
				$alTime = sprintf("%02d:%02d:%02d", $todayHr, $todayMn, $todaySc);
				$alEvent = 3;                    //  The event code (int)
				$alVarData = "Old Exp: $oldExpDate --- New Exp: $EXPDATE";            //  Variable data for the event log
				$query = "INSERT INTO dpx_activity_log (date, time, user, event, vardata) ";
				$query .= "VALUES ('$alDate', '$alTime', $USER, $alEvent, '$alVarData')";
				$result = mysql_query($query);
			}
		}
	// Post edited item to database
	$sql = "UPDATE dpx_sys_users SET userid = '$USERID', ";
	// Only set the password if it was entered
	if ($PASS <> "") {
		$sql .= "password = '";
		$sql .= sha1($PASS);
		$sql .= "', ";
		$newPass = 1;
	} else {
		$newPass = 0;
	}
	$sql .= " regdate = '$REGDATE', expdate = '$EXPDATE', fname = '$FNAME', ";
	$sql .= " lname = '$LNAME', email = '$EMAIL', default_ftphost = '$DEFHOST', ";
	$sql .= " default_ftpuser = '$DEFUSER', default_ftppass = '$DEFPASS', ";
	$sql .= "def_hfdob = '$HFDOB', def_hfdod = '$HFDOD', def_hfcomm = '$HFCOMM', ";
	$sql .= "def_hfcont = '$HFCONT' ";
	$sql .= "WHERE ctlnum = $USER";
	$result = mysql_query($sql);
	if ($newPass == 1) {
		putenv("TZ=US/Eastern");
		$todayYY = date ('Y');
		$todayMM = date ('m');
		$todayDD = date ('d');
		$todayHr = date ('H');
		$todayMn = date ('i');
		$todaySc = date ('s');
		$alDate = sprintf("%04d-%02d-%02d", $todayYY, $todayMM, $todayDD);
		$alTime = sprintf("%02d:%02d:%02d", $todayHr, $todayMn, $todaySc);
		$alEvent = 11;                    //  The event code (int)
		$alVarData = "";            //  Variable data for the event log
		$query = "INSERT INTO dpx_activity_log (date, time, user, event, vardata) ";
		$query .= "VALUES ('$alDate', '$alTime', $USER, $alEvent, '$alVarData')";
		$result = mysql_query($query);
	}
	
	// Close database connection and unset eitem
	mysql_close($conn);
	unset($_POST['euser']);
}

function editUserForm() {
	$user = $_GET['edituser'];
	$pageNum = $_GET['page'];
	// Connect to database
	include('dbconn.php');

	$query = "SELECT CTLNUM, USERID, ACCESSLVL, REGDATE, EXPDATE, FNAME, LNAME, EMAIL, DEFAULT_FTPHOST, ";
	$query .= " DEFAULT_FTPUSER, DEFAULT_FTPPASS, ";
	$query .= " DEF_HFDOB, DEF_HFDOD, DEF_HFCOMM, DEF_HFCONT ";
	$query .= " from dpx_sys_users where ctlnum='$user'";
	$result = mysql_query($query);
	if($result) {
		$row = mysql_fetch_array($result);	
		$user = $row["CTLNUM"];
		$userid = $row["USERID"];
		$accesslvl = $row["ACCESSLVL"];
		$regdate = $row["REGDATE"];
		$expdate = $row["EXPDATE"];
		$fname = $row["FNAME"];
		$lname = $row["LNAME"];
		$email = $row["EMAIL"];
		$defhost = $row["DEFAULT_FTPHOST"];
		$defuser = $row["DEFAULT_FTPUSER"];
		$defpass = $row["DEFAULT_FTPPASS"];
		$hfdob = $row["DEF_HFDOB"];
		$hfdod = $row["DEF_HFDOD"];
		$hfcomm = $row["DEF_HFCOMM"];
		$hfcont = $row["DEF_HFCONT"];
		print "<form action=\"index.php?func=mntuser&page=$pageNum\" method=\"post\">\n";
		print "<table width=\"80%\" align=\"center\">";
		print "<tr><td align=\"right\">Userid: </td>";
		print "<td align=\"left\" colspan=\"2\"><input type=\"text\" size=\"50\" name=\"euserid\" value=\"$userid\"</td></tr>\n";
		print "<tr><td align=\"right\">Password: </td>";
		print "<td align=\"left\" colspan=\"2\"><input type=\"text\" size=\"50\" name=\"epass\"></td></tr>\n";
		print "<tr><td> &nbsp </td><td colspan=\"2\"><h5> Only include if changing</h5></td></tr>";
		print "<tr><td> &nbsp </td><td colspan=\"2\"> &nbsp </td></tr>";
		print "<tr><td align=\"right\">Access Level: </td>";
		print "<td align=\"left\" colspan=\"2\">$accesslvl</td></tr>";
		print "<tr><td> &nbsp </td><td colspan=\"2\"> &nbsp </td></tr>";
		print "<tr><td align=\"right\">First Name: </td>";
		print "<td align=\"left\" colspan=\"2\"><input type=\"text\" size=\"50\" name=\"efname\" value=\"$fname\"></td></tr>\n";
		print "<tr><td align=\"right\">Last Name: </td>";
		print "<td align=\"left\" colspan=\"2\"><input type=\"text\" size=\"50\" name=\"elname\" value=\"$lname\"></td></tr>\n";
		print "<tr><td align=\"right\">Email: </td>";
		print "<td align=\"left\" colspan=\"2\"><input type=\"text\" size=\"50\" name=\"eemail\" value=\"$email\"></td></tr>\n";
		print "<tr><td> &nbsp </td><td colspan=\"2\"> &nbsp </td></tr>";
		print "<tr><td align=\"right\">Registration Date: </td>";
		print "<td align=\"left\"><input type=\"text\" size=\"10\" name=\"eregdate\" value=\"$regdate\"></td>";
		print "<td align=\"left\"><h5>(YYYY-MM-DD)</h5></td></tr>\n";
		print "<tr><td align=\"right\">Expiration Date: </td>";
		print "<td align=\"left\"><input type=\"text\" size=\"10\" name=\"eexpdate\" value=\"$expdate\"></td>";
		print "<td align=\"left\"><h5>(YYYY-MM-DD)</h5></td></tr>\n";
		print "<tr><td> &nbsp </td><td colspan=\"2\"> &nbsp </td></tr>";
		print "<tr><td align=\"right\">Default FTP Host: </td>";
		print "<td align=\"left\" colspan=\"2\"><input type=\"text\" size=\"50\" name=\"edefhost\" value=\"$defhost\"></td></tr>\n";
		print "<tr><td align=\"right\">Default FTP Userid: </td>";
		print "<td align=\"left\" colspan=\"2\"><input type=\"text\" size=\"50\" name=\"edefuser\" value=\"$defuser\"></td></tr>\n";
		print "<tr><td align=\"right\">Default FTP Password: </td>";
		print "<td align=\"left\" colspan=\"2\"><input type=\"text\" size=\"50\" name=\"edefpass\" value=\"$defpass\"></td></tr>\n";

		print "<tr><td align=\"right\">Include Date of Birth in HTML?: </td><td align=\"left\">\n";
		if ($hfdob == 'Y') {
			print "<input type=\"radio\" name=\"ehfdob\" value=\"Y\" checked/> Yes ";
			print "<input type=\"radio\" name=\"ehfdob\" value=\"N\"/> No </td></tr>\n";
		} else {
			print "<input type=\"radio\" name=\"ehfdob\" value=\"Y\"/> Yes ";
			print "<input type=\"radio\" name=\"ehfdob\" value=\"N\" checked/> No </td></tr>\n";
		}				
		print "<tr><td align=\"right\">Include Date of Death in HTML?: </td><td align=\"left\">\n";
		if ($hfdod == 'Y') {
			print "<input type=\"radio\" name=\"ehfdod\" value=\"Y\" checked/> Yes ";
			print "<input type=\"radio\" name=\"ehfdod\" value=\"N\"/> No </td></tr>\n";
		} else {
			print "<input type=\"radio\" name=\"ehfdod\" value=\"Y\"/> Yes ";
			print "<input type=\"radio\" name=\"ehfdod\" value=\"N\" checked/> No </td></tr>\n";
		}				
		print "<tr><td align=\"right\">Include Comment in HTML?: </td><td align=\"left\">\n";
		if ($hfcomm == 'Y') {
			print "<input type=\"radio\" name=\"ehfcomm\" value=\"Y\" checked/> Yes ";
			print "<input type=\"radio\" name=\"ehfcomm\" value=\"N\"/> No </td></tr>\n";
		} else {
			print "<input type=\"radio\" name=\"ehfcomm\" value=\"Y\"/> Yes ";
			print "<input type=\"radio\" name=\"ehfcomm\" value=\"N\" checked/> No </td></tr>\n";
		}				
		print "<tr><td align=\"right\">Include Contributor in HTML?: </td><td align=\"left\">\n";
		if ($hfcont == 'Y') {
			print "<input type=\"radio\" name=\"ehfcont\" value=\"Y\" checked/> Yes ";
			print "<input type=\"radio\" name=\"ehfcont\" value=\"N\"/> No </td></tr>\n";
		} else {
			print "<input type=\"radio\" name=\"ehfcont\" value=\"Y\"/> Yes ";
			print "<input type=\"radio\" name=\"ehfcont\" value=\"N\" checked/> No </td></tr>\n";
		}				

		print "</table>";
		print "<input type=\"Submit\" value=\"Update User\"><input type=\"hidden\"  name=\"euser\" value=\"$user\"";
		print "</form>"; 
	}
}
					
function displayUsers() {
	$rowsPerPage = 24;
	$pageNum = 1;
	if(isset($_GET['page'])) {
		$pageNum = $_GET['page'];
	}
	$offset = ($pageNum - 1) * $rowsPerPage;
	// Connect to database
	include('dbconn.php');
	// Select data by section and display it
	print "<form name=\"useradmin\" id=\"useradmin\" method=\"post\" action=\"index.php?func=mntuser\">\n";
	print "<table width=\"570\" align=\"center\" cellpadding=\"2\" border=\"1\">\n";
	print "<tr><th>Userid</th><th>Access Level</th><th>First Name</th><th>Last Name</th></tr>\n";
	$query = "SELECT CTLNUM, USERID, ACCESSLVL, FNAME, LNAME from dpx_sys_users order by userid LIMIT $offset, $rowsPerPage";
	$result = mysql_query($query);
	if($result) {
		while($row = mysql_fetch_array($result)) {
			$user = $row["CTLNUM"];
			$userid = $row["USERID"];
			$level = $row["ACCESSLVL"];
			$fname = $row["FNAME"];
			$lname = $row["LNAME"];
			print "<tr><td align=\"center\"> $userid </td>";
			print "<td>$level</td>";
			print "<td>$fname</td>";
			print "<td>$lname</td>";
			print "<td><a href=\"index.php?func=mntuser&edituser=$user&page=$pageNum\">edit</a></td>";
			print "</tr>\n";
		}
	}
	print "</table><br />\n";
	// how many rows we have in database
	$query = "SELECT COUNT(*) AS numrows FROM dpx_sys_users";
	$result = mysql_query($query) or die('Error, query failed');
	$row = mysql_fetch_array($result);
	$numrows = $row['numrows'];
	// how many pages we have when using paging?
	$maxPage = ceil($numrows/$rowsPerPage);
	$self = $_SERVER['PHP_SELF'];
	// creating 'previous' and 'next' link
	// plus 'first page' and 'last page' link

	// print 'previous' link only if we're not
	// on page one
	if ($pageNum > 1) {
		$page = $pageNum - 1;
		$prev = " <a href=\"$self?func=mntuser&page=$page\">[Prev Page]</a> ";
		$first = " <a href=\"$self?func=mntuser&page=1\">[First Page]</a> ";
	} else {
		$prev  = ' [Prev Page] ';       // we're on page one, don't enable 'previous' link
		$first = ' [First Page] ';      // nor 'first page' link
	}

	// print 'next' link only if we're not
	// on the last page
	if ($pageNum < $maxPage) {
		$page = $pageNum + 1;
		$next = " <a href=\"$self?func=mntuser&page=$page\">[Next Page]</a> ";
		$last = " <a href=\"$self?func=mntuser&page=$maxPage\">[Last Page]</a> ";
	} else {
		$next = ' [Next Page] ';      // we're on the last page, don't enable 'next' link
		$last = ' [Last Page] ';      // nor 'last page' link
	}

	// print the page navigation link
	print "<table width=\"570\"><tr>";
	print "<td> $first </td><td> $prev </td><td> Page <strong>$pageNum</strong> of <strong>$maxPage</strong> </td>";
	print "<td align=\"right\"> $next </td><td align=\"right\"> $last </td></tr>";
	print "</table>\n";
	print "</form>\n";

	mysql_close($conn);
}

function validInsert() {
	$USERID = $_POST['userid'];
	// Connect to database
	include('dbconn.php');
		
	// See if userid already exists
    $query = "SELECT accesslvl FROM dpx_sys_users WHERE userid = '$USERID'";
    $result = mysql_query($query);
		$count = mysql_num_rows($result);
		if ($count == 0) {
	    mysql_close($conn); 
	    return TRUE;
	  } else {
	    mysql_close($conn); 
	    return FALSE;
	  }	  	
}

function insertUser() {
	$USERID = $_POST['userid'];
	$PASS = $_POST['pass'];
	$LVL = $_POST['lvl'];
	$FNAME = $_POST['fname'];
	$LNAME = $_POST['lname'];
	$EMAIL = $_POST['email'];
	$REGDATE = $_POST['regdate'];
	$EXPDATE = $_POST['expdate'];
	$DEFHOST = $_POST['defhost'];
	$DEFUSER = $_POST['defuser'];
	$DEFPASS = $_POST['defpass'];
	$HFDOB = $_POST['hfdob'];
	$HFDOD = $_POST['hfdod'];
	$HFCOMM = $_POST['hfcomm'];
	$HFCONT = $_POST['hfcont'];
	// Connect to database
	include('dbconn.php');
	// Find next available slot
	$ctlnum = 0;
	$query = "select max(ctlnum) as MAX from dpx_sys_users";
	$result = mysql_query($query);
	if ($result) {
		$row = mysql_fetch_array($result);
		$ctlnum = $row["MAX"];
	}
	$ctlnum++;
	// Insert new data into database
  $query = "INSERT INTO dpx_sys_users ";
  $query .= " (ctlnum, userid, password, accesslvl, regdate, expdate, fname, lname, email, ";
  $query .= " default_ftphost, default_ftpuser, default_ftppass, def_hfdob, def_hfdod, ";
  $query .= " def_hfcomm, def_hfcont) ";
  $query .= " VALUES ";
  $query .= " ($ctlnum, '$USERID', '" . sha1($PASS) . "', '$LVL', '$REGDATE', '$EXPDATE', '$FNAME', ";
  $query .= " '$LNAME', '$EMAIL', '$DEFHOST', '$DEFUSER', '$DEFPASS', '$HFDOB', '$HFDOD', ";
  $query .= " '$HFCOMM', '$HFCONT')";
  $result = mysql_query($query);
  if (! $result) {
		echo "<br /><h4>DATABASE INSERT ERROR 271</h4>";
 	   mysql_close($conn); 
		return;
	}
	$cemtbl = $ctlnum . "_cems";
	$contrtbl = $ctlnum . "_contributors";
	$query = "CREATE TABLE $cemtbl like dpx_model_cem";
	$result = mysql_query($query);
  if (! $result) {
		echo "<br /><h4>DATABASE TABLE CREATE ERROR 272</h4>";
 		mysql_close($conn); 
		return;
	}
	$query = "CREATE TABLE $contrtbl like dpx_model_contrib";
 	$result = mysql_query($query);
 	if (! $result) {
		echo "<br /><h4>DATABASE TABLE CREATE ERROR 273</h4>";
  	mysql_close($conn); 
		return;
	}
  mysql_close($conn); 
  unset($_POST['user']);
}
		
function displayUserform() {
	print "<form action=\"index.php?func=mntuser\" method=\"post\">\n";
	print "<table width=\"80%\" align=\"center\">";
	print "<tr><td align=\"right\">Userid: </td>";
	print "<td align=\"left\" colspan=\"2\"><input type=\"text\" size=\"25\" name=\"userid\"></td></tr>\n";
	print "<tr><td align=\"right\">Password: </td>";
	print "<td align=\"left\" colspan=\"2\"><input type=\"password\" size=\"50\" name=\"pass\"></td></tr>\n";
	print "<tr><td align=\"right\">Access Level: </td>";
	print "<td align=\"left\" colspan=\"2\"><input type=\"radio\" name=\"lvl\" value=\"ADMIN\"/> ADMIN ";
	print "<input type=\"radio\" name=\"lvl\" value=\"USER\" checked/> USER </td></tr>\n";
	print "<tr><td align=\"right\">First Name: </td>";
	print "<td align=\"left\" colspan=\"2\"><input type=\"text\" size=\"50\" name=\"fname\"></td></tr>\n";
	print "<tr><td align=\"right\">Last Name: </td>";
	print "<td align=\"left\" colspan=\"2\"><input type=\"text\" size=\"50\" name=\"lname\"></td></tr>\n";
	print "<tr><td align=\"right\">Email: </td>";
	print "<td align=\"left\" colspan=\"2\"><input type=\"text\" size=\"50\" name=\"email\"></td></tr>\n";
	print "<tr><td align=\"right\">Registration Date: </td>";
	print "<td align=\"left\"><input type=\"text\" size=\"10\" name=\"regdate\"></td><td><h5>YYYY-MM-DD</h5></td></tr>\n";
	print "<tr><td align=\"right\">Expiration Date: </td>";
	print "<td align=\"left\"><input type=\"text\" size=\"10\" name=\"expdate\"></td><td><h5>YYYY-MM-DD</h5></td></tr>\n";
	print "<tr><td align=\"right\">Default FTP Host: </td>";
	print "<td align=\"left\" colspan=\"2\"><input type=\"text\" size=\"50\" name=\"defhost\"></td></tr>\n";
	print "<tr><td align=\"right\">Default FTP Userid: </td>";
	print "<td align=\"left\" colspan=\"2\"><input type=\"text\" size=\"50\" name=\"defuser\"></td></tr>\n";
	print "<tr><td align=\"right\">Default FTP Password: </td>";
	print "<td align=\"left\" colspan=\"2\"><input type=\"text\" size=\"50\" name=\"defpass\"></td></tr>\n";

	print "<tr><td align=\"right\">Include Date of Birth in HTML?: </td><td align=\"left\">\n";
	print "<input type=\"radio\" name=\"hfdob\" value=\"Y\" checked/> Yes ";
	print "<input type=\"radio\" name=\"hfdob\" value=\"N\"/> No </td></tr>\n";

	print "<tr><td align=\"right\">Include Date of Death in HTML?: </td><td align=\"left\">\n";
	print "<input type=\"radio\" name=\"hfdod\" value=\"Y\" checked/> Yes ";
	print "<input type=\"radio\" name=\"hfdod\" value=\"N\"/> No </td></tr>\n";

	print "<tr><td align=\"right\">Include Comment in HTML?: </td><td align=\"left\">\n";
	print "<input type=\"radio\" name=\"hfcomm\" value=\"Y\"/> Yes ";
	print "<input type=\"radio\" name=\"hfcomm\" value=\"N\" checked/> No </td></tr>\n";

	print "<tr><td align=\"right\">Include Contributor in HTML?: </td><td align=\"left\">\n";
	print "<input type=\"radio\" name=\"hfcont\" value=\"Y\" checked/> Yes ";
	print "<input type=\"radio\" name=\"hfcont\" value=\"N\"/> No </td></tr>\n";

	print "</table>";
	print "<input type=\"Submit\" value=\"Add User\">";
	print "</form>";
	print "<br /><br />\n"; 
}

?>