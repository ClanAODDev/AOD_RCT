<?php
session_start();
require_once("../lib.php");

error_reporting(-1);
ini_set('display_errors', 'On');

if (isLoggedIn()) {

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

	// sleep(2);
	echo $out;

} 

?>