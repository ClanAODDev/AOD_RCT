<?php

// eventually need to check if the current user is 
// either an admin or is actually a part of the platoon
// being requested, else reject request

$out = NULL;

$platoon = $params['platoon'];
$game_info = get_game_info($params['division']);
$game_name = $game_info['full_name'] . " Division";
$game_id = $game_info['id'];


if ($platoon_id = get_platoon_id_from_number($platoon, $game_id)) {

	$platoon_info = get_platoon_info($platoon_id);
	$platoon_name = (!is_null($platoon_info['name'])) ? $platoon_info['name'] : $params['platoon'][0];

	$right_now = new DateTime("now");

	$first_day_of_last_month = date("Y-m-d", strtotime("first day of previous month"));
	$last_day_of_last_month = date("Y-m-d", strtotime("last day of previous month"));

	$members = get_platoon_members($platoon_id);
	$member_count = count($members);

	$overall_aod_percent = array();
	$overall_aod_games = array();

	$breadcrumb = "
	<ul class='breadcrumb'>
		<li><a href='/'>Home</a></li>
		<li><a href='/{$params['division']}'>{$game_name}</a></li>
		<li class='active'>{$platoon_name}</li>
	</ul>
	";


	// build members table
	$members_table = "
	<table class='table table-striped table-hover' id='members-table'>
		<thead>
			<tr>
				<th><b>Member</b></th>
				<th><b>Rank</b></th>
				<th><b>AOD Games</b></th>
				<th><b>Total Games</b></th>
				<th><b>Percent AOD</b></th>
			</tr>
		</thead>
		<tbody>";

			foreach ($members as $row) {

				$total_games = count_total_games($row['member_id'], $first_day_of_last_month);
				$aod_games = count_aod_games($row['member_id'], $first_day_of_last_month);
				$percent_aod = ($aod_games > 0 ) ? (($aod_games)/($total_games))*100 : NULL;

					// push to overall
				$overall_aod_games[] = $aod_games;
				$overall_aod_percent[] = $percent_aod;

				if ($percent_aod >= PERCENTAGE_CUTOFF_GREEN) {
					$percent_class = "success";
				} else if ($percent_aod >= PERCENTAGE_CUTOFF_AMBER) {
					$percent_class = "warning"; 
				} else {
					$percent_class = "danger"; 
				}

				$members_table .= "
				<tr>
					<td>".$row['forum_name']."</td>
					<td>".$row['abbr']."</td>
					<td>".$aod_games."</td>
					<td>".$total_games."</td>
					<td><span class='label label-{$percent_class} user-color'>".number_format((float)$percent_aod, 2, '.', '')."%</span></td>
				</tr>
				";
			}

			$members_table .= "
		</tbody>
	</table>";

	// calculate percentages
	$overall_aod_percent = array_diff($overall_aod_percent, array(NULL));
	$overall_aod_percent = array_sum($overall_aod_percent) / count($overall_aod_percent);
	$overall_aod_games = array_sum($overall_aod_games);

	

	// build page structure
	$out .= "
	<div class='container fade-in'>
		<div class='row'>{$breadcrumb}</div>

		<div class='row'>

			<div class='col-md-12'>
				<h3>Demographics</h3>
			</div>
			<div class='col-xs-12 hr'><hr /></div>
		</div>

		<div class='row margin-top-20'>
			<div class='col-md-4'>
				<div class='panel panel-primary'>
					<div class='panel-heading'>Total Members</div>
					<div class='panel-body count-detail-big'><span class='count-animated'>{$member_count}</span></div>
				</div>
			</div>


			<div class='col-md-4'>
				<div class='panel panel-primary'>
					<div class='panel-heading'>Total AOD Games</div>
					<div class='panel-body count-detail-big'><span class='count-animated'>{$overall_aod_games}</span></div>
				</div>
			</div>

			<div class='col-md-4'>
				<div class='panel panel-primary'>
					<div class='panel-heading'>Percentage AOD Games</div>
					<div class='panel-body count-detail-big follow-tool' title='Excludes all zero values'><span class='count-animated percentage'>{$overall_aod_percent}</span></div>

				</div>
			</div>

		</div><!-- end row -->

		";


			// show user data
		$out .= "
		<div class='row'>

			<div class='col-md-12'>
				<h3>{$platoon_name} <span id='playerFilter'></span></h3>
			</div>

			<div class='col-xs-12 hr'><hr /></div>

		</div>
		";

			// insert members table
		$out .= $members_table;


		$out .= "
	</div><!-- end container -->
	";

} else {

	$out .= "
	<div class='container margin-top-50'>
		<div class='row'>
			<div class='span5'>
				<div class='hero-unit center'>
					<h1>Oops</h1>
					<p>It looks like the page you were looking for does not yet exist.</p>
					<a href='/' class='btn btn-large btn-info'><i class='icon-home icon-white'></i> Take Me Home</a>
				</div>
			</div>
		</div>
	</div>

	";

}

echo $out;


?>
