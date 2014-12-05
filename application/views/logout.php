<?php

if (isLoggedIn()) {

	session_destroy();
	header('Location: /aod_rct');

} else {

	header('Location: /aod_rct');
}

?>

