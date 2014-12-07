<?php
session_start();
require_once("../lib.php");

/*error_reporting(-1);
ini_set('display_errors', 'On');*/


if (isLoggedIn()) {


	$online_users = onlineUsers(); 
	$me = get_user_info($curUser);

	// update current user's last_seen while we're here
	updateUserStatus($me['userid']);
	sleep(1);

	if ($online_users) {

		$out = '<strong>Users online:  </strong>';

		foreach ($online_users as $user) {
			$out .= userColor(ucwords($user['username']), $user['role']);
		}

	} else {
		$out = "No users are currently online.";
	}

	echo $out;

} 

?>