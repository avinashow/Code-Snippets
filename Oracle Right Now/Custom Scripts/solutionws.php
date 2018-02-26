<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Campaign Map</title>
	<style>
		.error {color: #FF0000;}
		table, th{
			border: 1px solid black;
			border-collapse: collapse;
		}
		th {
		   color:white;
		  background-color:black;
		}
		th, td {
			padding: 5px;
		}
	</style>
	
</head>
<body>
<?php
// Find our position in the file tree
if (!defined('DOCROOT')) {
$docroot = get_cfg_var('doc_root');
define('DOCROOT', $docroot);
}
/************* Agent Authentication ***************/
 
// Set up and call the AgentAuthenticator
require_once (DOCROOT . '/include/services/AgentAuthenticator.phph');
// On failure, this includes the Access Denied page and then exits,
// preventing the rest of the page from running.
$p_sid = $_GET['p_sid'];
$account = AgentAuthenticator::authenticateSessionID($p_sid);


	/*ini_set('display_errors', 1);
	require_once( get_cfg_var( 'doc_root' ).'/include/ConnectPHP/Connect_init.phph' );

	require_once('include/init.phph');
	initConnectAPI("avinash","Federal@123");*/
use RightNow\Connect\v1_2 as RNCPHP;
	

	
	
	function create_answer_machine_serial_number($serial_num) {
		 $answer_id =  $_GET["solutionid"];
		$result_set = RNCPHP\ROQL::queryObject("select CO.FS_Asset from CO.FS_Asset where CO.FS_Asset.Serial_Number = '".$serial_num."'")->next();
		if ($result_set->count() > 0) {
			
			$res = $result_set->next();
			$query1 = "select Warranty.answer_machine_map from Warranty.answer_machine_map where Warranty.answer_machine_map.AssetID=$res->ID AND Warranty.answer_machine_map.AnswerID=$answer_id LIMIT 25";
			$result_set1 = RNCPHP\ROQL::queryObject($query1)->next();
			
			if($result_set1->count()==0)
			{
				
			$solution_serial = new RNCPHP\Warranty\answer_machine_map();
			$solution_serial->AssetID = $res;
			$solution_serial->SerialNumber = $serial_num;
			$solution_serial->AnswerID = RNCPHP\Answer::fetch($answer_id);
			$solution_serial->save(RNCPHP\RNObject::SuppressAll);
			}
			else
			{
				echo "<p style='color:red;'>Serial Number : ".$serial_num." already mapped for this solution</p>";
			}
			
			
		} else {
			echo "<p style='color:red;'>Serial Number : ".$serial_num." doesn't exist in CO.FS_Asset table</p>";
		}
	}
	
	function create_answer_machine_model_number($model_num) {
		$answer_id =  $_GET["solutionid"];
		$result_set = RNCPHP\ROQL::queryObject("select CO.FS_Asset from CO.FS_Asset where CO.FS_Asset.Model = '".$model_num."'")->next();
		if ($result_set->count() > 0) {
			while($res = $result_set->next())
			{
				
				$query1 = "select Warranty.answer_machine_map from Warranty.answer_machine_map where Warranty.answer_machine_map.AssetID=$res->ID AND Warranty.answer_machine_map.AnswerID=$answer_id LIMIT 25";
				$result_set1 = RNCPHP\ROQL::queryObject($query1)->next();
				if($result_set1->count()==0)
				{
					
					$solution_model = new RNCPHP\Warranty\answer_machine_map();
					$solution_model->AssetID = $res;
					$solution_model->SerialNumber = $res->Serial_Number;
					$solution_model->AnswerID = RNCPHP\Answer::fetch($answer_id, RNCPHP\RNObject::VALIDATE_KEYS_OFF);
					$solution_model->save(RNCPHP\RNObject::SuppressAll);				
				}
				else
				{
					echo "<p style='color:red;'>Model Number : ".$serial_num." already mapped for this campaign</p>";
				}
			}	
		} else {
			echo "<p style='color:red;'> Model Number : ".$model_num." doesn't exist in CO.FS_Asset table</p>";
		}
	}
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {	
		$answerID =  $_GET["solutionid"];
		if(isset($_FILES["model_num_excel"]) && $_FILES["model_num_excel"]["error"] == 0){
			$tmpName = $_FILES['model_num_excel']['tmp_name'];
			$handle = fopen($tmpName, 'r');
			$row = 0;
			$succes_count = 0;
			if ($answerID < 0) {
				echo "<p style='color:red;'> First Save the Solution Later Upload ModelNumbers</p>";
			} else {
				while(($data = fgetcsv($handle)) !== FALSE) {
					if ($row >= 0) {
						create_answer_machine_model_number($data[0]);
					}
					$row++;
				}
				fclose($handle);
			}
		}
		if(isset($_FILES["serial_num_excel"]) && $_FILES["serial_num_excel"]["error"] == 0){
			$tmpName = $_FILES['serial_num_excel']['tmp_name'];
			$handle = fopen($tmpName, 'r');
			$row = 0;
			$succes_count = 0;
			if ($answerID < 0) {
				echo "<p style='color:red;'> First Save the Solution Later Upload SerialNumbers</p>";
			} else {
				while(($data = fgetcsv($handle,22000)) !== FALSE) {
					if ($row >= 0) {					
						create_answer_machine_serial_number($data[0]);
					}
					$row++;
				}
				fclose($handle);
			}
		}
	}         	
?>

<form action="" method="post" enctype="multipart/form-data">

<table>
  <tr>
    <th colspan="3" style='text-align:left'>AFFECTED UNITS</th>
  </tr>
  <tr>
    <td>
      <input type="radio" name="choiceofcreation" value="Through Excel"/>Upload Serial Number
    </td>
    <td>
      <input type="radio" name="choiceofcreation" value="Through form"/>Upload Model Number
    </td>
    <td></td>
    </tr>
	<tr>
      <td colspan="3">
        <table id="contact_form" style="display:none">
         <tr>
           <th style="text-align:left">Upload Model Numbers</th>
           <th></th>
         </tr>
         <tr>
           <td>
             <input type="file" name="model_num_excel"/>
           </td>
           <td colspan="2">
             <input type="submit" id="upload_model" value="upload"/>
           </td>
         </tr>
        </table>
        <table id="attachment_form" style="display:none">
         <tr>
           <th style='text-align:left'>Upload Serial Numbers</th>
           <th></th>
         </tr>
         <tr>
           <td>
             <input type="file" name="serial_num_excel"/>
           </td>
           <td colspan="2">
             <input type="submit" id="upload_serial" value="upload"/>
           </td>
         </tr>
        </table>
      </td>
    </tr>
</table>
</form>



<script>
	function getEventTarget(e) {
		e = e || window.event;
		return e.target || e.srcElement; 
	}
	var btns = document.getElementsByName("choiceofcreation");
	for (var i = 0; i < btns.length; i++) {
		btns[i].onclick = function(evt){
			var target = getEventTarget(evt);
			if (target.value == "Through Excel") {
				document.getElementById("contact_form").style.display = "none";
				document.getElementById("attachment_form").style.display = "block";
			} else {
				document.getElementById("attachment_form").style.display = "none";
				document.getElementById("contact_form").style.display = "block";
			}
		};
	}
	
</script>



</body>
</html>