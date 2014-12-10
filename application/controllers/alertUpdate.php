<?php

include "../lib.php";

$data = NULL;

// values are bound and prepared in PDO
$user = $_POST['user'];
$alert = $_POST['id'];

updateAlert($user, $alert);

?>