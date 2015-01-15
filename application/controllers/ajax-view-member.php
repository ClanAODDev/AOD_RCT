<?php

session_start();
require_once("../lib.php");

if ($_POST && $_POST['id']) {
	$data['member_info'] = get_member($_POST['id']);

	if (!$data['member_info']) {
		$data['success'] = false;
		$data['message'] = "User could not be found";
	}

	$last_seen = formatTime(strtotime($data['member_info']['last_activity']));
	if (strtotime($last_seen) < strtotime('-30 days')) {
		$data['warning'] = "Player has not logged into the forums in more than 30 days!";
		$data['warningType'] = "danger";
	} else if (strtotime($last_seen) < strtotime('-14 days')) {
		$data['warning'] = "Player has not logged into the forums in more than 14 days!";
		$data['warningType'] = "warning";
	}

} else {
	$data['success'] = false;
	$data['message'] = "No data was posted.";
}

echo json_encode($data);
exit;

?>