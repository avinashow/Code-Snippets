<?php

	ini_set('display_errors', 1);
	require_once( get_cfg_var( 'doc_root' ).'/include/ConnectPHP/Connect_init.phph' );
	require_once('include/init.phph');
	initConnectAPI("avinashow","avinash");
	use RightNow\Connect\v1_2 as RNCPHP;
	
	$arr = array();
	$result_set = RNCPHP\ROQL::query("select emails.emaillist.address, name.first, name.last from Contact where emails.emaillist.address is not null")->next();
	while ($res = $result_set->next()) {
		$arr[$res["Address"]] = $res["First"]."_".$res["Last"];
	}
	return $arr;
?>