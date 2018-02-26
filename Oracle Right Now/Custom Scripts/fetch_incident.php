<?php
	ini_set('display_errors', 1);
	require_once( get_cfg_var( 'doc_root' ).'/include/ConnectPHP/Connect_init.phph' );
	require_once('include/init.phph');
	initConnectAPI("avinashow","avinash");
	use RightNow\Connect\v1_2 as RNCPHP;
	
	$incident = RNCPHP\Incident::fetch(15728);
	print_r($incident);
	echo "<br>";
	print_r($incident->Severity->LookupName);
	echo "<br>";
	print_r($incident->AssignedTo);
	echo "<br>";
	print_r($incident->Disposition->Name);
	echo "<br>";
	print_r($incident->Disposition->ID);
	echo "<br>";
	print_r($incident->PrimaryContact->Emails[0]->Address);
	echo "<br>";
	print_r($incident->PrimaryContact->Name);
	echo "<br>";
	print_r($incident->StatusWithType->Status->LookupName);
	
?>