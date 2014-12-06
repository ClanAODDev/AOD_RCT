<?php

	// eventually need to check if the current user is 
	// either an admin or is actually a part of the platoon
	// being requested, else reject request

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

$out = NULL;

var_dump($params);

if ($platoon_id = get_platoon_id_from_number($params['platoon'])[0]) {

	$platoon_info = get_platoon_info($platoon_id);
	$platoon_name = (!is_null($platoon_info['name'])) ? "Members of " . $platoon_info['name'] : "Members of Platoon " . $$params['platoon'][0];

	$right_now = new DateTime("now");
	$game_info = get_game_info($params['game']);
	$game_name = $game_info['full_name'] . " Division";

	$first_day_of_last_month = date("Y-m-d", strtotime("first day of previous month"));
	$last_day_of_last_month = date("Y-m-d", strtotime("last day of previous month"));



	$out .= "
	<div class='container margin-top-20'>
		<div class='row border-bottom'>
			<div class='col-md-6'>
				<h2><strong>{$game_name}</strong></h2>
			</div>
			<div class='col-md-6'>
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
				<h3>Demographics</h2>
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
						<div class='panel-body count-detail-big'><span class='count-animated percentage'>57</span></div>
					</div>
				</div>
			</div>

		</div>
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