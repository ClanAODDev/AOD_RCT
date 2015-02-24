<?php

if (!isset($_SESSION['secure_access']) || (isset($_SESSION['secure_access']) && $_SESSION['secure_access'] !== true)) { header("Location: /404/"); }

$out = NULL;
$platoonPm = array();

$platoon = $params['platoon'];
$game_info = get_game_info($params['division']);
$game_name = $game_info['full_name'];
$game_id = $game_info['id'];


if ($platoon_id = get_platoon_id_from_number($platoon, $game_id)) {

	$platoon_info = get_platoon_info($platoon_id);
	$platoon_name = (!is_null($platoon_info['name'])) ? $platoon_info['name'] : $params['platoon'][0];
	$right_now = new DateTime("now");


	$first_date_in_range = date("Y-m-d", strtotime("now - 30 days"));
	$last_date_in_range = date("Y-m-d", strtotime("now"));

	$overall_aod_percent = array();
	$overall_aod_games = array();
	
	$breadcrumb = "
	<ul class='breadcrumb'>
		<li><a href='/'>Home</a></li>
		<li><a href='/divisions/{$params['division']}'>{$game_name}</a></li>
		<li class='active'>{$platoon_name}</li>
	</ul>
	";

	$members = get_platoon_members($platoon_id);
	$member_count = count($members);

	// build members table
	$members_table = "
	<div class='table-responsive'>
		<table class='table table-striped table-hover' id='members-table'>
			<thead>
				<tr>
					<th><b>Member</b></th>
					<th class='nosearch text-center hidden-xs hidden-sm'><b>Rank</b></th>
					<th class='text-center hidden-xs hidden-sm'><b>Joined</b></th>
					<th class='text-center'><b>Last Active</b></th>
					<th class='text-center tool' title='In AOD servers'><b>AOD</b></th>
					<th class='text-center'><b>Overall</b></th>
					<th class='nosearch text-center follow-tool' title='Percent Played on AOD Servers'><b>%</b></th>
					<th class='col-hidden'><b>Rank Id</b></th>
					<th class='col-hidden'><b>Last Login Date</b></th>
				</tr>
			</thead>
			<tbody>";

				foreach ($members as $row) {

					$platoonPm[] = $row['member_id'];
					$total_games = count_total_games($row['member_id'], $first_date_in_range, $last_date_in_range);
					$aod_games = count_aod_games($row['member_id'], $first_date_in_range, $last_date_in_range);
					$percent_aod = ($aod_games > 0 ) ? (($aod_games)/($total_games))*100 : NULL;
					$percent_aod = number_format((float)$percent_aod, 2, '.', '');
					$overall_aod_games[] = $aod_games;
					$overall_aod_percent[] = $percent_aod;
					$rank = $row['rank'];
					$joindate = date("Y", strtotime($row['join_date']));
					$lastActive = formatTime(strtotime($row['last_activity']));
					$status = lastSeenColored($lastActive);

					$members_table .= "
					<tr data-id='{$row['id']}'>
						<td>" . memberColor(ucwords($row['forum_name']), $row['position_id']) . "</td>
						<td class='text-center hidden-xs hidden-sm'>{$rank}</td>
						
						<td class='text-center hidden-xs hidden-sm'>{$joindate}</td>
						<td class='text-center text-{$status}'>{$lastActive}</td>
						
						<td class='text-center'>{$aod_games}</td>
						<td class='text-center'>{$total_games}</td>

						<td class='text-center'><div class='progress text-center follow-tool' title='<small><center>{$aod_games} of {$total_games}<br />{$percent_aod}%</center></small>' style='width: 100%; margin: 0 auto; height: 15px; vertical-align:middle;'><div class='progress-bar progress-bar-" . getPercentageColor($percent_aod) . " progress-bar-striped' role='progressbar' aria-valuenow='72' aria-valuemin='0' aria-valuemax='50' style='width: ". $percent_aod . "%'><span style='display: none;'>{$percent_aod}%</span></div></div></td>

						<td class='text-center col-hidden'>" . $row['rank_id'] . "</td>
						<td class='text-center col-hidden'>" . $row['last_activity'] . "</td>
					</tr>
					";
				}

				$members_table .= "
			</tbody>
		</table>
	</div>";

	// calculate inactives, percentage
	$min = INACTIVE_MIN;
	$max = INACTIVE_MAX;


	$inactive = array_filter(
		$overall_aod_games,
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

	// platoon pm array
	$platoonPm = implode("&u[]=", $platoonPm);


	// build tools if user can edit member
	if ($userRole >= 2) {
		$editPanel .= "

		<div class='btn-group pull-right'>

			<button type='button' class='btn btn-default disabled'>Edit</button>
			<a class='btn btn-default popup-link' href='http://www.clanaod.net/forums/private.php?do=newpm&amp;u[]={$platoonPm}' target='_blank'><i class='fa fa-comment'></i> Send Platoon PM</a>
		</div>

		";
	}


	// build page structure
	$out .= "
	<div class='container fade-in'>
		<div class='row'>{$breadcrumb}</div>
		<div class='row page-header'>
			<div class='col-xs-7 platoon-name'>
				<h2><img src='/public/images/game_icons/large/{$shortname}.png' /> <strong>{$platoon_name}</strong> <small class='platoon-number'>". ordSuffix($platoon). " Platoon</small></h2>
			</div>
			<div class='col-xs-5'>
				{$editPanel}
			</div>

		</div>";



		// members data table
		$out .= "
		<div class='row'>
			<div class='col-md-3 hidden-xs'>
				<div class='panel panel-default'>
					<div class='panel-heading'>Total Members</div>
					<div class='panel-body count-detail-big striped-bg'><span class='count-animated'>{$member_count}</span>
					</div>
				</div>

				<div class='panel panel-default'>
					<div class='panel-heading'>Percentage AOD Games</div>
					<div class='panel-body count-detail-big follow-tool striped-bg' title='Excludes all zero values'><span class='count-animated percentage'>{$overall_aod_percent}</span>
					</div>
				</div>

				<!--<div class='panel panel-default'>
				<div class='panel-heading'>Game Inactivity</div>
				<div class='panel-body count-detail-big striped-bg follow-tool' title='{$inactive_count} out of {$member_count} with < {$max} AOD games'>
					<span class='count-animated percentage'>{$inactive_percent}</span>
				</div>
			</div>-->
		</div>


		<div class='col-md-9'>			

			<div class='panel panel-default'>
				<!-- Default panel contents -->
				<div class='panel-heading'><div class='download-area hidden-xs'></div>Platoon members (last 30 days)<span></span></div>
				<div class='panel-body border-bottom'><div id='playerFilter'></div>
			</div> 
			{$members_table}
			<div class='panel-footer text-muted text-center' id='member-footer'></div>
		</div>
	</div>

	";

		// end container
	$out .= "
</div>
";

} else {

	// platoon does not exist for specified game
	// redirect to 404
	header('Location: /404/');

}

echo $out;

?>