<?php

include "../lib.php";

$data = NULL;


// values are bound and prepared in PDO
$user = $_POST['user'];
$pass = $_POST['password'];
$email = $_POST['email'];

if (userExists($user)) {
	$data['success'] = false;
	$data['message'] = "That username has already been used.";
} else {
	
		createUser($user, $email, $pass);
		$data['success'] = true;
		$data['message'] = "Your account was created!";
	
}

echo json_encode($data);
exit;

?>