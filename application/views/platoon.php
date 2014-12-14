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
	$overall_aod_percent = array();
	$overall_aod_games = array();
	
	$breadcrumb = "
	<ul class='breadcrumb'>
		<li><a href='/'>Home</a></li>
		<li><a href='/divisions/'>Divisions</a></li>
		<li><a href='/divisions/{$params['division']}'>{$game_name}</a></li>
		<li class='active'>{$platoon_name}</li>
	</ul>
	";

	$members = get_platoon_members($platoon_id);
	$member_count = count($members);

	// build members table
	$members_table = "
	<table class='table table-striped table-hover' id='members-table'>
		<thead>
			<tr>
				<th><b>Member</b></th>
				<th class='nosearch text-center' width='90px'><b>Rank</b></th>
				<th class='nosearch text-center'><b>AOD Games</b></th>
				<th class='nosearch text-center'><b>Total Games</b></th>
				<th class='nosearch text-center'><b>Percent AOD</b></th>
				<th class='col-hidden'><b>Rank Id</b></th>
			</tr>
		</thead>
		<tbody>";

			foreach ($members as $row) {

				$total_games = count_total_games($row['member_id'], $first_day_of_last_month);
				$aod_games = count_aod_games($row['member_id'], $first_day_of_last_month);
				$percent_aod = ($aod_games > 0 ) ? (($aod_games)/($total_games))*100 : NULL;
				$percent_aod = number_format((float)$percent_aod, 2, '.', '');
				$overall_aod_games[] = $aod_games;
				$overall_aod_percent[] = $percent_aod;

				$members_table .= "
				<tr data-id='{$row['id']}'>
					<td>" . memberColor($row['forum_name'], $row['bf4_position_id']) . "</td>
					<td class='text-center'>" . $row['rank'] . "</td>
					<td class='text-center'>" . $aod_games . "</td>
					<td class='text-center'>" . $total_games . "</td>
					<td class='text-center'><div class='progress text-center follow-tool' title='{$percent_aod}%' style='width: 100px; margin: 0 auto;'><div class='progress-bar progress-bar-" . getPercentageColor($percent_aod) . " progress-bar-striped' role='progressbar' aria-valuenow='72' aria-valuemin='0' aria-valuemax='50' style='width: ". $percent_aod . "%'><span style='display: none;'>{$percent_aod}%</span></div></div></td>

					<td class='text-center'>" . $row['rank_id'] . "</td>
				</tr>
				";
			}

			$members_table .= "
		</tbody>
	</table>";


	// calculate inactives, percentage
	$min = INACTIVE_MIN;
	$max = INACTIVE_MAX;

	$inactive = array_filter(
		$overall_aod_percent,
		function ($value) use($min,$max) {
			return ($value >= $min && $value <= $max);
		})
	;

	$inactive_count = count($inactive);
	$inactive_percent = round((float)($inactive_count / $member_count) * 100 ) . '%';

	



	// calculate overall percentages
	$overall_aod_percent = array_diff($overall_aod_percent, array('0.00'));
	$overall_aod_percent = array_sum($overall_aod_percent) / count($overall_aod_percent);
	$overall_aod_games = array_sum($overall_aod_games);



	

	// build page structure
	$out .= "
	<div class='container fade-in'>
		<div class='row'>{$breadcrumb}</div>
		<div class='row'>
			<div class='col-md-12'>
				<h3>{$platoon_name} <small>". ordSuffix($platoon). " Platoon</small></h3>
			</div>
			<div class='col-xs-12 hr'><hr /></div>
		</div>";

		// show user data
		$out .= "
		<div class='row margin-top-20'>


			<div class='col-md-8'>			

				<div class='panel panel-default'>
					<!-- Default panel contents -->
					<div class='panel-heading download-area'>Platoon members   <small class='text-muted'>({$first_day_of_last_month} - {$last_day_of_last_month})</small><span></span></div>
					<div class='panel-body border-bottom'><div id='playerFilter'></div></div>
					{$members_table}
					<div class='panel-footer text-muted text-center' id='member-footer'></div>
				</div>
			</div>
			";

			$out .= "

			<div class='col-md-2'>
				<div class='panel panel-default'>
					<div class='panel-heading'>Total Members</div>
					<div class='panel-body count-detail-big striped-bg'><span class='count-animated'>{$member_count}</span></div>
				</div>
			</div>

			<div class='col-md-2'>
				<div class='panel panel-default'>
					<div class='panel-heading'>Percent Inactive</div>
					<div class='panel-body count-detail-big striped-bg follow-tool' title='{$inactive_count} out of {$member_count} with < {$max} AOD games'>
						<span class='count-animated percentage'>{$inactive_percent}</span>
					</div>
				</div>
			</div>

			<div class='col-md-4'>
				<div class='panel panel-default'>
					<div class='panel-heading'>Total AOD Games</div>
					<div class='panel-body count-detail-big striped-bg'><span class='count-animated'>{$overall_aod_games}</span></div>
				</div>

				<div class='panel panel-default'>
					<div class='panel-heading'>Percentage AOD Games</div>
					<div class='panel-body count-detail-big follow-tool striped-bg' title='Excludes all zero values'><span class='count-animated percentage'>{$overall_aod_percent}</span></div>

				</div>
			</div>

			";

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