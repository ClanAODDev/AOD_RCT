<?php
session_start();
require_once("../lib.php");

if (isLoggedIn()) {

	setcookie('aod_rct_active_count', $_COOKIE['aod_rct_active_count'] + 1, time() + (86400 * 30), '/');
	updateUserActivityStatus($member_info['userid']);

	// time to catch up
	sleep(2);
	$online_users = onlineUsers(); 

	if ($online_users) {

		$out = 'Users online: ';
		$usersArray = array();
		$icon = '<i class="fa fa-clock-o text-muted" title="Idle"></i>';

		foreach ($online_users as $user) {
			$userString = userColor(ucwords($user['username']), $user['role']);
			$string = ($user['idle'] == 1) ? $icon . ' <span class="text-muted">' . $userString . '</span>' : $userString;
			$usersArray[] = $string;
		}

		$users = implode(', ', $usersArray);
		$out .= $users;

	} else {
		$out = "No users are currently online.";
	}

	echo $out;

} 

?>