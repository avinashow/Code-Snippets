<?php
/*
* CPMObjectEventHandler: health_equity_Form_Object
* Package: CO
* Objects: CO\health_equity_CO
* Actions: Create
* Version: 1.2
*/
// This object procedure binds to v1_1 of the Connect for PHP API
//use \RightNow\Connect\v1_1 as RNCPHP;
use \RightNow\Connect\v1_2 as RNCPHP;

// This object procedure binds to the v1 interface of the process designer
use \RightNow\CPM\v1 as RNCPM;

if (!defined('CUSTOM_SCRIPT'))
	define('CUSTOM_SCRIPT', 1);



class health_equity_Form_Object implements RNCPM\ObjectEventHandler
{
	
	public static function apply( $run_mode, $action, $obj, $n_cycles )
	{		

		//fetching the health_equity_CO MetaData
		$fieldarrays = health_equity_Form_Object::getMetaData($obj);
		
		
		//Organization fields
		$orgfields = $fieldarrays["orgfields"];
		
		//RAWorkflow object fields
		$rawfields = $fieldarrays["rawfields"];
		
		//Contacts Fields
		$confields = $fieldarrays['confields'];
		
		
		try
		{	
			$orgid = health_equity_Form_Object::createOrg($orgfields, $obj);	
			echo "organization created:\n";
			echo $orgid."\n";
			$rawid = health_equity_Form_Object::createRAW($rawfields, $obj, $orgid);
			echo "RAW created:\n";
			echo $rawid."\n";
			$conids = health_equity_Form_Object::createContacts($confields, $obj, $orgid);	
			echo "contacts created:\n";
			print_r($conids);	
			if (count($conids) > 0) {
				$obj->Status = "Processed";
				$obj->save();
			}
		}
		catch (Exception $error) 
		{
			echo "Line 52 " .$error->getMessage();
			$obj->Status = "Error Occurred";
		}
		//RNCPHP\ConnectAPI::commit();
		return;
	} // apply()
	
	public static function createOrg($fieldarr,$obj) {
		//Creating the Org Object
		$org = new RNCPHP\Organization();
		
		$orgobj = RNCPHP\ROQL::queryObject("select org from organization org where org.Name= '".$obj->org_Name."'")->next();
		
		if ($orgobj->count() > 0) {
			$org = $orgobj->next();
		} else {
			//Initializing CustomFields of package c
			$omd = $org::getMetadata();
		    $org_type_name = $omd->CustomFields->type_name;
		    $orgmd = $org_type_name::getMetadata();
		    $org->CustomFields->c = new $orgmd->c->type_name;
			
			
			//Creating Organization Addresses Object
			$org->Addresses = new RNCPHP\TypedAddressArray();
			$org->Addresses[0] = new RNCPHP\TypedAddress();
			$org->Addresses[0]->AddressType = new RNCPHP\NamedIDLabel();
			$org->Addresses[0]->AddressType->LookupName = "Primary";	
		}

		try {
			for ($i = 0; $i < count($fieldarr); $i++) {
				$fieldsplit = explode("_",$fieldarr[$i]);
				if (strpos($fieldarr[$i],"_CF_c_")) {
					$field = explode("_c_",$fieldarr[$i]);
					$org->CustomFields->c->$field[count($field) - 1] = $obj->$fieldarr[$i];									
				 } elseif (strpos($fieldarr[$i],"_addresses_StateOrProvince")) {								
					$org->Addresses[0]->StateOrProvince = new RNCPHP\NamedIdLabel();
					$org->Addresses[0]->StateOrProvince->LookupName = $obj->$fieldarr[$i];									
				 } elseif (strpos($fieldarr[$i],"_addresses_Country") == true) {
				 	$org->Addresses[0]->Country = RNCPHP\Country::fetch($obj->$fieldarr[$i]);							 	
				 } elseif(strpos($fieldarr[$i],"_addresses_") == true) {	
				 	if ($fieldsplit[count($fieldsplit) - 1] == "postalcode") {
						$org->Addresses[0]->PostalCode = $obj->$fieldarr[$i];								 		
				 	} else {						
						$org->Addresses[0]->$fieldsplit[count($fieldsplit) - 1] = $obj->$fieldarr[$i];
					}
				 } else {								
					$org->$fieldsplit[count($fieldsplit) - 1] = $obj->$fieldarr[$i];
				 }	
			}
			$org->save();
		} catch (Exception $error) {
		}
		return $org->ID;
	}
	
	public static function createRAW($rawfields,$obj,$orgid) {
		$raworkflow = new RNCPHP\RAWorkflow\RAWorkflow;
		try {
			for ($i = 0; $i < count($rawfields); $i++) {
				$field = explode("RAW_",$rawfields[$i]);
				$raworkflow->$field[count($field) - 1] = $obj->$rawfields[$i];
			}
			$raworkflow->EmployerOrgID = RNCPHP\Organization::fetch($orgid);
			$raworkflow->save();
		} catch (Exception $error) {
		}
		return $raworkflow->ID;
	}

	
	
	public static function createContacts($confields, $obj, $orgid) {
		$conarr = array();
		try {
		//Looping to create Contact Objects
				foreach($confields as $key => $value) {
					$email = "";
					if ($key === "Contact") {
						$email = $obj->$value[0];
					} else {
						$email = $obj->$value[1];
					}
					if (strlen($email) > 0) {
						$conobj = RNCPHP\ROQL::queryObject("select con from Contact con where con.Emails.Address='".$email."'")->next();
						if ($conobj->count() > 0) {
							$conid = health_equity_Form_Object::contactinfo($key,$orgid,$value,$obj,$conobj->next());
						} else {
							$conid = health_equity_Form_Object::contactinfo($key,$orgid,$value,$obj, NULL);
						}
					}
					
					if (!is_null($conid)) {
						array_push($conarr,$conid);
					}
					
				}
			} catch(Exception $error) {
				echo "Line 94 ".$error->getMessage();
			}
			return $conarr;
	}
	
	public static function contactinfo($key,$orgid,$value, $obj, $flag) {
		$contact = new RNCPHP\Contact();
		if(is_null($flag)) {
			$md = $contact::getMetadata();
		    $con_type_name = $md->CustomFields->type_name;
		    $conmd = $con_type_name::getMetadata();
		    $contact->CustomFields->c = new $conmd->c->type_name;
		} else {
			$contact = $flag;
		}
	    $count = 0;
		for($i = 0; $i < count($value); $i++) {	
					if (strpos($value[$i],"con_Name") !== false) {
						if (strlen($obj->$value[$i]) > 0) {
							$namearr = explode(" ",$obj->$value[$i]);
							$contact->Name = new RNCPHP\PersonName();
							$contact->Name->First = $namearr[0];
							$contact->Name->Last = $namearr[1];								
						} else {
							$count = 1;
						}					
					} elseif (strpos($value[$i],"_Phones_") !== false) {
						if (strlen($obj->$value[$i]) > 0) {
							$contact->Phones = new RNCPHP\PhoneArray();
							$contact->Phones[0] = new RNCPHP\Phone();
							$contact->Phones[0]->PhoneType = new RNCPHP\NamedIDOptList();
							$contact->Phones[0]->PhoneType->LookupName = 'Office Phone';
							$contact->Phones[0]->Number = $obj->$value[$i];
						} else {
							$count = 1;
						}
					} elseif (strpos($value[$i],"_Emails_") !== false) {
						if (strlen($obj->$value[$i]) > 0) {						
							$contact->Emails = new RNCPHP\EmailArray();
						    $contact->Emails[0] = new RNCPHP\Email();
						    $contact->Emails[0]->AddressType=new RNCPHP\NamedIDOptList();
						    $contact->Emails[0]->AddressType->LookupName = "Email - Primary";
						    $contact->Emails[0]->Address = $obj->$value[$i];
					    } else {
					    	$count = 1;
					    }
					} elseif (strpos($value[$i],"con_CF_") !== false) {
						if (strlen($obj->$value[$i]) > 0) {
							$field = explode("_CF_c_",$value[$i]);
							$customfield = preg_replace('/[0-9]+/', '', $field[count($field)-1]);
							$contact->CustomFields->c->$customfield = new RNCPHP\NamedIdLabel();
							$contact->CustomFields->c->$customfield->LookupName = $obj->$value[$i];
						} else {
							$count = 1;
						}			 								
					} else {	
						if (strlen($obj->$value[$i]) > 0) {
							$fieldsplit = explode("_",$value[$i]);	
							$standardfield = preg_replace('/[0-9]+/', '', $fieldsplit[count($fieldsplit)-1]);						
							$contact->$standardfield = $obj->$value[$i];	
						} else {
							$count = 1;
						}		
					}
				}
				if ($count == 0) {
					if ($key == "Contact") {
						$contact->CustomFields->c->is_primary = 1;
					} else {
						$contact->CustomFields->c->is_primary = 0;						
					}					
					$contact->Organization = RNCPHP\Organization::fetch($orgid);
					try {
						print_r("<pre>");
						print_r($contact);
						$contact->save();
					} catch (Exception $error) {
						echo "Line 227 ".$error->getMessage();
					}
					
					return $contact->ID;
					
				}
				return NULL;
	}

	
	/*public static function createOrgRaw($fieldarr, $obj) {
		//Creating the Org Object
		$org = new RNCPHP\Organization();
		
		//Initializing CustomFields of package c
		$omd = $org::getMetadata();
	    $org_type_name = $omd->CustomFields->type_name;
	    $orgmd = $org_type_name::getMetadata();
	    $org->CustomFields->c = new $orgmd->c->type_name;
		
		
		//Creating Organization Addresses Object
		$org->Addresses = new RNCPHP\TypedAddressArray();
		$org->Addresses[0] = new RNCPHP\TypedAddress();
		$org->Addresses[0]->AddressType = new RNCPHP\NamedIDLabel();
		$org->Addresses[0]->AddressType->LookupName = "Primary";		
		
		//Creating RAWorkflow Object
		$raworkflow = new RNCPHP\RAWorkflow\RAWorkflow;
		
		try {
			for ($i = 0; $i < count($fieldarr); $i++) {
				$fieldsplit = explode("_",$fieldarr[$i]);
				switch($fieldsplit[0]) {
					case "org" : if (strpos($fieldarr[$i],"_CF_c_")) {
									$field = explode("_c_",$fieldarr[$i]);
									$org->CustomFields->c->$field[count($field) - 1] = $obj->$fieldarr[$i];									
								 } elseif (strpos($fieldarr[$i],"_addresses_StateOrProvince")) {								
									$org->Addresses[0]->StateOrProvince = new RNCPHP\NamedIdLabel();
									$org->Addresses[0]->StateOrProvince->LookupName = $obj->$fieldarr[$i];									
								 } elseif (strpos($fieldarr[$i],"_addresses_Country") == true) {
								 	$org->Addresses[0]->Country = RNCPHP\Country::fetch($obj->$fieldarr[$i]);							 	
								 } elseif(strpos($fieldarr[$i],"_addresses_") == true) {	
								 	if ($fieldsplit[count($fieldsplit) - 1] == "postalcode") {
										$org->Addresses[0]->PostalCode = $obj->$fieldarr[$i];								 		
								 	} else {						
										$org->Addresses[0]->$fieldsplit[count($fieldsplit) - 1] = $obj->$fieldarr[$i];
									}
								 } else {								
									$org->$fieldsplit[count($fieldsplit) - 1] = $obj->$fieldarr[$i];
								 }								 								 
								 break;	
								 							 
					case "RAW" : $field = explode("RAW_",$fieldarr[$i]);
								 $raworkflow->$field[count($field) - 1] = $obj->$fieldarr[$i];											
								 break;
				}
			}
			print_r("<pre>");
			print_r($org);
			$org->save();
			$raworkflow->EmployerOrgID = RNCPHP\Organization::fetch($org->ID);
			$raworkflow->save();

		} catch(Exception $error) {
		}
		return $org->ID;
	}
*/	
	
	public static function getMetaData($obj) {
		$objmetdata = $obj::getMetadata();
		$orgfields = array();
		$confields = array();
		$rawfields = array();
		foreach($objmetdata as $key => $value) {
			if (is_object($value)) {
				if(strpos($key,"org_") !== false) {
					array_push($orgfields,$key);
				} elseif (strpos($key,"RAW_") !== false) {
					array_push($rawfields,$key);
				} elseif (strpos($key,"con_") !== false) {
					preg_match_all('!\d+!', $key, $matches);					
					if (count($matches[0]) == 0) {
						if (!array_key_exists("Contact",$confields)) {
							$confields["Contact"] = array();
						}
						array_push($confields["Contact"],$key);
					} else {
						if (!array_key_exists("Contact".$matches[0][0],$confields)) {
							$confields["Contact".$matches[0][0]] = array();							
						} 
						array_push($confields["Contact".$matches[0][0]],$key);
					}
				} else {
				}
			}
		}
		return array("orgfields"=>$orgfields,"confields"=>$confields,"rawfields" => $rawfields);
	}
	
} // class Organization_create

/*
The Test Harness
*/
class health_equity_Form_Object_TestHarness implements RNCPM\ObjectEventHandler_TestHarness
{
	static $heq_invented = NULL;
	public static function setup()
	{
		// For this test, create a new Organization as expected.
		$heq = RNCPHP\CO\health_equity_CO::fetch(69);
		static::$heq_invented = $heq;
		return;
	}
	public static function fetchObject( $action, $object_type )
	{
			return(static::$heq_invented);
	}
	public static function validate( $action, $object )
	{
		return true;
	}
	public static function cleanup()
	{
		return;
	}
}