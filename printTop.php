<?php

function printTOP() {
	global $puser;
	global $ptix;
	global $bannerTime;
	
	print "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
	print "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
	print "<head>\n";
	print "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n";
	print "<title>HPTTix - Incident Tracking System</title>\n";
	print "<meta name=\"author\" content=\"Pateo Group, Inc.\" />\n";
	print "<link rel=\"stylesheet\" href=\"hpttix.css\" type=\"text/css\" />\n";
	print "</head>\n";
	print "<body>\n";
	print "<div id=\"wrapper\">\n";
	print "<div id=\"banner\">\n";
	print "  <img class=\"logo\" src=\"logo.gif\" height=\"100\" alt=\"Pateo Group Logo\" align=\"left\">\n";
	print "  <h2>HPT-Tix</h2>\n";
	print "  <h3>Incident Tracking Utility<br /></h3><br />\n";
	print " <hr /><table align=\"left\" width=\"40%\"cellpadding=\"0\" ><tr><td align=\"left\">Userid:</td>";
	print "<td align=\"left\">$puser</td><td align=\"right\">Open Tickets: &nbsp </td><td align=\"left\"> $ptix</td></tr>";
	print "</table><table align=\"right\" width=\"40%\"cellpadding=\"0\" ><tr><td align=\"right\">$bannerTime</td></tr></table>";
	print "<br /><hr />\n";
	print "</div>\n";

	if(isset($_SESSION['msg'])) {
		print "<br /><div id='msg'><h4>" . $_SESSION['msg'] . "</h4></div><br /> \n";
		unset($_SESSION['msg']);
	}
}

?>