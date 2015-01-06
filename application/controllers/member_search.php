<?php

include('../lib.php');

if (!isset($_SESSION['secure_access']) || (isset($_SESSION['secure_access']) && $_SESSION['secure_access'] !== true)) { header("Location: /404/"); }

$out = NULL;

if (isset($_GET['name'])) {
	$results = get_member_name($_GET['name']);

	if (empty($results)) {

		$out .= "<li class='text-muted list-group-item'>No results found.</li";

	} else if (empty($_GET['name'])) {

		return false;

	} else {

		foreach($results as $row) {
			$name = ucwords($row['forum_name']);
			$id = $row['id'];
			$rank = $row['abbr'];
			$game = $row['game_name'];
			$out .= "
			<a href='/member/{$id}' class='list-group-item'>
				<strong>{$rank} {$name}</strong>
				<span class='text-muted pull-right'>{$game}</span>
			</a>";	
		} 

	}
	echo $out;
}
