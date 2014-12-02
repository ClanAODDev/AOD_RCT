<?php

/**
 * AOD RCT Application
 * Intended to meet the initial needs
 * of the BF4 division within AOD
 **/

include "../config.php";
include "application/lib.php";

define( 'TEMPLATES', dirname( __FILE__ ) . '/public/templates/' );
define( 'VIEWS', dirname( __FILE__ ) . '/application/views/' );

$uri = rtrim( dirname($_SERVER["SCRIPT_NAME"]), '/' );
$uri = '/' . trim( str_replace( $uri, '', $_SERVER['REQUEST_URI'] ), '/' );
$uri = urldecode( $uri );


$rules = define_pages();

if (isLoggedIn()) {

	foreach ( $rules as $action => $rule ) {
		if ( preg_match( '~^'.$rule.'$~i', $uri, $params ) ) {
			include(TEMPLATES . "header.php");
			include(TEMPLATES . "navigation.php");
			include(VIEWS . $action . ".php");
			include(TEMPLATES . "footer.php");
			exit;
		}
	}

	// if no page is found, show the 404 page
	include(TEMPLATES . "header.php");
	include(TEMPLATES . "navigation.php");
	include(TEMPLATES . "404.html");
	include(TEMPLATES . "footer.php");
	exit;

} else {
	
	include(TEMPLATES . "header.php");
	include(TEMPLATES . "navigation.php");
	include(VIEWS . "login.php");
	include(TEMPLATES . "footer.php");
	exit;
}


?>
