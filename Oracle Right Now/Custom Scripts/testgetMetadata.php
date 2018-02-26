<?php

    ini_set('display_errors', 1);
	require_once( get_cfg_var( 'doc_root' ).'/include/ConnectPHP/Connect_init.phph' );
	require_once('include/init.phph');
	initConnectAPI("avinashow","avinash");
    
    use RightNow\Connect\v1_3 as RNCPHP;
        
    $metadata = RNCPHP\CO\Maintenance::getMetadata();
    
    print_r($metadata);  


?>
    