<?php

include "../lib.php";

if (!isset($_SESSION['secure_access']) || (isset($_SESSION['secure_access']) && $_SESSION['secure_access'] !== true)) { header("Location: /404/"); }

$data = NULL;





$userRole = $_POST['userRole'];


// type of update
$action = $_POST['action'];







if ($pass != $passVerify) {

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