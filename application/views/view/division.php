<?php

if (!isset($_SESSION['secure_access']) || (isset($_SESSION['secure_access']) && $_SESSION['secure_access'] !== true)) { header("Location: /404/"); }

$out = NULL;
$platoon_items = NULL;

// extract game top-level data
$game_info = get_game_info($params['division']);
$game_id = $game_info['id'];
$game_name = $game_info['full_name'];
$short_name = $game_info['short_name'];
$game_descr = $game_info['description'];


// fetch division leaders
$divldrs = NULL;
$leaders = get_division_ldrs($game_id);
if (count($leaders)) {

	foreach($leaders as $leader) {

		$divldrs .= "
		<a href='/member/{$leader['forum_id']}' class='list-group-item'>
			<h5 class='pull-right'><i class='fa fa-shield fa-2x text-muted'></i></h5>
			<h4 class='list-group-item-heading'><strong>{$leader['rank']} {$leader['forum_name']}</strong></h4>
			<p class='list-group-item-text text-muted'>{$leader['position_desc']}</p>
		</a>";

	}

} else {
	$divldrs = "<li class='list-group-item'>No leadership currently exists for this division.</li>";
}

// generate list of existing platoons for game
$platoons = get_platoons($game_id);

foreach ($platoons as $row) {
	$number_with_suffix = ordSuffix($row['number']);
	$number = $row['number'];
	$platoon_name = $row['platoon_name'];
	$platoon_ldr = $row['abbr'] . " " . $row['forum_name'];

	$platoon_items .= "
	<a href='/divisions/{$short_name}/{$number}' class='list-group-item'>
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

if ($game_id == 2) {

	// statistics
	$toplistMonthly = null;
	$monthly = get_monthly_bf4_toplist(10);
	$i = 1;
	foreach ($monthly['players'] as $mem) {
		$toplistMonthly .= "<tr><td class='text-center text-muted'>{$i}</td><td>{$mem['rank']} {$mem['forum_name']}</td><td><strong>{$mem['aod_games']}</strong></td></tr>";
		$i++;
	}

	$toplistDaily = null;
	$daily = get_daily_bf4_toplist(10);

	$i = 1;
	foreach ($daily as $mem) {
		$toplistDaily .= "<tr data-id='{$mem['member_id']}'><td class='text-center text-muted'>{$i}</td><td>{$mem['rank']} {$mem['forum_name']}</td><td><strong>{$mem['aod_games']}</strong></td></tr>";
		$i++;
	}

	// end statistics

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
	{$breadcrumb}

	<div class='page-header'>
		<h2><strong><img src='/public/images/game_icons/large/{$shortname}.png' /> {$game_name} Division</strong></h2>
	</div>

	

	<div class='row'>

		<div class='col-md-8'>
			<div class='panel panel-primary'>
				<div class='panel-heading'>Currently Active Platoons</div>
				<div class='list-group'>
					{$platoon_list}
				</div>
			</div>
		</div>

		<div class='col-md-4'>
			<div class='panel panel-info'>
				<div class='panel-heading'>Division Command Staff</div>
				{$divldrs}
			</div>
		</div>

	</div>";

	// bf statistics
	if ($game_id == 2) {

		// statistics section
		$out .="
		<div class='row col-md-12 margin-top-50'>

			<div class='page-header'>
				<h3>Division Statistics</h3>
			</div>
		</div>

		<div class='row'>

			<div class='col-md-6'>
				<div class='panel panel-primary toplist'>

					<div class='panel-heading'>Daily Most Active Players</div>
					<table class='table table-striped table-hover'>
						{$toplistDaily}
					</table>
				</div>
			</div>

			<div class='col-md-6'>
				<div class='panel panel-primary toplist'>

					<div class='panel-heading'>Monthly Most Active Players</div>
					<table class='table table-striped table-hover'>
						{$toplistMonthly}
					</table>
				</div>
			</div>
		</div>
		"; 

	}


	$out .= "
</div>";


echo $out;


?>