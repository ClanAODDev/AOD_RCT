<?php

/**
 * AOD RCT Application
 * Intended to meet the initial needs
 * of the BF4 division within AOD
 **/

session_start();
ob_start();

$_SESSION['secure_access'] = true; 

include "application/lib.php";

define( 'TEMPLATES', dirname( __FILE__ ) . '/application/layouts/' );
define( 'VIEWS', dirname( __FILE__ ) . '/application/views/' );

$uri = rtrim( dirname($_SERVER["SCRIPT_NAME"]), '/' );
$uri = '/' . trim( str_replace( $uri, '', $_SERVER['REQUEST_URI'] ), '/' );
$uri = urldecode( $uri );

// update status to idle = 0
if (isLoggedIn()) { 
	if (isset($_COOKIE['active_count'])) {		
		setcookie('active_count', 0);
	}
	updateUserActivityStatus($member_info['userid'], true);
}

$rules = define_pages();

foreach ( $rules as $action => $rule ) {

	if ( preg_match( '~^'.$rule.'$~i', $uri, $params ) ) {

		if (isLoggedIn()) {

			include(TEMPLATES . "header.php");
			if((@include VIEWS . $action . ".php") === false) {
				include(TEMPLATES . "404.html");
			}
			include(TEMPLATES . "footer.php");
			exit;

		} else if (!isLoggedIn() && ($action == "register")) {

			include(TEMPLATES . "header.php");
			include(VIEWS . $action . ".php");
			include(TEMPLATES . "footer.php");
			exit;

		} else {

			include(TEMPLATES . "header.php");
			include(VIEWS . "login.php");
			include(TEMPLATES . "footer.php");
			exit;

		}
	} 
}

// if no page is found, show the 404 page
include(TEMPLATES . "header.php");
include(TEMPLATES . "404.html");
include(TEMPLATES . "footer.php");
exit;

ob_end_flush();

?>
