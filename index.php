<?php

session_start();
ob_start();

/**
 * prevent direct access to views
 */
$_SESSION['secure_access'] = true; 

// link functions
include "application/lib.php";

/**
 * handle activity logging for online tracking
 */
if (isLoggedIn()) { 
	if (isset($_COOKIE['active_count'])) {		
		setcookie('active_count', 0);
	}
	updateUserActivityStatus($member_info['userid'], true);
}

/**
 * routing system
 */

$rules = routing();

foreach ( $rules as $action => $rule ) {

	if ( preg_match( '~^'.$rule.'$~i', $uri, $params ) ) {

		$path_parts = explode("/", $action);

		// handle stats requests differently
		if (!isLoggedIn() && isset($path_parts[0]) && $path_parts[0] == "stats") {

			include(VIEWS . $action . ".php");
			exit;

		} else if (!isLoggedIn() && isset($path_parts[1]) && $path_parts[1] == "register") {

			include(TEMPLATES . "header.php");
			include(VIEWS . $action . ".php");
			include(TEMPLATES . "footer.php");
			exit;

		} else if (isLoggedIn()) {

			include(TEMPLATES . "header.php");

			if ((@include VIEWS . $action . ".php") === false) {
				include(TEMPLATES . "404.php");
			}

			include(TEMPLATES . "footer.php");
			exit;

		} else {

			include(TEMPLATES . "header.php");
			include(VIEWS . "user/login.php");
			include(TEMPLATES . "footer.php");
			exit;

		}
	} 
}

// if no page is found, show the 404 page
include(TEMPLATES . "header.php");
include(TEMPLATES . "404.php");
include(TEMPLATES . "footer.php");
exit;

ob_end_flush();

?>
