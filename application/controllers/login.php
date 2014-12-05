<?php

include "../lib.php";
$data = NULL;


error_reporting(E_ALL);
ini_set('display_errors', '1');


$user = $_POST['user'];
$pass = $_POST['password'];


$userexists = userExists($user);

if (!$userexists) { 
	$data['success'] = false;
	$data['message'] = 'Your credentials are incorrect';

} else {

	if (!validatePassword($pass, $user)) {

		$data['success'] = false;
		$data['message'] = 'Your credentials are incorrect';    

	} else {

		$data['success'] = true;
		$data['message'] = 'You have been logged in';  
		
		session_start();  
		$_SESSION['username'] = $user;
		$_SESSION['loggedIn'] = true;
	}

}


echo json_encode($data);
exit;

?>