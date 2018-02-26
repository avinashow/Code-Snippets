<?php

	ini_set('display_errors', 1);
	require_once( get_cfg_var( 'doc_root' ).'/include/ConnectPHP/Connect_init.phph' );
	require_once('include/init.phph');
	initConnectAPI("avinashow","avinash");
	use RightNow\Connect\v1_2 as RNCPHP;
	
	$incident = RNCPHP\ROQL::query("select i.ID, i.Queue from incident i where incident.ID = 15664")->next();
	print_r($incident->next());
	
?>