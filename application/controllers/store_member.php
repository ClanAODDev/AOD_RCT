<?php

session_start();

$data = NULL;

// some user based values defined in lib (user_game, user_platoon, forumId)
include("../lib.php");

$forumName = trim($_POST['name']);
$battlelog = trim($_POST['battlelog']);
$member_id = trim($_POST['member_id']);

// fetch values if appropriate role, else use own values
$squadLdr = ($userRole >= 2 || isDev()) ? $_POST['squadLdr'] : $forumId;
$platoon = ($userRole >= 3 || isDev()) ? $_POST['platoon'] : $user_platoon;

// if user not squad leader, squad set to 0, then position is gen pop
// else squad member
$bf4_position_id = ($squadLdr == 0 && ($userRole >= 2 || isDev()) ) ? 7 : 6;

// attempt to fetch bf4dbid, also validates battlelog name
if (!$bf4db = get_bf4db_id($battlelog)) {
	$data = array('success' => false, 'message' => 'Invalid battlelog name.', 'battlelog' => true);
} else if (memberExists($member_id) == true) {
	$data = array('success' => false, 'message' => 'Member already exists. Check forum id.', 'memberExists' => true);
} else if (createMember($forumName, $member_id, $battlelog, $bf4db, $platoon, $bf4_position_id, $squadLdr, $user_game)) {
	$data = array('success' => true, 'message' => 'Member entry created');
} else {
	$data = array('success' => false, 'message' => 'Something went wrong...');
}

echo json_encode($data);

?>

