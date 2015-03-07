<?php

include "../lib.php";

$data = NULL;

$user = $_POST['user'];
$pass = $_POST['password'];

if (!userExists($user)) { 

	$data['error'] = 'Your credentials are incorrect';

} else {

	// valid password returns a user id
	$id = validatePassword($pass, $user);

	if (!$id) {

		$data['error'] = 'Your credentials are incorrect';    

	} else {

		$data['error'] = null;  

		session_start();
		updateLoggedInTime($user);  

		$_SESSION['username'] = $user;
		$_SESSION['loggedIn'] = true;
		$_SESSION['user_id'] = $id;
	}

}


if (!is_null($data['error'])) {
	echo $data['error'];
} else {
	header('Location: /');
}

?>