<?php

session_start();
include "../lib.php";

if (isset($_POST['id']) && isset($_POST['action'])){

	// fetched values
	$action = $_POST['action'];
	$uid = $_POST['id'];
	$lid = $forumId;

	// only continue if we have permission to edit the user
	if ($action == 1) {
		$result = updateFlagged($uid, $lid, 1);
		$data = array('success' => $result['success'], 'message' => $result['message']);
	} else {
		$result = updateFlagged($uid, $lid, 0);
		$data = array('success' => $result['success'], 'message' => $result['message']);
	}

} else {
	$data = array('success' => false, 'message' => 'You do not have permission to modify this player.');
}

// print out a pretty response
echo json_encode($data);
exit;

?>