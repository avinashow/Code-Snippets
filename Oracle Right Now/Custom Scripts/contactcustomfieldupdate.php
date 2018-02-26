<?php

	ini_set('display_errors', 1);
	require_once( get_cfg_var( 'doc_root' ).'/include/ConnectPHP/Connect_init.phph' );
	require_once('include/init.phph');
	initConnectAPI("avinashow","avinash");
	use RightNow\Connect\v1_2 as RNCPHP;
    
    $org = new RNCPHP\Incident;
    $md = $org::getMetadata();

    $marr = array_keys(get_object_vars($md));

    print_r("<pre>");

    $cf_type_name = $md->CustomFields->type_name;

    $md2 = $cf_type_name::getMetadata();

    $fieldmd = $md2->c->type_name;
    
    $fmd = $fieldmd::getMetadata();
    
    $fieldmarr = array();

    $farr = array_keys(get_object_vars($fmd));
    
    
    for($i = 10; $i < count($marr); $i++) {
        $fieldmarr[$marr[$i]] = $md->$marr[$i]->type_name;
    }

    
    for($i = 10; $i < count($farr); $i++) {
        $fieldmarr[$farr[$i]] = $fmd->$farr[$i]->type_name;
    }
    

    
    $org->CustomFields->c = new $md2->c->type_name;
 
    $org->CustomFields->c->related_to = new RNCPHP\NamedIDLabel();
    $org->CustomFields->c->related_to->LookupName = "Samsung";
    /*private function setCFNameArray(){
                    print_r("welcome");
                    print_r("<br>");
                    //read custom fld pakage's meta data
                    $org = new RNCPHP\Organization;
                    $omd = $org::getMetadata();
                    $md = $omd->CustomFields->type_name;
                    $customFldMD = $md::getmetadata();
                    $OSCcfPkgArray = $cfPakageArray = array_keys(get_object_vars($customFldMD));
                    $OSCcfArray = array();
                    $CustomFldMetaData = array();
                    //read custom fld names under each package
                    for($pi = 10; $pi < sizeof($cfPakageArray); $pi++){
                                    $pkgMD = $customFldMD->$cfPakageArray[$pi]->type_name;
                                    $CustomFldMetaData[$cfPakageArray[$pi]] = $pkgMD::getmetadata();
                                    $OSCcfArray[$cfPakageArray[$pi]]['fields'] = array_keys(get_object_vars($pkgMD::getmetadata()));
                    }
                    print_r($OSCcfArray);
        
    }*/
    print_r($fieldmarr);
    print_r("<pre>");
    
?>