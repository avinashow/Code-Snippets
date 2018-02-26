<!DOCTYPE html>
<html>
<body>
<header>Form Details:<header>
<br></br>
<form action = "<?php $_PHP_SELF ?>" method = "POST">
	First Name: <br>
	<input type="text" id="firstname" placeholder="First Name" name="firstname"><span>*</span><br >
	Last Name: <br>
	<input type="text" id="lastname" placeholder="Last Name" name="lastname"><span>*</span><br>
	E-mail: <br>
	<input type="text" id="email" placeholder="E-mail ID" name="email"><span>*</span><br>
	Phone: <br>
	<input type="text" id="phone" placeholder="Phone Number" name="phone"><br>
	Request:<br>
	<textarea rows="5" cols="50" name="request"></textarea><span>*</span>
	<br><br>
	<input type="submit" name="submit" value="Submit">
</form> 

</body>
</html>

<?php

ini_set('display_errors', 1);
require_once( get_cfg_var( 'doc_root' ).'/include/ConnectPHP/Connect_init.phph' );
require_once('include/init.phph');
initConnectAPI("abdul2","abdul2");
use RightNow\Connect\v1_2 as RNCPHP;

if( $_POST["email"]) {
      if (!preg_match("/^[a-zA-Z ]*$/",$_Post['email'])) {
         die ("invalid e-mail");
      }
      echo "Welcome ". $_POST['email']. "<br />";
   
   }
   if( $_POST["firstname"]) {
      if (!preg_match("/^[a-zA-Z ]*$/",$_Post['firstname'])) {
         die ("invalid firstname");
      }
      echo "First Name: ". $_POST['firstname']. "<br />";
   
   }
   if( $_POST["lastname"]) {
      if (!preg_match("/^[a-zA-Z ]*$/",$_Post['lastname'])) {
         die ("invalid lastname");
      }
      echo "Last Name: ". $_POST['lastname']. "<br />";
   
   }
   if( $_POST["phone"]) {
       echo "Phone: ". $_POST['phone']. "<br />";
   
   }
   if( $_POST["request"]) {
      if (!preg_match("/^[a-zA-Z ]*$/",$_Post['request'])) {
         die ("invalid");
      }
      echo "Your request ". $_POST['request']. "<br />";
   
   }

try {
    $incident = new RNCPHP\Incident();
 
    $incident->Subject = "Product has problem";
	
	$contact = RNCPHP\Contact::fetch($email);
			if (is_null($contact->email)) {
					// Be sure to instantiate the sub-object
					// if it is not already there
					
					$contact->Emails = new RNCPHP\EmailArray();
					$contact->Emails[0] = new RNCPHP\Email();
					$contact->Emails[0]->AddressType=new RNCPHP\NamedIDOptList();
					$contact->Emails[0]->AddressType->LookupName = "$email";
					//$contact->Emails[0]->Address = "Primary_email_".date("h_i_s")."@example.com";
					
					
					$contact->Name = new RNCPHP\Contact();
									}
					$contact->firstname = $firstname;
					$contact->lastname = $lastname;
					$contact->email = $email;
					$contact->phone = $phone;
 
					$incident->save(RNCPHP\RNObject::SuppressAll);
						echo "Incident Created";
}
 
catch (Exception $err ){
    echo $err->getMessage();
}


?>
