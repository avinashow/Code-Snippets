<?php

    ini_set('display_errors', 1);
	require_once( get_cfg_var("doc_root")."/ConnectPHP/Connect_init.php");
	use RightNow\Connect\v1_2 as RNCPHP;
	initConnectAPI("speridian","Speridian2017");
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {	
        $obj = new RNCPHP\CO\health_equity_CO;
        $fieldarr = getMetadata($obj);
        for ($i = 0; $i < count($fieldarr); $i++) {
            print_r($_POST[$fieldarr[$i]]);
            print_r("<br/>");
            if (isset($_POST[$fieldarr[$i]]) && strlen($_POST[$fieldarr[$i]]) > 0) {
                if (strpos($fieldarr[$i],"RAW_Effective") !== false || strpos($fieldarr[$i],"RAW_EEeligibility") !== false) {
                    $timestamp = explode("-",$_POST[$fieldarr[$i]]);
                    $obj->$fieldarr[$i] = strtotime(date($_POST[$fieldarr[$i]]));
                } else {
                    $obj->$fieldarr[$i] = $_POST[$fieldarr[$i]];
                }
            }
        }
        $obj->save();
    }
    
    function getMetadata($obj) {
        $fields = array();
        $objmd = $obj::getMetaData();
        foreach($objmd as $key => $value) {
            if (is_object($value)) {
				if (strpos($key,"RAW_") !== false || strpos($key,"con_") !== false || strpos($key,"org_") !== false || $key == "Status") {
                    array_push($fields, $key);
                }
			}
        }
        return $fields;
    }
    
    
?>