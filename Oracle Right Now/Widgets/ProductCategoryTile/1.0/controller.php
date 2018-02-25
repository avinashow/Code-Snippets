<?php
namespace Custom\Widgets\ui;

use RightNow\Connect\v1_3 as Connect;

class ProductCategoryTile extends \RightNow\Libraries\Widget\Base {
    function __construct($attrs) {
        parent::__construct($attrs);

        $this->setAjaxHandlers(array(
            'default_ajax_endpoint' => array(
                'method'      => 'handle_default_ajax_endpoint',
                'clickstream' => 'custom_action',
            ),
        ));
    }

    function getData() {
		
		$level = \RightNow\Utils\Url::getParameter('level');
		
		if (isset($level)) {
			$level++;
		} else {
			$level = 1;
		}
	
        $prodcat = $this->data['attrs']['prodcat']; 
        
		$prodid = \RightNow\Utils\Url::getParameter('prodid');
		
		$catid = \RightNow\Utils\Url::getParameter('catid');
		
		$query = "select sc from service{$prodcat} sc where";
		
		$parentquery = "select sc from service{$prodcat} sc where";
		
		
		if (isset($catid) && $prodcat == "category") {
			$query = $query." sc.parent.id = $catid";
			$parentquery = $parentquery. " sc.ID = $catid";
		} else {
			if ($prodcat == "category") {
				$query = $query." sc.parent.id is null";			
			}
		}
		if (isset($prodid) && $prodcat == "product") {
			$query = $query." sc.parent.id = $prodid";
		} else {
			if ($prodcat == "product") {
				$query = $query." sc.parent.id is null";
			}
		}	
		$query = $query." and sc.EndUserVisibleInterfaces.ID = 2";
		
 		$response= Connect\ROQL::queryObject($query)->next(); 
 		$this->data["js"]["formdisplay"] = "none";
 		 		
 		
 		if(isset($catid)  && $response->count() == 0) {
 			$parents = Connect\ROQL::queryObject("Select SC from ServiceCategory SC where SC.ID = $catid")->next();
 			print_r("<u>Parents Hierarchy</u><br>");
 			while ($parent = $parents->next()) {
 				for ($i = 0; $i < count($parent->CategoryHierarchy); $i++) {
 					print_r($parent->CategoryHierarchy[$i]->LookupName);
 					print_r("<br>");
 				}
 				print_r($parent->LookupName);
	 		}
	 		$this->data["js"]["formdisplay"] = "block";
 		}
 		
 		$this->data["js"]["categories"] = array("row1"=>array(),"row2"=>array());
 		$this->data["js"]["products"] = array("row1"=>array());
 		$count = 1;
 		
 		//$divider = $response->count() / 2 + 1;
 			
 		
 		
 		while($res =$response->next()) {
 			$obj = array();
 			$obj["ID"] = $res->ID;
 			$obj["Name"] = $res->Name;
 			$obj["level"] = $level;
 			if ($prodcat == "product") {
 				array_push($this->data["js"]["products"]["row1"], $obj);
 			} else {
 				//if ($count < $divider) {
 					array_push($this->data["js"]["categories"]["row1"], $obj);
 				//} else {
 				//	array_push($this->data["js"]["categories"]["row2"], $obj); 					
 				//}
 			}
 			$count++;
 		}	
 		
 		$this->data["js"]["margin"] = 5; 		
 		if ($level > 2 || $response->count() %2 == 0) {
 			$this->data["js"]["margin"] = 0;
 		}
 		
 		
 		$this->data["js"]["display"] = ($level > 2)?"block":"none";
 				
    	return parent::getData();

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