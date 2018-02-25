<?php
namespace Custom\Widgets\warranty\report\otherdealers;

use RightNow\Utils\Connect,
    RightNow\Utils\Text,
    RightNow\Utils\Config,
    RightNow\Api,
	RightNow\Connect\v1_2 as RNCPHP,
    RightNow\ActionCapture,
    RightNow\Utils\Framework,
    RightNow\Internal\Sql\Report as Sql;

use libcurl;
class GenericReport extends \RightNow\Libraries\Widget\Base {
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
    	$format = array();
		$filters = array('recordKeywordSearch' => true);
		
		$reportToken = \RightNow\Utils\Framework::createToken($this->data["attrs"]["report_id"]);
        $resultdata = $this->CI->model('Report')->getDataHTML($this->data["attrs"]["report_id"], $reportToken, $filters, $format)->result;
	
        $colmun_header_map = array();
        $column_headers = array();
		foreach($resultdata as $key=>$value) {
			if ($key == "headers") {
				for($i = 0; $i < count($resultdata[$key]); $i++) {
					$colmun_header_map[$resultdata[$key][$i]["heading"]] = $resultdata[$key][$i]["col_definition"];
					array_push($column_headers,$resultdata[$key][$i]["heading"]);
				}
			}
		}
		$this->data["js"]["col_header_mapping"] = $colmun_header_map;
		//print_r($resultdata);
		
		$generic_filter= new RNCPHP\AnalyticsReportSearchFilter;
    	$filters = new RNCPHP\AnalyticsReportSearchFilterArray;
    	$filterNames = $this->data['attrs']['filter_name'];
    	$filterArray = explode(",",$filterNames);
    	
    	$filterData = $this->data['attrs']['filter_data'];
		
    	
    	$filterDataArray = explode(",",$filterData);
		
       	$i = 0;
    	/*foreach($filterArray as $filterKey)
    	{
    	
		$generic_filter->Name = $filterKey;
		$generic_filter->Values = array($filterDataArray[0]);
		$filters[] = $generic_filter;
		$i++;
		}*/
		
		for($j = 0; $j < count($filterArray); $j++) {
			$status_filter= new RNCPHP\AnalyticsReportSearchFilter;
			$status_filter->Name = $filterArray[$j];
			$status_filter->Values = array((string)$filterDataArray[$j]);
			$filters[] = $status_filter;
		}
		
		
        /*$format = array(
            'truncate_size' => $this->data['attrs']['truncate_size'],
            'max_wordbreak_trunc' => $this->data['attrs']['max_wordbreak_trunc'],
            'emphasisHighlight' => $this->data['attrs']['highlight'],
            'recordKeywordSearch' => true,
            'dateFormat' => $this->data['attrs']['date_format'],
            'urlParms' => \RightNow\Utils\Url::getParametersFromList($this->data['attrs']['add_params_to_url']),
            'hiddenColumns' => true,
            'sanitizeData' => $this->data['attrs']['sanitize_data']
        );

		
		$reportToken = \RightNow\Utils\Framework::createToken($this->data['attrs']['report_id']);
        $results = $this->CI->model('Report')->getDataHTML($this->data['attrs']['report_id'], $reportToken, $filters, $format)->result;*/
		
		$ar= RNCPHP\AnalyticsReport::fetch($this->data['attrs']['report_id']);
		
		
		$arr= $ar->run( 0, $filters );
		
		$transferdata = array();
		// processing results
		$nrows= $arr->count();
		if ( $nrows) {
		    $row = $arr->next();
		  
		    // Emit the column headings
		    //echo( join( ',', array_keys( $row ) ) ."\n" );
		    // Emit the rows in this report run
		    $map_arr = array();
		    $map = array();
		    $i = 1;
		    $count = 0;
		    for ( $ii = 0; $ii++ < $nrows; $row = $arr->next() ) {
		    	 if ($this->data["attrs"]["per_page"] == $count) {
		    	 	break;
		    	 }
		    	 $this->data['tabledata']['data'][$ii] = $row;
				 $count++;
		    }
			  //echo "<pre>";
		     //print_r( $this->data['tabledata']['data']);
			
			
		}
/*if($this->data["attrs"]["report_id"]=='100532')
{

 			$this->data['tabledata']['transferredData'] = $transferdata;
			$this->data['tabledata']['headers'] = array();
			$this->data["js"]["totalrecords"] = $nrows;
			$this->data["js"]["pagerecords"] = count($this->data['tabledata']['data']);
			//echo "<pre>"; print_r($this->data['attrs']); echo "</pre>";
			$this->data['headers'] = $column_headers;
			for($i=0; $i<count($this->data['headers']); $i++){
			 $this->data['tabledata']['headers'][$i]["heading"] = $this->data['headers'][$i];
			 $this->data['tabledata']['headers'][$i]["width"] = "";
			 $this->data['tabledata']['headers'][$i]["col_id"] = $i + 1; 
			 $this->data['tabledata']['headers'][$i]["order"] = $i;
			 //$this->data['tabledata']['headers'][$i]["col_definition"] = $colDefinition[$i];
			 //$this->data['tabledata']['headers'][$i]["visible"] = ($i < 4)? 1:"";
			  
			}
			$this->data['tabledata']['row_num'] = 0;
			$this->data['tabledata']['start_num'] = 0;
			$this->data['js']['headers'] = $this->data['tabledata']['headers'];
			
			
			$serialNum =  array();
			if(count($this->data['tabledata']['data']) != 0){
			foreach($this->data['tabledata']['data'] as $serKey => $serVal){
				$serialNum[]  = $serVal['Serial Number'];
			}
			}
			
			$assetIdArray = array();
			
			for($i = 0; $i < count($serialNum); $i++){
				if(count($serialNum) != 0){
					$query = "select CO.FS_Asset.ID from CO.FS_Asset where CO.FS_Asset.Serial_Number = '".$serialNum[$i]."'";
					$assetIDs = RNCPHP\ROQL::query($query)->next();
					$assetid = $assetIDs ->next();
					$assetIdArray[$i] = $assetid["ID"];                
				}
			}
		 
			$this->data['tabledata']['assetid'] = $assetIdArray;
			
			$this->data["js"]["assets"] = $assetIdArray;
			 return parent::getData();
} */




		if($this->data["attrs"]["report_id"]=='100429' || $this->data["attrs"]["report_id"]=='100520')
		{
			$this->data['tabledata']['transferredData'] = $transferdata;
			$this->data['tabledata']['headers'] = array();
			$this->data["js"]["totalrecords"] = $nrows;
			$this->data["js"]["pagerecords"] = count($this->data['tabledata']['data']);
			//echo "<pre>"; print_r($this->data['attrs']); echo "</pre>";
			$this->data['headers'] = $column_headers;
			for($i=0; $i<count($this->data['headers']); $i++){
			 $this->data['tabledata']['headers'][$i]["heading"] = $this->data['headers'][$i];
			 $this->data['tabledata']['headers'][$i]["width"] = "";
			 $this->data['tabledata']['headers'][$i]["col_id"] = $i + 1; 
			 $this->data['tabledata']['headers'][$i]["order"] = $i;
			 $this->data['js']['headers'] = $this->data['tabledata']['headers'];
			  $this->data['js']['data'] = $this->data['tabledata']['data'];
	
			}
			//print_r($this->data['tabledata']['data']);
		//print_r($resultdata);
		}
		
		else
		{
		
		
        $url = $_SERVER['REQUEST_URI'];
		//echo $url;
		if($url!= "/app/warranty/my_claims")
		{
			//$this->data["js"]["tabledata"] = $this->data['tabledata']['data'];
			$this->data["tabledata"]["data"] = array();
			
		}	
 
			$this->data['tabledata']['transferredData'] = $transferdata;
			$this->data['tabledata']['headers'] = array();
			$this->data["js"]["totalrecords"] = $nrows;
			$this->data["js"]["pagerecords"] = count($this->data['tabledata']['data']);
			//echo "<pre>"; print_r($this->data['attrs']); echo "</pre>";
			$this->data['headers'] = $column_headers;
			for($i=0; $i<count($this->data['headers']); $i++){
			 $this->data['tabledata']['headers'][$i]["heading"] = $this->data['headers'][$i];
			 $this->data['tabledata']['headers'][$i]["width"] = "";
			 $this->data['tabledata']['headers'][$i]["col_id"] = $i + 1; 
			 $this->data['tabledata']['headers'][$i]["order"] = $i;
			 //$this->data['tabledata']['headers'][$i]["col_definition"] = $colDefinition[$i];
			 //$this->data['tabledata']['headers'][$i]["visible"] = ($i < 4)? 1:"";
			  
			}
			$this->data['tabledata']['row_num'] = 0;
			$this->data['tabledata']['start_num'] = 0;
			$this->data['js']['headers'] = $this->data['tabledata']['headers'];
			
			
			$serialNum =  array();
			if(count($this->data['tabledata']['data']) != 0){
			foreach($this->data['tabledata']['data'] as $serKey => $serVal){
				$serialNum[]  = $serVal['Serial Number'];
			}
			}
			
			$assetIdArray = array();
			
			for($i = 0; $i < count($serialNum); $i++){
				if(count($serialNum) != 0){
					$query = "select CO.FS_Asset.ID from CO.FS_Asset where CO.FS_Asset.Serial_Number = '".$serialNum[$i]."'";
					$assetIDs = RNCPHP\ROQL::query($query)->next();
					$assetid = $assetIDs ->next();
					$assetIdArray[$i] = $assetid["ID"];                
				}
			}
		 
			$this->data['tabledata']['assetid'] = $assetIdArray;
			
			$this->data["js"]["assets"] = $assetIdArray;
			
			
			
			 return parent::getData();
		}
	
       

    }

    /**
     * Handles the default_ajax_endpoint AJAX request
     * @param array $params Get / Post parameters
     */
    function handle_default_ajax_endpoint($params) {
    	//print_r($_REQUEST["formData"]);
    	$data = json_decode($_REQUEST["formData"]);
    	echo $this->CI->model('custom/KeywordSearchModel')->getPagination($data, array(), ($smartAssistant === 'true'))->toJson();
        // Perform AJAX-handling here...
        // echo response
    }
}