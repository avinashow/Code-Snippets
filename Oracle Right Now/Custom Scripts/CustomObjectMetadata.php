<?php	
	ini_set('display_errors', 1);
	require_once( get_cfg_var( 'doc_root' ).'/include/ConnectPHP/Connect_init.phph' );
	require_once('include/init.phph');
	initConnectAPI("avinash","avinash@123");
	use RightNow\Connect\v1_2 as RNCPHP;
	
	/*$objmetdata = RNCPHP\Organization::getmetadata();
	$arrfield = array();
	foreach($objmetdata as $key => $value) {
		if (is_object($value)) {
			array_push($arrfield,$key);
		}
	}
	*/
	//$objdata = RNCPHP\ROQL::queryObject("Select CO.addtnl_info from CO.addtnl_info limit 1")->next();
	
	echo "<pre>";
	
	print_r(RNCPHP\Country::fetch("US"));
	echo "</pre>";	
?>
	
	