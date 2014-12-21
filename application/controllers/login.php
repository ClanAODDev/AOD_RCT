<?php

include "../lib.php";
$data = NULL;

error_reporting(E_ALL);
ini_set('display_errors', '1');

$string = NULL;
$key = "archie";

$hexa = $_REQUEST['password'];
for ($i=0; $i < strlen($hexa)-1; $i+=2) {
	$string .= chr(hexdec($hexa[$i].$hexa[$i+1]));
}

$decrypt_pass = mcrypt_decrypt(MCRYPT_DES, $key, $string, MCRYPT_MODE_ECB);

$user = $_POST['user'];
$pass = $decrypt_pass;

$userexists = userExists($user);

if (!$userexists) { 
	$data['success'] = false;
	$data['message'] = 'Your credentials are incorrect';

} else {

	// valid password returns a user id
	$id = validatePassword($pass, $user);
	if (!$id) {

		$data['success'] = false;
		$data['message'] = 'Your credentials are incorrect';    

	} else {

		$data['success'] = true;
		$data['message'] = 'You have been logged in';  
		
		session_start();
		updateLoggedInTime($user);  
		$_SESSION['username'] = $user;
		$_SESSION['loggedIn'] = true;
		$_SESSION['user_id'] = $id;
	}

}


echo json_encode($data);
exit;

?>