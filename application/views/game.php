<?php

$out = NULL;

var_dump($params);
die;

if (!is_null($gameid)) {


	$info = get_game_info($gameid);

	$out .= "
	<div class='container fade-in '>
		<div class='row'>
			<h3>". $info['full_name']. "</h3>
			<p>Filler stuff</p>
		</div>
		<div class='row'>

		</div>
	</div>
	";
}

echo $out;

?>