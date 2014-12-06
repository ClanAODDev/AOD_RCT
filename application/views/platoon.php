<?php

// eventually need to check if the current user is 
// either an admin or is actually a part of the platoon
// being requested, else reject request

$out = NULL;
$members_table = NULL;

$platoon = $params['platoon'];

if ($platoon_id = get_platoon_id_from_number($platoon)) {

	$platoon_info = get_platoon_info($platoon_id);
	$platoon_name = (!is_null($platoon_info['name'])) ? "Members of <strong>" . $platoon_info['name'] . "</strong>" : "Members of Platoon <strong>" . $params['platoon'][0]. "</strong>";

	$right_now = new DateTime("now");
	$game_info = get_game_info($params['game']);
	$game_name = $game_info['full_name'] . " Division";

	$first_day_of_last_month = date("Y-m-d", strtotime("first day of previous month"));
	$last_day_of_last_month = date("Y-m-d", strtotime("last day of previous month"));

	$members = get_platoon_members($platoon_id);
	$overall_aod_percent = array();

	foreach ($members as $row) {

		$total_games = count_total_games($row['member_id'],$first_day_of_last_month);
		$aod_games = count_aod_games($row['member_id'],$first_day_of_last_month);
		$percent_aod = (($aod_games)/($total_games))*100;

		// push to overall
		$overall_aod_percent[] = $percent_aod;

		if ($percent_aod <= 50) { 
			$percent_class = "danger"; 
		} else if ($percent_aod <= 75) { 
			$percent_class = "warning"; 
		} else {
			$percent_class = NULL;
		}

		$members_table .= "
		<tr>
			<td>".$row['forum_name']."</td>
			<td>".$row['abbr']."</td>
			<td>".$aod_games."</td>
			<td>".$total_games."</td>
			<td class='{$percent_class}'>".number_format((float)$percent_aod, 2, '.', '')."%</td>
		</tr>
		";
	}

	$members_table .= "</table>";


 	$overall_aod_percent = array_sum($overall_aod_percent) / count($overall_aod_percent);


	// build page structure

	$out .= "
	<div class='container margin-top-20'>
		<div class='row border-bottom'>

			<div class='col-xs-6'>
				<h2><strong>{$game_name}</strong></h2>
			</div>

			<div class='col-xs-6'>
				<h2 class='pull-right'><small>{$platoon_name}</small></h2>
			</div>

		</div>

		<div class='row margin-top-20'>

			<div class='col-md-12'>
				<p>{$game_info['description']}</p>
			</div>

		</div>

		<div class='row border-bottom'>

			<div class='col-md-12'>
				<h3>Demographics</h3>
			</div>

		</div>

		<div class='row margin-top-20'>
			<div class='col-md-4'>
				<div class='panel panel-info'>
					<div class='panel-heading'>Total Members</div>
					<div class='panel-body count-detail-big'><span class='count-animated'>98276</span></div>
				</div>
			</div>


			<div class='col-md-4'>
				<div class='panel panel-info'>
					<div class='panel-heading'>Total AOD Games</div>
					<div class='panel-body count-detail-big'><span class='count-animated'>456</span></div>
				</div>
			</div>

			<div class='col-md-4'>
				<div class='panel panel-info'>
					<div class='panel-heading'>Percentage AOD Games</div>
					<div class='panel-body count-detail-big'><span class='count-animated percentage'>{$overall_aod_percent}</span></div>
				</div>
			</div>

		</div><!-- end row -->

		";


		// show user data
		$out .= "
		<div class='row border-bottom'>

			<div class='col-md-12'>
				<h3>Platoon Members</h3>
			</div>

		</div>
		";

		$out .= "<table class='table table-striped table-hover table-condensed margin-top-20'>
		<tr>
			<td><b>Member</b></td>
			<td><b>Rank</b></td>
			<td><b>AOD Games</b></td>
			<td><b>Total Games</b></td>
			<td><b>Percent AOD</b></td>
		</tr>";

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
