<?
if (!$Register) die('404/Invalid modification of request');

// Step 2
$FailedValidate=false;
$Failed=array('Username'=>false, 'Password'=>false, 'Email'=>false);
$FailedReason=array('Username'=>'', 'Password'=>'', 'Email'=>'');

$Form=array('Username'=>$_POST['username'], 'Email'=>$_POST['email'], 'Password'=>$_POST['password']);

// Test username
if (!preg_match('/^[a-z0-9_?]{1,20}$/iD',$Form['Username'])) {
	$FailedValidate=true;
	$Failed['Username']=true;
	$FailedReason['Username']="Invalid username";
}
if (!preg_match('/^[_a-z0-9-]+([.+][_a-z0-9-]+)*@[a-z0-9-\.]{1,255}\.[a-zA-Z]{2,6}$/iD',$Form['Email'])) {
	$FailedValidate=true;
	$Failed['Email']=true;
	$FailedReason['Email']="Invalid email";
}

if ((strlen($Form['Password'])<6) || (strlen($Form['Password'])>40)) {
	$FailedValidate=true;
	$Failed['Password']=true;
	$FailedReason['Password']="Invalid password, must be 6 to 40 characters";
}

if ($User->accountNameInUse($Form['Username'])) {
	$FailedValidate=true;
	$Failed['Username']=true;
	$FailedReason['Username']="Username in use";
}

if ($User->accountEmailInUse($Form['Email'])) {
	$FailedValidate=true;
	$Failed['Email']=true;
	$FailedReason['Email']="Email in use";
}

if ($_GET['output']=='json') {
	$response = array();
	if (!$FailedValidate && !($UserID=$User->makeAccount($Form['Username'], $Form['Password'], $Form['Email'],true,1,true)))
		$status=-4;
	else {
		if ($Failed['Username']) $status+=13;
		if ($Failed['Password']) $status+=27;
		if ($Failed['Email']) $status+=43;
	}
	$response[]=array('status'=>$status);
	die(json_encode($response));
	
}

if ($FailedValidate==true) {
	echo "Failed validate, try again<br />";
	require 'register_form.php';
	die();
}



// Wholly validated!
if (!($UserID=$User->makeAccount($Form['Username'], $Form['Password'], $Form['Email'])))
	die("Account creation error!");
else
	

?>