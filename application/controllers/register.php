<?php

include "../lib.php";

$data = NULL;
$string = NULL;
$key = "archie";

$hexa = $_REQUEST['password'];
for ($i=0; $i < strlen($hexa)-1; $i+=2) {
	$string .= chr(hexdec($hexa[$i].$hexa[$i+1]));
}

$decrypt_pass = mcrypt_decrypt(MCRYPT_DES, $key, $string, MCRYPT_MODE_ECB);
$decrypt_passVerify = mcrypt_decrypt(MCRYPT_DES, $key, $string, MCRYPT_MODE_ECB);

// handling decryption of credentials
$pass = $decrypt_pass;
$passVerify = $decrypt_passVerify;

$user = $_POST['user'];
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