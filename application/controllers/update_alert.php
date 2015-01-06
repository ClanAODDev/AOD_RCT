<?php

include "../lib.php";

if (!isset($_SESSION['secure_access']) || (isset($_SESSION['secure_access']) && $_SESSION['secure_access'] !== true)) { header("Location: /404/"); }

$data = NULL;

// values are bound and prepared in PDO
$user = $_POST['user'];
$alert = $_POST['id'];

updateAlert($user, $alert);

?>