<?php


ini_set('display_errors', 1); 
error_reporting(E_ALL);

$out = NULL;
$platoon_items = NULL;

// extract game top-level data
$game_info = get_game_info($params['division']);
$game_id = $game_info['id'];
$game_name = $game_info['full_name'];
$game_descr = $game_info['description'];

/*getDivisionLeadership($game_id);*/



// generate list of existing platoons for game
$platoons = get_platoons($game_id);

foreach ($platoons as $row) {
	$number_with_suffix = ordSuffix($row['number']);
	$number = $row['number'];
	$platoon_name = $row['platoon_name'];
	$platoon_ldr = $row['abbr'] . " " . $row['forum_name'];

	$platoon_items .= "
	<a href='/divisions/bf4/{$number}' class='list-group-item'>
	<h5 class='pull-right text-muted'>{$number_with_suffix} Plt</h5>
		<h4 class='list-group-item-heading'><strong>{$platoon_name}</strong></h4>
		<p class='list-group-item-text text-muted'>{$platoon_ldr}</p>
	</a>";
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
	<li><a href='/divisions/'>Divisions</a></li>
	<li class='active'>{$game_name}</li>
</ul>
";

// game specific data
$out .= "
<div class='container fade-in'>
	<div class='row'>{$breadcrumb}</div>
	<div class='row'>
		<div class='col-xs-12'>
			<h2><strong>{$game_name} Division</strong></h2>
		</div>

		<div class='col-xs-12 hr'><hr /></div>
	</div>

	<div class='row margin-top-20'>

		<div class='col-md-12'>
			<p>{$game_descr}</p>
		</div>

	</div>

	

	<div class='row margin-top-20'>
		<div class='col-md-6'>
			<div class='panel panel-default'>
				<div class='panel-heading'>Active Platoons</div>
				<div class='panel-body'>
					<div class='list-group'>
						{$platoon_list}
					</div></div>
				</div>
			</div>
		</div>
	</div>
	";


	echo $out;


	?>