<?php

include "../lib.php";
$data = NULL;

// handle squad leader updates
if ($_POST['trans'] == 's') {

	$forumName = $_POST['fname'];
	$battlelog = $_POST['blog'];
	$member_id = $_POST['mid'];
	$uid = $_POST['uid'];

	// need to check user permissions here to ensure user can edit selected member
	// not terribly pertinent since form visibility is controlled

	// attempt to fetch bf4dbid, also validates battlelog name
	if (!$bf4db = get_bf4db_id($battlelog)) {
		$data = array('success' => false, 'message' => 'Invalid battlelog name.', 'battlelog' => false);
	} else {
		$result = updateMember($uid, $forumName, $battlelog, $bf4db, $member_id);
		$data = array('success' => $result['success'], 'message' => $result['message']);
	}

}

echo json_encode($data);

?>