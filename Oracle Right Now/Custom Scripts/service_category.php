<?php
	
set_time_limit(0);
ignore_user_abort(true);
ini_set('display_errors', 1);
require_once( get_cfg_var("doc_root")."/ConnectPHP/Connect_init.php");
use RightNow\Connect\v1_2 as RNCPHP;
initConnectAPI();

$file = fopen('category_list.phph' , 'r');
try{
	while (($line = fgetcsv($file)) != "") {
		
		$result_product = RNCPHP\ServiceProduct::find("ServiceProduct.name = '$line[0]'");
		$result_category = RNCPHP\ServiceCategory::find("ServiceCategory.name = '$line[1]'");
		
		if (count($result_product) > 0) {
			
			$serviceCategory = new RNCPHP\ServiceCategory();
			$ServiceCategory->Names = new RNCPHP\LabelRequiredArray();
			$ServiceCategory->Names[0] = new RNCPHP\LabelRequired();
			$ServiceCategory->Names[0]->LabelText = $line[1];
			$ServiceCategory->Names[0]->Language = new RNCPHP\NamedIDOptList();
			$ServiceCategory->Names[0]->Language->ID = 1;
			$ServiceCategory->save();
			
			$result_set[0]->CategoryLinks = new RNCPHP\ServiceCategoryDeltaArray();
			$result_set[0]->CategoryLinks[count($result_product->CategoryLinks)] = new RNCPHP\ServiceCategoryDelta();
			$result_set[0]->CategoryLinks[count($result_product->CategoryLinks)]->ServiceCategory = RNCPHP\ServiceCategory ::fetch($serviceCategory->ID);
			
			$result_set->save();
			
		} else {
			
			$ServiceProduct = new RNCPHP\ServiceProduct();
			$ServiceProduct->Names = new RNCPHP\LabelRequiredArray();
			$ServiceProduct->Names[0] = new RNCPHP\LabelRequired();
			$ServiceProduct->Names[0]->LabelText = $line[0];
			$ServiceProduct->Names[0]->Language = new RNCPHP\NamedIDOptList();
			$ServiceProduct->Names[0]->Language->ID = 1;
			
			$cat_len = count($result_product->CategoryLinks);
			$serviceCategory = new RNCPHP\ServiceCategory();
			$ServiceCategory->Names = new RNCPHP\LabelRequiredArray();
			$ServiceCategory->Names[0] = new RNCPHP\LabelRequired();
			$ServiceCategory->Names[0]->LabelText = $line[1];
			$ServiceCategory->Names[0]->Language = new RNCPHP\NamedIDOptList();
			$ServiceCategory->Names[0]->Language->ID = 1;
			$ServiceCategory->save();
			
			$ServiceProduct->CategoryLinks = new RNCPHP\ServiceCategoryDeltaArray();
			$ServiceProduct->CategoryLinks[count($result_product)] = new RNCPHP\ServiceCategoryDelta();
			$ServiceProduct->CategoryLinks[count($result_product)]->ServiceCategory = RNCPHP\ServiceCategory ::fetch($serviceCategory->ID);
			
			$ServiceProduct->save();
		}
	}
} catch(Exception $err) {
	echo $err;
}

?>