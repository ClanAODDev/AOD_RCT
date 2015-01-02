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


// fetch division leaders
$leaders = get_division_ldrs($game_id);
if ($leaders[0] && $leaders[1]) {
	$co = $leaders[0]['abbr'] . " " . $leaders[0]['forum_name'];
	$co_id = $leaders[0]['id'];
	$xo = $leaders[1]['abbr'] . " " . $leaders[1]['forum_name'];
	$xo_id = $leaders[1]['id'];

	$leaders = "
	<a href='/member/{$co_id}' class='list-group-item'>
		<h5 class='pull-right'><i class='fa fa-shield fa-2x text-muted'></i></h5>
		<h4 class='list-group-item-heading'><strong>{$co}</strong></h4>
		<p class='list-group-item-text text-muted'>Division Commander</p>
	</a>
	<a href='/member/{$xo_id}' class='list-group-item'>
		<h5 class='pull-right'><i class='fa fa-shield fa-2x text-muted'></i></h5>
		<h4 class='list-group-item-heading'><strong>{$xo}</strong></h4>
		<p class='list-group-item-text text-muted'>Division Executive Officer</p>
	</a>
	";
} else {
	$leaders = "<li class='list-group-item'>No leadership currently exists for this division.</li>";
}



// generate list of existing platoons for game
$platoons = get_platoons($game_id);

foreach ($platoons as $row) {
	$number_with_suffix = ordSuffix($row['number']);
	$number = $row['number'];
	$platoon_name = $row['platoon_name'];
	$platoon_ldr = $row['abbr'] . " " . $row['forum_name'];

	$platoon_items .= "
	<a href='/divisions/bf4/{$number}' class='list-group-item'>
		<h5 class='pull-right text-muted'>{$number_with_suffix} Platoon</h5>
		<h4 class='list-group-item-heading'><strong>{$platoon_name}</strong></h4>
		<p class='list-group-item-text text-muted'>{$platoon_ldr}</p>
	</a>";
}

if (!empty($platoon_items)) {

	$platoon_list = $platoon_items;

} else {

	$platoon_list = "<li class='list-group-item'>No platoons currently exist for this division.</li>";

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

		<div class='page-header'>
			<h2><strong>{$game_name} Division</strong></h2>
		</div>

	

	<div class='row'>

		<div class='col-md-8'>
			<div class='panel panel-primary'>
				<div class='panel-heading'>Active Platoons</div>
				<div class='list-group'>
					{$platoon_list}
				</div>
			</div>
		</div>

		<div class='col-md-4'>
			<div class='panel panel-info'>
				<div class='panel-heading'>Division Command Staff</div>
				{$leaders}
			</div>
		</div>

	</div>";


	// statistics section
	

	$out .="
	<div class='row'>
		<div class='col-md-12 page-header'>
			<h3>Division Statistics</h3>
		</div>
	</div>

	<div class='row'>

		<div class='col-md-4'>
			<div class='panel panel-default'>
				<div class='panel-heading'>Active Platoons</div>
				<div class='list-group'>
					{$platoon_list}
				</div>
			</div>
		</div>

		<div class='col-md-4'>
			<div class='panel panel-default'>
				<div class='panel-heading'>Active Platoons</div>
				<div class='list-group'>
					{$platoon_list}
				</div>
			</div>
		</div>

		<div class='col-md-4'>
			<div class='panel panel-default'>
				<div class='panel-heading'>Active Platoons</div>
				<div class='list-group'>
					{$platoon_list}
				</div>
			</div>
		</div> 

	</div>

</div>



";


echo $out;


?>