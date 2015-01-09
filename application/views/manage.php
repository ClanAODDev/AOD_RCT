<?php

session_start();
include("../lib.php");

$out = NULL;

if ($params['form'] == "squad") {
	
$content = "";

} else if ($params['form'] == "platoon") {
	
$content = "";

} else if ($params['form'] == "division") {
	
$content = "";

} else {

	header("Location: /404/");
	exit;

}

$out .= "
<div class='container'>
{$content}
</div>";

echo $out;

?>