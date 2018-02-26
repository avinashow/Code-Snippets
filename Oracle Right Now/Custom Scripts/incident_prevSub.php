<?php
/*
* CPMObjectEventHandler: incident_prevSub
* Package: OracleServiceCloud
* Objects: Incident
* Actions: Create,Update
* Version: 1.2
*/
// This object procedure binds to v1_2 of the Connect PHP API
use \RightNow\Connect\v1_2 as RNCPHP;

// This object procedure binds to the v1 interface of the process
// designer
use \RightNow\CPM\v1 as RNCPM;
require_once( get_cfg_var( 'doc_root' ).'/custom/incident_auditlog.php');
/**
* An Object Event Handler must provide two classes:
* - One with the same name as the CPMObjectEventHandler tag
* above that implements the ObjectEventHandler interface.
* - And one of the same name with a "_TestHarness" suffix
* that implements the ObjectEventHandler_TestHarness interface.
*
* Each method must have an implementation.
*/
class incident_prevSub
implements RNCPM\ObjectEventHandler
{

public static function
apply( $run_mode, $action, $obj, $n_cycles )
{
   try
		{
			
			$prevSub = $obj->prev->Subject;
			$prevContact = $obj->prev->PrimaryContact->Emails[0]->Address;
			$prevStatus = $obj->prev->StatusWithType->Status->LookupName;
			$prevSeverity = $obj->prev->Severity->LookupName;
			$prevProduct = $obj->prev->Product->ID;
			$prevCategory = $obj->prev->Category->ID;
			$prevDisposition = $obj->prev->Disposition->ID;
			
			if ($prevSub != $obj->Subject) {
				$log = new RNCPHP\PSLog\Log();
				$log->SubType = "Subject Field";
				$log->Message = $prevSub;
				$log->Note = $obj->Subject;
				$log->save();
			}
			if ($prevContact != $obj->PrimaryContact->Emails[0]->Address) {
				$log = new RNCPHP\PSLog\Log();
				$log->SubType = "Contact Field";
				$log->Message = $prevContact;
				$log->Note = $obj->PrimaryContact->Emails[0]->Address;
				$log->save();
			}
			if ($prevStatus != $obj->StatusWithType->Status->LookupName) {
				$log = new RNCPHP\PSLog\Log();
				$log->SubType = "Status Field";
				$log->Message = $prevStatus;
				$log->Note = $obj->StatusWithType->Status->LookupName;
				$log->save();
			}
			if ($prevSeverity != $obj->Severity->LookupName) {
				$log = new RNCPHP\PSLog\Log();
				$log->SubType = "Severity Field";
				$log->Message = $prevSeverity;
				$log->Note = $obj->Severity->LookupName;
				$log->save();
			}
			if ($prevProduct != $obj->Product->ID) {
				$log = new RNCPHP\PSLog\Log();
				$log->SubType = "Product Field";
				$log->Message = $prevProduct;
				$log->Note = $obj->Product->ID;
				$log->save();
			}
			if ($prevCategory != $obj->Category->ID) {
				$log = new RNCPHP\PSLog\Log();
				$log->SubType = "Category Field";
				$log->Message = $prevCategory;
				$log->Note = $obj->Category->ID;
				$log->save();
			}
			if ($prevDisposition != $obj->Disposition->ID) {
				$log = new RNCPHP\PSLog\Log();
				$log->SubType = "Disposition Field";
				$log->Message = $prevDisposition;
				$log->Note = $obj->Disposition->ID;
				$log->save();
			}
			$obj->save();
		} catch (Exception $err) {
			echo $err;
		 }
		return;
} // apply()

} // class obj_create_update
/*
The Test Harness
*/
class incident_prevSub_TestHarness
implements RNCPM\ObjectEventHandler_TestHarness
{
static $con_invented;

public static function setup()
{
// For this test, create a new
// contact and incident as expected.

			$inc = RNCPHP\Incident::fetch(14860);
	       
	   
			$inc->Subject="testing";
	        $inc->save(RNCPHP\RNObject::SuppressAll);
	        static::$con_invented = $inc;
	        return;

}

public static function fetchObject( $action, $object_type )
{
// Return the object that we
// want to test with.
// You could also return an array of objects
// to test more than one variation of an object.
return static::$con_invented;
}

public static function validate( $action, $object )
{
return true;
}
public static function cleanup()
{
// Destroy every object invented
// by this test.
// Not necessary since in test
// mode and nothing is committed,
// but good practice if only to
// document the side effects of
// this test.
//static::$inc_invented->destroy().
//static::$con_invented = NULL;
//static::$inc_invented = NULL;
return;
}

}