<?php

include('../lib.php');

$out = NULL;

if (isset($_GET['name'])) {
	$results = search_name($_GET['name']);

	if (empty($results)) {

		$out .= "<li class='text-muted list-group-item'>No results found.</li";

	} else if (empty($_GET['name'])) {

		return false;

	} else {

		foreach($results as $row) {
			$name = ucwords($row['forum_name']);
			$id = $row['member_id'];
			$rank = $row['abbr'];
			$game = $row['game_name'];
			$out .= "
			<a href='/member/{$id}' class='list-group-item'>
				<strong>{$rank} {$name}</strong>
			</a>";	
		} 

	}
	echo $out;
}
