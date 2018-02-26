<?php
	ini_set('display_errors', 1);
	require_once( get_cfg_var( 'doc_root' ).'/include/ConnectPHP/Connect_init.phph' );
	require_once('include/init.phph');
	initConnectAPI("avinash","Federal@123");
	use RightNow\Connect\v1_2 as RNCPHP;
	use RightNow\Api,
		RightNow\Utils\Text,
		RightNow\Utils\Config,
		RightNow\ActionCapture,
		RightNow\Utils\Framework,
		RightNow\Internal\Sql\Report as Sql;

	$status_filter= new RNCPHP\AnalyticsReportSearchFilter;
	$status_filter->Name = 'dealer';
	$status_filter->Values = array(8183);
	
	$status_filter1= new RNCPHP\AnalyticsReportSearchFilter;
	$status_filter1->Name = 'snum';
	$status_filter1->Values = array("p");
	$filters = new RNCPHP\AnalyticsReportSearchFilterArray;
	array_push($filters,$status_filter);
	array_push($filters,$status_filter1);
	$ar= RNCPHP\AnalyticsReport::fetch(100344);
	$report = Sql::_report_get(100344);
	echo "<pre>";
	print_r($report);
	echo "<br><br>";
	$arr= $ar->run( 0,$filters );
	$nrows= $arr->count();
	if ($nrows) {
		$row = $arr->next();
		// Emit the column headings
		echo( join( ',', array_keys( $row ) ) ."\n" );
		// Emit the rows in this report run
		for ( $ii = 0; $ii++ < $nrows; $row = $arr->next() ) {
			echo( join( ',', $row ) . "\n" );
		}
	} 
	
?>