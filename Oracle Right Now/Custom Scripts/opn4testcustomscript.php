<?php
     ini_set('display_errors', 1);
     require_once( get_cfg_var( 'doc_root' ).'/include/ConnectPHP/Connect_init.phph' );
     require_once('include/init.phph');
     initConnectAPI("avinashow","avinash");
     use RightNow\Connect\v1_2 as RNCPHP;

    echo "weclome";
    $obj = RNCPHP\ROQL::queryObject("select sc from ServiceCategory sc where sc.parent.ID = 1537")->next();
    while ($object = $obj->next()) {
        print_r("<pre>");
        //print_r($object->CategoryHierarchy);
        $catobj = $object->CategoryHierarchy;
        echo count($catobj);
        for ($i = 0; $i < count($object->CategoryHierarchy); $i++) {
            print_r($catobj[$i]);
        }
    }