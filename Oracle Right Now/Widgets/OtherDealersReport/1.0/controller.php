<?php
namespace Custom\Widgets\warranty\otherdealers;
use RightNow\Connect\v1_2 as RNCPHP;
use libcurl;
class OtherDealersReport extends \RightNow\Libraries\Widget\Base {
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
    		
		$generic_filter= new RNCPHP\AnalyticsReportSearchFilter;
    	$filters = new RNCPHP\AnalyticsReportSearchFilterArray;
    	$filterNames = $this->data['attrs']['filter_name'];
    	$filterArray = explode(",",$filterNames);
    	
    	$filterData = $this->data['attrs']['filter_data'];
    	
    	$filterDataArray = explode(",",$filterData);
		
       	$i = 0;
    	foreach($filterArray as $filterKey)
    	{
    	
		$generic_filter->Name = $filterKey;
		$generic_filter->Values = array($filterDataArray[0]);
		$filters[] = $generic_filter;
		$i++;
		}

		$ar= RNCPHP\AnalyticsReport::fetch($this->data['attrs']['report_id']);
		$arr= $ar->run( 0, $filters );
		$transferdata = array();
		// processing results
		$nrows= $arr->count();
		if ( $nrows) {
		    $row = $arr->next();
		    // Emit the column headings
		    //echo( join( ',', array_keys( $row ) ) ."\n" );
		   $this->data['headers'] = array_keys($row);
		    // Emit the rows in this report run
		    $map_arr = array();
		    $map = array();
		    $i = 1;
		    $count = 0;
		    for ( $ii = 0; $ii++ < $nrows; $row = $arr->next() ) {
		    	 if ($count == 10) {
		    	 	$map[$i] = $map_arr;
		    	 	$map_arr = array();
		    	 	array_push($map_arr,$row);
		    	 	$count = 0;
		    	 	$i++;
		    	 } else {
		    	 	array_push($map_arr,$row);
		    	 }
				 $this->data['tabledata']['data'][$ii] = $row;
				 $count++;
		    }
		    $map[$i] = $map_arr;
		}   
		$this->data["js"]["paginatedData"] = $map;
		$this->data["js"]["tabledata"] = $this->data['tabledata']['data'];
		$this->data['tabledata']['transferredData'] = $transferdata;
		$this->data['tabledata']['headers'] = array();
		
		//echo "<pre>"; print_r($this->data['attrs']); echo "</pre>";

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

    /**
     * Handles the default_ajax_endpoint AJAX request
     * @param array $params Get / Post parameters
     */
    function handle_default_ajax_endpoint($params) {
        // Perform AJAX-handling here...
        // echo response
        print_r($params);
    }
}