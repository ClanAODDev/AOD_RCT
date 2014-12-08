<?php


ini_set('display_errors', 1); 
error_reporting(E_ALL);


$out = NULL;
$platoon_items = NULL;

// extract game top-level data
$game_info = get_game_info($params['division']);
$game_id = $game_info['id'];
$game_name = $game_info['full_name'] . " Division";
$game_descr = $game_info['description'];



// generate list of existing platoons for game
$platoons = get_platoons($game_id);

foreach ($platoons as $row) {
	$platoon_items .= "<li class='list-group-item'><a href='/bf4/platoon/{$row['number']}''>".$row['number'].". ".$row['name']."</a></li>";
}

if (!empty($platoon_items)) {

	$platoon_list = "
	<ul class='list-group'>
		{$platoon_items}
	</ul>";

} else {

	$platoon_list = "No platoons currently exist for this division.";

}

	$breadcrumb = "
	<ul class='breadcrumb'>
	<li><a href='/'>Home</a></li>
	<li class='active'>{$game_name}</li>
	</ul>
	";

// game specific data
$out .= "
<div class='container fade-in'>
<div class='row'>{$breadcrumb}</div>
	<div class='row'>
		<div class='col-xs-12'>
			<h2><strong>{$game_name}</strong></h2>
		</div>

		<div class='col-xs-12 hr'><hr /></div>
	</div>

	<div class='row margin-top-20'>

		<div class='col-md-12'>
			<p>{$game_descr}</p>
		</div>

	</div>

	<div class='row'>
		<div class='col-xs-8'>
			<h3>Platoons</h3>
		</div>

		<div class='col-xs-8 hr'><hr /></div>
	</div>

	<div class='row margin-top-20'>

		<div class='col-md-8'>
			{$platoon_list}
		</div>

	</div>
</div>
";


echo $out;

?>