<?php

session_start();
include "../lib.php";

// null declared values
$data = NULL;
$platoon = NULL;
$sqdldr = NULL;
$position = NULL;


// fetched values
$forumName = $_POST['fname'];
$battlelog = $_POST['blog'];
$member_id = $_POST['mid'];
$recruiter = $_POST['recruiter'];
$uid = $_POST['uid'];


// post values based on role since we can't be sure 
// a hidden form element wasn't tampered with
if ($userRole > 1 || isDev()) { $sqdldr = $_POST['squad']; $position = $_POST['position']; }
if ($userRole > 2 || isDev()) {	$platoon = $_POST['platoon']; }


// only continue if we have permission to edit the user
if (canEdit($uid) == true) {

	// attempt to fetch bf4dbid, also validates battlelog name
	// disable bf4db id check for now... allow bf3, hardline players
	if (!$bf4db = get_bf4db_id($battlelog)) {
		$bf4db = 0;
	}

	// modify the member
	$result = updateMember($uid, $forumName, $battlelog, $bf4db, $member_id, $platoon, $sqdldr, $position, $recruiter);
	$data = array('success' => $result['success'], 'message' => $result['message']);

} else {
	$data = array('success' => false, 'message' => 'You do not have permission to modify this player.');
}

// print out a pretty response
echo json_encode($data);
exit;

?>