<?php

include "../lib.php";

$data = NULL;

// values are bound and prepared in PDO
$user = $_POST['user'];
$alert = $_POST['id'];

if (isset($user) && isset($alert)) {
	updateAlert($alert, $user);
} else {
	echo "User or alert id not specified.";
}

?>