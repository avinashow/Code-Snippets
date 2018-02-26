<?php

	ini_set('display_errors', 1);
	require_once( get_cfg_var( 'doc_root' ).'/include/ConnectPHP/Connect_init.phph' );
	require_once('include/init.phph');
	initConnectAPI("avinashow","avinash");
	use RightNow\Connect\v1_2 as RNCPHP;
	
	$result = RNCPHP\Answer::fetch(1039);
	print_r($result);
	echo "<br>";
	print_r($result->AccessLevels);
	echo "<br>";
	print_r($result->AccessLevels[0]->LookupName);
	
?>