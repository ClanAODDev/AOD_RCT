<?php

include('../lib.php');
$out = NULL;

if (isset($_GET['name'])) {
	$results = get_member_name($_GET['name']);

	if (empty($results)) {

		$out .= "<li class='text-muted list-group-item'>No results found.</li";

	} else if (empty($_GET['name'])) {

		return false;

	} else {

		foreach($results as $row) {
			$name = $row['forum_name'];
			$id = $row['id'];
			$rank = $row['abbr'];
			$game = $row['game_name'];
			$out .= "<a href='/member/{$id}' class='list-group-item'>{$rank} {$name}<span class='text-muted pull-right'>{$game}</span></a>";	
		} 
		
	}
	echo $out;
}
