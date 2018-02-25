<?php
namespace Custom\Widgets\avinash;
use RightNow\Connect\v1_3 as RNCPHP;
use RightNow\Utils\Connect,
    RightNow\Utils\Config;

require_once( get_cfg_var("doc_root")."/ConnectPHP/Connect_init.php" );

class CustomGenericFormInput extends \RightNow\Libraries\Widget\Base {
    function __construct($attrs) {
        parent::__construct($attrs);
        $this->setAjaxHandlers(array(
            'default_ajax_endpoint' => array(
                'method'      => 'handle_default_ajax_endpoint',
                'clickstream' => 'custom_action',
            ),
        ));
    }
	
	 //public function __call($method, $args) {
     //   return call_user_func_array(array(new CustomGenericFormInput(), $method), $args);
    //}	
	
    function getData() {    

		$this->data["js"] = $this->data["attrs"];
    	$fields =  explode(".",$this->data["attrs"]["name"]);
    	$object = "";
    	$func = "get{$fields[1]}";
    	$visibility = RNCPHP\ROQL::queryObject("select kp from CO.KPITable kp where kp.TTR = '$fields[2]' and kp.Metric3 like '$fields[0]%'")->next();
    	$vis = $visibility->next();
		if ($vis->AHT == "FALSE") {
			$this->data["js"]["visibility"] = "none";
		} else {
			$this->data["js"]["visibility"] = "block";
			$object = $this->$func();
			$mdata = $object::getMetadata();
	        if ($mdata->$fields[2]->is_menu) {
	        	$this->data["js"]["is_menu"] = true;
	        	$this->data["js"]["menuitems"] = $this->getMenuItems($fields[0],$fields[1],$fields[2]);
	        } else {
	        	$this->data["js"]["is_menu"] = false;
	        }
		}
    	
    }
    
    
	function getAssets() {
		return new RNCPHP\CO\Assets;
	}    
    
	
	function getMaintenance() {
		return new RNCPHP\CO\Maintenance;
	}
	
	function getfeedbackAnswer() {
		return new RNCPHP\CO\feedbackAnswer;
	}
	
	
	function getMenuItems($package,$object,$field) {
		$package = $package."\\";
		$items = Connect::getNamedValues($package.$object,$field);
		return $items;
	}
	
    /**
     * Handles the default_ajax_endpoint AJAX request
     * @param array $params Get / Post parameters
     */
    function handle_default_ajax_endpoint($params) {
        // Perform AJAX-handling here...
        // echo response
    }
}