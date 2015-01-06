<?php

session_start();
require_once("../lib.php");


if (isset($_SESSION['loggedIn'])) {

	if (isLoggedIn()) {
		if (isset($_COOKIE['active_count'])) {
			setcookie('active_count', $_COOKIE['active_count'] + 1, time() + (86400 * 30), '/');
		} else {
			setcookie('active_count', 0, time() + (86400 * 30), '/');
		}

		updateUserActivityStatus($member_info['userid']);
		$online_users = onlineUsers(); 

		if ($online_users) {

			$out = 'Users online: ';
			$usersArray = array();

			foreach ($online_users as $user) {
				$string = ($user['idle'] == 1) ? userColor(ucwords($user['username']), 99) : userColor(ucwords($user['username']), $user['role']);
				$usersArray[] = $string;
			}

			$users = implode(', ', $usersArray);
			$out .= $users;

		} else {
			$out = "No users are currently online.";
		}
	} 
} else {
	$out = "No active session.";
}

echo $out;

?>