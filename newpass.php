<?php
function newpass() {
	printTop();
	print "<div id='profile'><h5>Change Your Password</h5></div>\n";
	print "<div id='nav'><h5><a href='index.php'>Dashboard</a> | <a href='logout.php'>Logout</a></h5></div>\n";
	print "<br /><br /><br />";
	print "<form action=\"newpasswd.php\" name=\"passform\" method=\"post\"> \n";
	print "<h3>Change Password</h3><br>";
	print "	<table align=\"center\"> \n";
	print "    <tr><td>Old Password:</td><td><input type=\"password\" name=\"oldpass\" /></td></tr> \n";
	print "    <tr><td>New Password:</td><td><input type=\"password\" name=\"newpass1\" /></td></tr> \n";
	print "    <tr><td>Retype New Password:</td><td><input type=\"password\" name=\"newpass2\" /></td></tr> \n";
	print "    <tr><td colspan=\"2\"><input type=\"submit\" name=\"submit\" value=\"Submit\" /></td></tr> \n";
	print "  </table>";
	print "</form> \n";
	print "<script type=\"text/javascript\"> \n";
	print " document.passform.oldpass.focus(); \n";
	print "</script> \n";
}
?>
