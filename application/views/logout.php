<?php

if (isLoggedIn()) {

	session_destroy();
	header('Location: /');

} else {

	header('Location: /');
}

?>

