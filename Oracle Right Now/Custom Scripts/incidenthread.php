<?php
	ini_set('display_errors', 1);
	require_once( get_cfg_var( 'doc_root' ).'/include/ConnectPHP/Connect_init.phph' );
	require_once('include/init.phph');
	initConnectAPI("avinashow","avinash");
	use RightNow\Connect\v1_2 as RNCPHP;
	
	$incident = RNCPHP\Incident::fetch(3240);
	print_r($incident->Threads);
	echo "<br>";
	$incident->Threads = new RNCPHP\ThreadArray();
	$incident->save(RNCPHP\RNObject::SuppressAll);
	print_r($incident->Threads);