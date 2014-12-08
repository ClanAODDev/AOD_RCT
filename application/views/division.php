<?php

$out = NULL;

$game_info = get_game_info($params['game']);
$game_name = $game_info['full_name'] . " Division";



// game specific data

$out .= "
<div class='container fade-in '>
	<div class='row'>
			<div class='col-xs-12'>
				<h2><strong>{$game_name}</strong></h2>
			</div>

			<div class='col-xs-12 hr'><hr /></div>
		</div>

		<div class='row margin-top-20'>

			<div class='col-md-12'>
				<p>{$game_info['description']}</p>
			</div>

		</div>
</div>
";
}

echo $out;

?>