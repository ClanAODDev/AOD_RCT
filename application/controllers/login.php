<?php

include "../lib.php";

$forum = "http://www.clanaod.net/forums/";
$vb = new vBForumFunctions($forum);
$data = NULL;

$user = $_POST['user'];
$pass = $_POST['password'];

/*if(!$vb->login($user, $pass)) {
	$data['success'] = false;
	$data['message'] = 'Login information was incorrect';
} else {
	session_start();
	$data['success'] = true;
	$_SESSION['loggedIn'] = true;
	$_SESSION['username'] = $user;
}*/

sleep(2);

echo json_encode($data);
exit;

?>