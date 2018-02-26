<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Update Account</title>
	<style>
		#upload_form {
			position:absolute;
			width: 50%;
			height: 30%;
		}
	</style>
</head>
<body>
	<div id="upload_form">
		<form id="attachment_form" action="" method="POST" enctype="multipart/form-data">
			<input type="file" name="kpi_excel"/><br>
			<input type="submit" id="upload_button" value="upload"/>
		</form>
	</div>
</body>
<?php
	ini_set('display_errors', 1);
	require_once( get_cfg_var( 'doc_root' ).'/include/ConnectPHP/Connect_init.phph' );
	require_once('include/init.phph');
	initConnectAPI("avinashow","avinash");
	use RightNow\Connect\v1_2 as RNCPHP;
	
	
	function create_kpi($data) {
		echo $data[2];
		$kpi = new RNCPHP\CO\KPITable();
		$kpi->TTR = $data[0];
		$kpi->AHT = $data[1];
		$kpi->Channel = "N/A";
		$kpi->Metric3 = $data[2];
		$kpi->Metric4 = "N/A";
		$kpi->Metric5 = "N/A";
		$kpi->save(RNCPHP\RNObject::SuppressAll);
		echo $kpi->ID;
	}
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {	
		if (isset($_FILES['kpi_excel'])) {
			$tmpName = $_FILES['kpi_excel']['tmp_name'];
			$handle = fopen($tmpName, 'r');
			$row = 0;
			while(($data = fgetcsv($handle)) !== FALSE) {
				if ($row > 0) {
					create_kpi($data);
				}
				$row++;
			}
			fclose($handle);
		}
	}
	

?>
</html>