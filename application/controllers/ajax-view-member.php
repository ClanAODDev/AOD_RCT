<?php

session_start();
require_once("../lib.php");

if ($_POST && $_POST['id']) {
	$data['member_info'] = get_member($_POST['id']);

	if (!$data['member_info']) {
		$data['success'] = false;
		$data['message'] = "User could not be found";
	}

} else {
	$data['success'] = false;
	$data['message'] = "No data was posted.";
}

echo json_encode($data);
exit;

?>