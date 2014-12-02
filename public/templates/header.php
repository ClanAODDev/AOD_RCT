<?php

session_start('aod_rct');

$player = (isset($_GET['player']) && (!empty($_GET['player']))) ? $_GET['player'] : NULL;
$gameid = (isset($_GET['game']) && (!empty($_GET['game']))) ? $_GET['game'] : NULL;

?>

<html>
<head>
	<title>AOD | Squad Management</title>

	<meta name="viewport" content="width=device-width, initial-scale=.0, maximum-scale=1.0, user-scalable=no">
	
	<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="public/css/style.css">		
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>	

</head>
<body>
	<div id="wrap">
		<div class="push-top"></div>



