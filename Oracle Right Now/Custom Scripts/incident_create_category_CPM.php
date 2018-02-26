<?php
/*
* CPMObjectEventHandler: incident_create_category_CPM
* Package: OracleServiceCloud
* Objects: Incident
* Actions: Create
* Version: 1.2
*/
// This object procedure binds to v1_2 of the Connect PHP API
use \RightNow\Connect\v1_2 as RNCPHP;

// This object procedure binds to the v1 interface of the process
// designer
use \RightNow\CPM\v1 as RNCPM;
/**
* An Object Event Handler must provide two classes:
* - One with the same name as the CPMObjectEventHandler tag
* above that implements the ObjectEventHandler interface.
* - And one of the same name with a "_TestHarness" suffix
* that implements the ObjectEventHandler_TestHarness interface.
*
* Each method must have an implementation.
*/
class incident_create_category_CPM
implements RNCPM\ObjectEventHandler
{

public static function
apply( $run_mode, $action, $obj, $n_cycles )
{
   try
		{

		if($action ==1)
		{
			
			$cat=$obj->Category->LookupName;
			$obj->CustomFields->c->customer_category=$cat;					
					
		}
		$obj->save();



	    }
		 catch (Exception $err)
		 {
			echo $err;
		 }
		return;



} // apply()

} // class obj_create_update
/*
The Test Harness
*/
class incident_create_category_CPM_TestHarness
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