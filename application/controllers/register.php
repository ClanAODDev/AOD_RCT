<?php

include "../lib.php";

$data = NULL;

// values are bound and prepared in PDO
$user = $_POST['user'];
$pass = $_POST['password'];
$passVerify = $_POST['passVerify'];
$email = $_POST['email'];

if (stristr($user, 'aod_')) {
	$data['success'] = false;
	$data['message'] = "Please do not include 'AOD_' to your username";

} else if ($pass != $passVerify) {

	$data['success'] = false;
	$data['message'] = "Passwords must match.";

} else if (userExists($user)) {

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