<?php
session_start();
require_once("../lib.php");

/*error_reporting(-1);
ini_set('display_errors', 'On');*/

if (isLoggedIn()) {

	$online_users = onlineUsers(); 
	$me = get_user_info($curUser);

	// update current user's last_seen while we're here
	updateUserActivityStatus($me['userid']);
	sleep(3);

	if ($online_users) {

		$out = 'Users online: ';
		$usersArray = array();

		foreach ($online_users as $user) {
			$icon = ($user['idle'] == 1) ? '<i class="fa fa-clock-o text-muted" title="Idle"></i> ': NULL; 
			$usersArray[] = $icon . userColor(ucwords($user['username']), $user['role']);
		}

		$users = implode(', ', $usersArray);
		$out .= $users;

	} else {
		$out = "No users are currently online.";
	}

	echo $out;

} 

?>