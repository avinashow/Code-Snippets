<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Incident Creation</title>
	<style>
		.error {color: #FF0000;}
	</style>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<script>
		$(document).ready(function() {
			$("#attachment_form").hide();
			$("#contact_form").hide();
			$("input[name=choiceofcreation]").click(function() {
				$("span").fadeOut();
				$("p").fadeOut("slow");
				if ($(this).val() === "Through Excel") {
					$("#contact_form").hide();
					$("#attachment_form").show();
				} else {
					$("#attachment_form").hide();
					$("#contact_form").show();
				}
			});
		});
	</script>
</head>
<body>
<h2>Form For Creating Incident(with Existing or new Contact)</h2>
<div>
	<input type="radio" name="choiceofcreation" value="Through form"/>Through Form
	<input type="radio" name="choiceofcreation" value="Through Excel"/>Through Excel
</div>
<form id="attachment_form" action="" method="POST" enctype="multipart/form-data">
	<input type="file" name="contact_excel"/><br>
	<input type="submit" id="upload_button" value="upload"/>
</form>
<form id="contact_form" action="" method="POST" enctype="multipart/form-data">
	<div class="row">
		<label for="name">First Name:</label><br />
		<input id="name" class="input" name="firstname" type="text" value="" size="30" required/><br />
	</div>
	<div class="row">
		<label for="name">Last Name:</label><br />
		<input id="name" class="input" name="lastname" type="text" value="" size="30" required/><br />
	</div>
	<div class="row">
		<label for="age">Age:</label><br />
		<input id="name" class="input" name="age" type="text" value="" size="30"/><br />
	</div>
	<div class="row">
		<label for="email">Email:</label><br />
		<input id="email" class="input" name="email" type="email" title="Enter Valid Email" value="" size="30" required/><br />
	</div>
	<div class="row">
		<label for="phone">Phone Number:</label><br />
		<input id="email" class="input" name="phone" pattern="\d{3}[\-]\d{3}[\-]\d{4}" type="tel" title="Pattern xxx-xxx-xxxx" value="" size="30" /><br />
	</div>
	<div class="row">
		<label for="message">Subject:</label><br />
		<textarea id="message" class="input" name="request" rows="7" cols="30" required></textarea><br />
	</div><br>
	<input id="submit_button" type="submit" value="Submit" />
</form>
<?php

	ini_set('display_errors', 1);
	require_once( get_cfg_var( 'doc_root' ).'/include/ConnectPHP/Connect_init.phph' );
	require_once('include/init.phph');
	initConnectAPI("avinashow","avinash");
	use RightNow\Connect\v1_2 as RNCPHP;

	function create_incident($subject, $co_id) {
		$incident = new RNCPHP\Incident();	 
		$incident->Subject = $subject;
		$incident->PrimaryContact = RNCPHP\Contact::fetch($co_id);
		$incident->save(RNCPHP\RNObject::SuppressAll);
		echo "<p style='color:green'>Incident Created</p>";
	}
	function create_contact($fname, $lname, $email, $age, $phone) {
		$contact = new RNCPHP\Contact();
		$contact->Emails = new RNCPHP\EmailArray();
		$contact->Emails[0] = new RNCPHP\Email();
		$contact->Emails[0]->AddressType=new RNCPHP\NamedIDOptList();
		$contact->Emails[0]->AddressType->LookupName = "Email - Primary";
		$contact->Emails[0]->Address = $email;
		$contact->Name = new RNCPHP\PersonName();
		$contact->Name->First = $fname;
		$contact->Name->Last = $lname;
		$contact->Login = $fname;
		$contact->CustomFields->c->age = $age;
		$contact->Phones = new RNCPHP\PhoneArray();
		$contact->Phones[0] = new RNCPHP\Phone();
		$contact->Phones[0]->PhoneType = new RNCPHP\NamedIDOptList();
		$contact->Phones[0]->PhoneType->LookupName = 'Office Phone';
		$contact->Phones[0]->Number = $phone;
		$contact->save(RNCPHP\RNObject::SuppressAll);
		echo "<p style='color:green'>NewContact Created</p>";
	}
	function update_contact($existing_contact) {
		$ex_cont = RNCPHP\ROQL::query("select Contact.ID from Contact where emails.emaillist.address = '$existing_contact[2]'")->next();
		$c_id = "";
		while ($res = $ex_cont ->next()) {
			$c_id = $res["ID"];
		}
		$exist_contact = RNCPHP\Contact::fetch($c_id);
		$exist_contact->Name->First = $existing_contact[0];
		$exist_contact->Name->Last = $existing_contact[1];
		$exist_contact->CustomFields->c->age = $existing_contact[3];
		$exist_contact->Phones = new RNCPHP\PhoneArray();
		$exist_contact->Phones[0] = new RNCPHP\Phone();
		$exist_contact->Phones[0]->PhoneType = new RNCPHP\NamedIDOptList();
		$exist_contact->Phones[0]->PhoneType->LookupName = 'Office Phone';
		$exist_contact->Phones[0]->Number = $existing_contact[4];
		$exist_contact->save(RNCPHP\RNObject::SuppressAll);
	}
	function fetch_all_contact_details() {
		$arr = array();
		$result_set = RNCPHP\ROQL::query("select emails.emaillist.address, name.first, name.last from Contact where emails.emaillist.address is not null")->next();
		while ($res = $result_set->next()) {
			$arr[$res["Address"]] = $res["First"]."_".$res["Last"];
		}
		return $arr;
	}
	if ($_SERVER["REQUEST_METHOD"] == "POST") {	
		if (isset($_FILES['contact_excel'])) {
			$existing_contacts = fetch_all_contact_details();
			$tmpName = $_FILES['contact_excel']['tmp_name'];
			$handle = fopen($tmpName, 'r');
			$row = 0;
			$succes_count = 0;
			while(($data = fgetcsv($handle)) !== FALSE) {
				if ($row > 0) {
					if (array_key_exists($data[2], $existing_contacts)) {
						update_contact($data);
						echo "<p style='color:orange'>Updated Contact $data[2]</p>";
						echo "<span style='color:red'>Contact: '$data[2]' exists</span><br>";
					} else {
						create_contact($data[0], $data[1], $data[2], $data[3], $data[4]);
						$succes_count++;
					}
				}
				$row++;
			}
			if ($succes_count === 0) {
				echo "<span style='color:blue'>No Contact Created</span><br>";
			} else {
				echo "<span style='color:green'>$succes_count Contact(s) Created</span><br>";
			}
			fclose($handle);
		}else {
			$firstname = $_POST["firstname"];
			$lastname = $_POST["lastname"];
			$email = $_POST["email"];
			$phoneno = $_POST["phone"];
			$subject = $_POST["request"];
			$age = $_POST["age"];
			
			
			$result_list = RNCPHP\Contact::find("Contact.Emails.EmailList.Address = '$email'");
			
			
			if (count($result_list) === 0) {
				try{
					create_contact($firstname, $lastname, $email, $age, $phoneno);
					create_incident($subject, $contact->ID);
				} catch (Exception $err ){
					echo $err->getMessage();
				}
			} else {
				
				echo "<p style='color:red'>Email Already Exist<p>"; 
				
				create_incident($subject, $result_list[0]->ID);
			}
		}
	}
?>
</body>
</html>