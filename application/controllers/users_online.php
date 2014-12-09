<?php
session_start();
require_once("../lib.php");

if (ini_get("session.use_cookies")) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000,
		$params["path"], $params["domain"],
		$params["secure"], $params["httponly"]
		);
}

// Finally, destroy the session.
session_destroy();
die;

/*error_reporting(-1);
ini_set('display_errors', 'On');*/

if (isLoggedIn()) {

	$online_users = onlineUsers(); 
	$me = get_user_info($curUser);

	// update current user's last_seen while we're here
	updateUserStatus($me['userid']);
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