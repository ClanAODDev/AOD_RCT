<?php

include "../lib.php";
$data = NULL;

$user = $_POST['user'];
$pass = $_POST['password'];

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