<?php

include "../lib.php";

$forum = "http://www.clanaod.net/forums/";
$vb = new vBForumFunctions($forum);

if ($_POST) {

	// may want to consider sanitizing, but since we aren't recording yet... TODO
	$user = $_POST['user'];
	$pass = $_POST['password'];

	// validate credentials, ensure user can login
	if(!$vb->login($user, $pass)) {

		$data['success'] = false;
		$data['message'] = 'Login information was incorrect';

	} else {

		// not providing a message because there's no error to report
		$data['success'] = true;
	}

} 

echo json_encode($data);

?>