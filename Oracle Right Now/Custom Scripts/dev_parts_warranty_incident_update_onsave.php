<?php 
// Start agent authenticator
$sid_parm = '';
define(DEBUG, TRUE);
if (!empty($_POST['p_sid'])) {
	$sid_parm = trim($_POST['p_sid']);
} else if (!empty($_GET['p_sid'])) {
	$sid_parm = trim($_GET['p_sid']);
}

// Find our position in the file tree
if (!defined('DOCROOT')) {
	$docroot = get_cfg_var('doc_root');
	define('DOCROOT', $docroot);
}
/************* Agent Authentication ***************/

// Set up and call the AgentAuthenticator
require_once (DOCROOT . '/include/services/AgentAuthenticator.phph');

$account = AgentAuthenticator::authenticateSessionID($sid_parm);
/********************* end agent authentication ***************************/

// Set up namespace for CPHP.
use RightNow\Connect\v1_2 as RNCPHP;

	try
	{
		$incidentID = $_REQUEST["incidentID"];
		$contactID = $_REQUEST["contactID"];
		echo $contactID;
		echo $incidentID;
		//$contactObj = RNCPHP\Contact::fetch((int)$contactID);
		//print_r($contactObj);
		$partsWarrantyInfoObjects = RNCPHP\ROQL::queryObject("Select pwi from CO.parts_warranty_info pwi where pwi.contactID = '".$contactID."'")->next();
	
		while($partsWarrantyInfoObject = $partsWarrantyInfoObjects->next())
		{	
			
			$partsWarrantyInfoObject->incidentID = $incObj;
			$partsWarrantyInfoObject->save(RNCPHP\RNObject::SuppressAll);
		}
	}
	catch(Exception $e)
	{
		echo "ConnectPHP error was: " . $e -> getMessage();
		phpoutlog("ConnectPHP exception in file: " . __FILE__ . ", line: " . __LINE__);
		phpoutlog("Exception code : " . $e -> getCode());
		phpoutlog("Exception message : " . $e -> getMessage());
		throw $e;
	}
?>
<html>
<head>
</head>
<body>
<form id="frmsearch" name="frmsearch" action="dev_parts_warranty_incident_update_onsave.php">
<input id="incidentID" name="incidentID" type="hidden" value=""> 
<input id="contactID" name="contactID" type="hidden" value="">
<div id="status"></div>
</form>
</body>
<script type="text/javascript">
function onsave()
{
	var i_id = window.external.Incident.Id;
	var c_id = window.external.Incident.CId;
	alert(i_id);
	alert(c_id);
	var contactID = document.getElementById("contactID");
	var incidentID = document.getElementById("incidentID");
	contactID.value = c_id;
	incidentID.value = i_id;
	document.getElementById("frmsearch").submit();
}
</script>
</html>