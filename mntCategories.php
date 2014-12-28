<?php

$thing = "Category";
$things = "Categories";

function mntCategories() {
	global $thing, $things;
	printTop();
	print "<div id='profile'><h5><a href='index.php?func=newpass'>Change Your Password</a></h5></div>\n";
	print "<div id='nav'><h5><a href='index.php'>Dashboard</a> | <a href='logout.php'>Logout</a></h5></div>\n";
	print "<br /><br /><br />";
	print "<h3>Maintain $things</h3><br />";
	print "<div id=\"incList\">";
	if (isset($_POST['thing'])) {
	if (validInsert()) {
		insertThing();
		echo "<br /><div id='msg'><h4>New $thing Added</h4></div><br /><br /> \n";
	} else {
		echo "<br /><div id='msg'><h4>Can not add - $thing already exists or is blank</h4><br /><br /> \n";
	}
	displayThingForm();
	displayThings();
	}
	elseif (isset($_GET['editThing'])) {
		editThingForm();
	}
	elseif (isset($_POST['ething'])) { 
		editUpdateThing();
		echo "<br /><div id='msg'><h4>$thing Updated</h4></div><br /><br /> \n";
		displayThingForm();
		displayThings();
	}
	else {
		displayThingForm();
		displayThings(); 
	}
	print "</div>\n";
}

function editUpdateThing() {
	$ID = $_POST['ething'];
// THE NAMES PAST HERE MUST BE CHANGED TO FIT THE PARTICULAR ROUTINE.  DON'T FORGET FUNC NAMES, ETC.
	$NAME = escQuotes(trim($_POST['ename']));
	$ACTIVE = $_POST['eactive'];
	$CUST = $_POST['ecust'];
	$DEFAULT = $_POST['edefault'];
	// Connect to database
	include('dbconn.php');
// CHANGE TO FIT PARTICULAR TABLE. Post edited item to database
	if ($DEFAULT == 1) {
		$sql2 = "UPDATE defaults SET defCategory = $ID where id = 1";
		$result2 = mysql_query($sql2);
		$ACTIVE = 1;
		$CUST = 0;
	}
	$sql = "UPDATE categories SET name = '$NAME', cust = $CUST, active = $ACTIVE ";
	$sql .= "WHERE id = $ID";
	$result = mysql_query($sql);
	// Close database connection and unset eitem
	mysql_close($conn);
	unset($_POST['ething']);
}

function editThingForm() {
	global $thing, $things;
	$id = $_GET['editThing'];
	$pageNum = $_GET['page'];
	// Connect to database
	include('dbconn.php');
// THE NAMES PAST HERE MUST BE CHANGED TO FIT THE PARTICULAR ROUTINE.  DON'T FORGET FUNC NAMES, ETC.
	$query = "SELECT defCategory from defaults where id = 1";
	$result = mysql_query($query);
	if ($result) {
		$row = mysql_fetch_array($result);
		$defaultValue = $row["defCategory"];
	}
	$query = "SELECT ID, CUST, NAME, ACTIVE ";
	$query .= " from categories where id = $id";
	$result = mysql_query($query);
	if ($result) {
		$row = mysql_fetch_array($result);	
		$id = $row["ID"];
		$name = $row["NAME"];
		$cust = $row["CUST"];
		$active = $row["ACTIVE"];
		print "<form action=\"index.php?func=mntCategories&page=$pageNum\" method=\"post\">\n";
		print "<table width=\"80%\" align=\"center\">";
		print "<tr><td align=\"right\">$thing: </td>";
		print "<td align=\"left\" colspan=\"2\"><input type=\"text\" size=\"50\" name=\"ename\" value=\"$name\"</td></tr>\n";
		print "<tr><td align=\"right\">Customer: </td>";
		print "<td align=\"left\"><select name=\"ecust\">";
		if ($cust == 0) {
			print "<option value=\"0\">ALL</option>";
		} else {
			$query2 = "SELECT name FROM customers where id = $cust";
			$result2 = mysql_query($query2);
			if ($result2) {
				$row2 = mysql_fetch_array($result2);
				$cname = $row2["name"];
				print "<option value=\"$cust\">$cname</option>";
			}
		}
		$query3 = "SELECT id, name FROM customers where id <> $cust and active = 1 order by name";
		$result3 = mysql_query($query3);
		if ($result3) {
			while ($row3 = mysql_fetch_array($result3)) {
				$cid = $row3["id"];
				$cname = $row3["name"];
				print "<option value=\"$cid\">$cname</option>";
			}
		}
		if ($cust <> 0) {
			print "<option value\"0\">ALL</option>";
		}
		print "</select></td></tr>";
		print "<tr><td align=\"right\">Active?: </td><td align=\"left\">";
		if ($active == 1) {
			print "<input type=\"radio\" name=\"eactive\" value=\"1\" checked /> Yes ";
			print "<input type=\"radio\" name=\"eactive\" value=\"0\"/> No </td></tr>\n";
		} else {
			print "<input type=\"radio\" name=\"eactive\" value=\"1\" /> Yes ";
			print "<input type=\"radio\" name=\"eactive\" value=\"0\" checked /> No </td></tr>\n";
		}
		print "<tr><td align=\"right\">Default?: </td><td align=\"left\">";
		if ($defaultValue == $id) {
			print "<input type=\"radio\" name=\"edefault\" value=\"1\" checked /> Yes ";
			print "<input type=\"radio\" name=\"edefault\" value=\"0\"/> No </td></tr>\n";
		} else {
			print "<input type=\"radio\" name=\"edefault\" value=\"1\" /> Yes ";
			print "<input type=\"radio\" name=\"edefault\" value=\"0\" checked /> No </td></tr>\n";
		}
		print "</table>";
		print "<input type=\"Submit\" value=\"Update $thing\"><input type=\"hidden\"  name=\"ething\" value=\"$id\">";
		print "</form>"; 
	}
}
					
function displayThings() {
	global $thing, $things;
	$rowsPerPage = 24;
	$pageNum = 1;
	if (isset($_GET['page'])) {
		$pageNum = $_GET['page'];
	}
	$offset = ($pageNum - 1) * $rowsPerPage;
	// Connect to database
	include('dbconn.php');
	// Select data by section and display it
	$query = "SELECT defCategory from defaults where id = 1";
	$result = mysql_query($query);
	if ($result) {
		$row = mysql_fetch_array($result);
		$defaultValue = $row["defCategory"];
	}
	print "<table width=\"100%\" align=\"center\" cellpadding=\"2\" border=\"1\">\n";
	print "<tr><th>$thing</th><th>Customer</th><th>Default?</th><th>Active?</th></tr>\n";
	$query = "SELECT ID, CUST, NAME, ACTIVE ";
	$query .= "FROM categories ";
	$query .= "ORDER BY CUST, NAME LIMIT $offset, $rowsPerPage";
	$result = mysql_query($query);
	if ($result) {
		while ($row = mysql_fetch_array($result)) {
			$id = $row["ID"];
			$name = $row["NAME"];
			$cust = $row["CUST"];
			$active = $row["ACTIVE"];
			print "<tr><td align=\"center\"> $name </td>";
			if ($cust == 0) {
				print "<td>ALL</td>";
			} else {
				$query2 = "SELECT name FROM customers where id = $cust";
				$result2 = mysql_query($query2);
				if ($result2) {
					$row2 = mysql_fetch_array($result2);
					$cname = $row2["name"];
					print "<td>$cname</td>";
				}
			}
			if ($defaultValue == $id) {
				print "<td>Yes</td>";
			} else {
				print "<td>&nbsp</td>";
			}
			if ($active == 1) {
				print "<td>Yes</td>";
			} else {
				print "<td>No</td>";
			}
			print "<td><a href=\"index.php?func=mntCategories&editThing=$id&page=$pageNum\">edit</a></td>";
			print "</tr>\n";
		}
	}
	print "</table><br />\n";
	// how many rows we have in database
	$query = "SELECT COUNT(*) AS numrows FROM statuses";
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
		$prev = " <a href=\"$self?func=mntCategories&page=$page\">[Prev Page]</a> ";
		$first = " <a href=\"$self?func=mntCategories&page=1\">[First Page]</a> ";
	} else {
		$prev  = ' [Prev Page] ';       // we're on page one, don't enable 'previous' link
		$first = ' [First Page] ';      // nor 'first page' link
	}

	// print 'next' link only if we're not
	// on the last page
	if ($pageNum < $maxPage) {
		$page = $pageNum + 1;
		$next = " <a href=\"$self?func=mntCategories&page=$page\">[Next Page]</a> ";
		$last = " <a href=\"$self?func=mntCategories&page=$maxPage\">[Last Page]</a> ";
	} else {
		$next = ' [Next Page] ';      // we're on the last page, don't enable 'next' link
		$last = ' [Last Page] ';      // nor 'last page' link
	}

	// print the page navigation link
	print "<table width=\"100%\"><tr>";
	print "<td align=\"left\"> $first </td><td align=\"left\"> $prev </td><td> Page <strong>$pageNum</strong> of <strong>$maxPage</strong> </td>";
	print "<td align=\"right\"> $next </td><td align=\"right\"> $last </td></tr>";
	print "</table>\n";
	print "</form>\n";

	mysql_close($conn);
}

function validInsert() {
	$NAME = $_POST['thing'];
	if ($NAME == "") {
		return FALSE;
	}
	// Connect to database
	include('dbconn.php');
	// See if thing already exists
    $query = "SELECT id FROM categories WHERE name = '$NAME'";
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

function insertThing() {
	$NAME = escQuotes(trim($_POST['thing']));
	$CUST = $_POST['cust'];
	$ACTIVE = $_POST['active'];
	// Connect to database
	include('dbconn.php');
	// Insert new data into database
	$query = "INSERT INTO categories ";
  	$query .= " (id, cust, name, active) ";
  	$query .= " VALUES ";
  	$query .= " ('', $CUST, '$NAME', $ACTIVE)";
  	$result = mysql_query($query);
  	if (! $result) {
		echo "<br /><h4>DATABASE INSERT ERROR 271</h4>";
 	   mysql_close($conn); 
		return;
	}
  mysql_close($conn); 
  unset($_POST['thing']);
}
		
function displayThingForm() {
	global $thing, $things;
	print "<form action=\"index.php?func=mntCategories\" method=\"post\">\n";
	print "<table width=\"80%\" align=\"center\">";
	print "<tr><td align=\"right\">$thing: </td>";
	print "<td align=\"left\" colspan=\"2\"><input type=\"text\" size=\"25\" name=\"thing\"></td></tr>\n";
	print "<tr><td align=\"right\">Customer: </td>";
	print "<td align=\"left\"><select name=\"cust\">";
	print "<option value=\"0\">ALL</option>";
	// Connect to database
	include('dbconn.php');
	$query2 = "SELECT id, name FROM customers where active = 1 order by name";
	$result2 = mysql_query($query2);
	if ($result2) {
		while ($row2 = mysql_fetch_array($result2)) {
			$cust = $row2["id"];
			$cname = $row2["name"];
			print "<option value=\"$cust\">$cname</option>";
		}
	}
	mysql_close($conn);
	print "<tr><td align=\"right\">Active?: </td><td align=\"left\">\n";
	print "<input type=\"radio\" name=\"active\" value=\"1\" checked /> Yes ";
	print "<input type=\"radio\" name=\"active\" value=\"0\"/> No </td></tr>\n";
	print "</table>";
	print "<input type=\"Submit\" value=\"Add $thing\">";
	print "</form>";
	print "<br /><br />\n"; 
}

include ('escQuotes.php');

?>
