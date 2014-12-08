<?php

// Unset all of the session variables.
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000,
		$params["path"], $params["domain"],
		$params["secure"], $params["httponly"]
		);
}

// Finally, destroy the session.
session_destroy();
?>

<div class='container fade-in margin-top-20'>
	<div class='row'>
		<div class='col-md-12'>
		<h1>Logged out</strong>!</h1>
			<p>Your session has been destroyed.</p>
		</div> <!-- end col -->
	</div> <!-- end end row -->
</div>


