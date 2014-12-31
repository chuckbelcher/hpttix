<?php
//
//  sysDescription.php is a PHP component of HPTTix
//  Copyright 2014 John Francis, HPT Solutions, Inc.  
//  Licensed under the Open Software License (OSL 3.0).
//  
	print "<h3>To use the HPT-Tix Incident Tracking System, you will need to know the following:</h3><br />";
	print "<h4><ul>";
	print "<li>OPERATORS are personnel that use this system to track their work for CALLERS.</li>";
	print "<li>There are two classes of OPERATORS: USERS and ADMINs.</li>";
	print "<li>CALLERS are representatives of CUSTOMERS that are authorized to request support from operators.</li>";
	print "<li>CUSTOMERS are entities that have contracted to receive support services.</li>";
	print "<li>A CUSTOMER is associated with a PLAN.</li>";
	print "<li>The PLAN contains the billing parameters for the contract.</li>";
	print "<li>Work is tracked and billed using INCIDENT TICKETS, one ticket per support incident.</li>";
	print "<li>Each INCIDENT is comprised of one or more ACTIVITIES.</li>";
	print "<li>System generated ACTIVITIES, such as changing a status, are marked as NOT BILLABLE. ACTIVITIES entered ";
	print "by OPERATORS are BILLABLE by default.</li>";
	print "<li>ADMINS are able to mark ACTIVITIES, or even entire INCIDENTS as NOT BILLABLE.</li>";
	print "<li>ADMINS are able to run BILLING REPORTS to use in preparing invoices for CUSTOMERS.</li>";
	print "</ul></h4>";
	print "<br /><br />";

?>