<?php
	ini_set('display_errors', 1);
	require_once( get_cfg_var("doc_root")."/ConnectPHP/Connect_init.php");
	use RightNow\Connect\v1_2 as RNCPHP;
	initConnectAPI("speridian","Speridian2017");
	
	
	$results = array();
	$filter = new RNCPHP\AnalyticsReportSearchFilter;
	$filter->Name = 'status';
	$filter->Values = array("created");
	$filters = new RNCPHP\AnalyticsReportSearchFilterArray;
	$filters[] = $filter;
	$ar= RNCPHP\AnalyticsReport::fetch(101980);   // Report ID
	$arr= $ar->run( 0, $filters);
	$j=0;
	for ( $ii = $arr->count(); $ii--; ) {
		$results[] = $arr->next();
		$id = process($results[$j]["ID"]);
		$j++;
	}
	
	function process($ID) {
	
		$HQobj = RNCPHP\ROQL::queryObject("select obj from CO.health_equity_CO obj where obj.ID = ".$ID)->next();
		print_r("<pre>");
		print_r("record ".$ID);
		print_r("<br>");
		$obj = $HQobj->next();	
		print_r($orgfields);
		//fetching the health_equity_CO MetaData
		$fieldarrays = getMetadata($obj);	
			
		//Organization fields
		$orgfields = $fieldarrays["orgfields"];
		
		//RAWorkflow object fields
		$rawfields = $fieldarrays["rawfields"];
		
		//Contacts Fields
		$confields = $fieldarrays['confields'];
		
		
		try
		{	
			$orgid = createOrg($orgfields, $obj);	
			echo "organization created:\n";
			echo $orgid."\n";
			$rawid = createRAW($rawfields, $obj, $orgid);
			echo "RAW created:\n";
			echo $rawid."\n";
			$conids = createContacts($confields, $obj, $orgid);	
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
		return $obj->ID;
	}
	
	function getStates() {
		$states = array();
		$stateobj = RNCPHP\ROQL::query("select country.provinces.name as sname from country where country.lookupname = 'US'")->next();
		while ($sobj = $stateobj->next()) {
			array_push($states, $sobj["sname"]);
		}
		return $states;
	}
	
	
	function createOrg($fieldarr,$obj) {
		//Creating the Org Object
		$org = new RNCPHP\Organization();
		
		$states = getStates();
				
		print_r($states);
		
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
			
			$org->Addresses[0]->Country = RNCPHP\Country::fetch(1);		
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
				 	$org->Addresses[0]->Country = RNCPHP\Country::fetch(1);							 	
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
			print_r("<pre>");
			print_r($org);
			$org->save();
		} catch (Exception $error) {
			echo "invalid state lookupname";
			$flag = false;
		}
		return $org->ID;
	}
	
	function createRAW($rawfields,$obj,$orgid) {
		$raworkflow = new RNCPHP\RAWorkflow\RAWorkflow;
		try {
			for ($i = 0; $i < count($rawfields); $i++) {
				$field = explode("RAW_",$rawfields[$i]);
				$raworkflow->$field[count($field) - 1] = $obj->$rawfields[$i];
			}
			$raworkflow->EmployerOrgID = RNCPHP\Organization::fetch($orgid);
			$raworkflow->save(RNCPHP\RNObject::SuppressAll);
		} catch (Exception $error) {
		}
		return $raworkflow->ID;
	}
	
	function createContacts($confields, $obj, $orgid) {
		echo "called contact\n";
		print_r("<pre>");
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
					print_r($email);
					if (strlen($email) > 0) {
						$conobj = RNCPHP\ROQL::queryObject("select con from Contact con where con.Emails.Address='".$email."'")->next();
						if ($conobj->count() > 0) {
							$conid = coninfo($key,$orgid,$value,$obj,$conobj->next());
						} else {
							$conid = coninfo($key,$orgid,$value,$obj, NULL);
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
	
	function coninfo($key,$orgid,$value, $obj, $flag) {
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
						$contact->save(RNCPHP\RNObject::SuppressAll);
					} catch (Exception $error) {
						echo "Line 227 ".$error->getMessage();
					}
					
					return $contact->ID;
					
				}
				return NULL;
	}
		
	function getMetaData($obj) {
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
	

	
	
	

?>


