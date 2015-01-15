<?php

if (!isset($_SESSION['secure_access']) || (isset($_SESSION['secure_access']) && $_SESSION['secure_access'] !== true)) { header("Location: /404/"); }

$out = NULL;
$editPanel = NULL;

if ($member = get_member($params['id'])) {

	$game_info = get_game_info($member['game_id']);
	$short_game_name = $game_info['short_name'];
	$game_name = $game_info['full_name'];
	$game_id = $game_info['id'];

	$rank = $member['rank'];
	$name = ucwords($member['forum_name']);
	$position = $member['position'];

	$platoon_id = $member['platoon_id'];
	$platoon_info = get_platoon_info($platoon_id);
	$platoon_name = (!is_null($platoon_info['name'])) ? $platoon_info['name'] : false;
	$platoon = get_platoon_number_from_id($platoon_id, $game_id);

	// server history
	$past_games = get_player_games($member['member_id']);
	$games = NULL;
	if (count($past_games)) {
		foreach ($past_games as $game) {
			$date = formatTime(strtotime($game['datetime']));
			$games .= "
			<tr>
				<td>{$game['server']}</td>
				<td class='text-muted'>{$date}</td>
			</tr>";
		}
	} else if (is_null($member['bf4db_id']) || empty($member['bf4db_id'])) {
		$games = "<li class='list-group-item text-muted'>This player does not have a BF4DB id stored. You should update it.</li>";
	} else {
		$games = "<li class='list-group-item text-muted'>Either this player has no recorded games or the data sync has not yet stored any data for this player.</li>";
	}

	// member data
	$joined = date("Y-m-d", strtotime($member['join_date']));
	$last_seen = formatTime(strtotime($member['last_activity']));
	$last_post = formatTime(strtotime($member['last_forum_post']));
	$status = $member['desc'];

// canEdit($member['member_id'])

	// build tools
	if (1==1) {
		$editPanel .= "
		

		<div class='btn-group pull-right'>
			<button type='button' class='btn btn-default text-center'>Edit</button>
			<button type='button' class='btn btn-default text-center'>Promote</button>
			<button type='button' class='btn btn-danger text-center'>Remove</button>
		</div>

		
		";
	}





	// profile data
	$battlelog = (empty($member['battlelog_name'])) ? NULL : "<a target='_blank' href='" . BATTLELOG . $member['battlelog_name'] . "' class='list-group-item'>Battlelog <span class='pull-right'><i class='text-info fa fa-external-link'></i></span></a>";
	
	$bf4db = (empty($member['bf4db_id'])) ? NULL : "<a target='_blank' href='" . BF4DB . $member['bf4db_id'] . "' class='list-group-item'>BF4DB <span class='pull-right'><i class='text-info fa fa-external-link'></i></span></a>";

	$forums = "<a target='_blank' href='" . CLANAOD . $member['member_id'] . "' class='list-group-item'>AOD Forum <span class='pull-right'><i class='text-info fa fa-external-link'></i></span></a>";


	// for platoon info if it exists
	$platoon_link = ($platoon_name) ? "<li><a href='/divisions/{$short_game_name}/{$platoon}'>{$platoon_name}</a></li>" : NULL;
	$platoon_item = ($platoon_name) ? "<li class='list-group-item text-right'><span class='pull-left'><strong>Platoon: </strong></span> <span class='text-muted'>{$platoon_name}</span></li>" : NULL;


	$breadcrumb = "
	<ul class='breadcrumb'>
		<li><a href='/'>Home</a></li>
		<li><a href='/divisions'>Divisions</a></li>
		<li><a href='/divisions/{$short_game_name}'>{$game_name}</a></li>
		{$platoon_link}
		<li class='active'>{$name}</li>
	</ul>
	";

	$avatar = get_user_avatar($member['member_id'], 'large');

	$out .= "
	<div class='container'>
		{$breadcrumb}
		
		<div class='page-header vertical-align'>
			<div class='col-xs-1 hidden-sm hidden-xs'>{$avatar}</div>
			<div class='col-xs-7'>
				<h2><strong>{$rank} {$name}</strong></h2>
			</div>			
			<div class='col-xs-4'>
				{$editPanel}
			</div>
		</div>


		<div class='row margin-top-20'>
			<div class='col-md-3'>

				<div class='panel panel-info'>
					<div class='panel-heading'><strong>Member Information</strong></div>
					<ul class='list-group'>
						<li class='list-group-item text-right'><span class='pull-left'><strong>Status: </strong></span> <span class='text-muted'>{$status}</span></li>
						<li class='list-group-item text-right'><span class='pull-left'><strong>Divisions: </strong></span> <span class='text-muted'>{$game_name}</span></li>
						{$platoon_item}


					</ul>
				</div>

				<div class='panel panel-info'>
					<div class='panel-heading'><strong>Activity</strong></div>
					<ul class='list-group'>
						<li class='list-group-item text-right'><span class='pull-left'><strong class=''>Joined:</strong></span> <span class='text-muted'>{$joined}</span></li>
						<li class='list-group-item text-right'><span class='pull-left'><strong class=''>Last seen:</strong></span> <span class='text-muted'>{$last_seen}</span></li>
						<li class='list-group-item text-right'><span class='pull-left'><strong class=''>Last posted:</strong></span>  <span class='text-muted'>{$last_post}</span></li>
					</ul>
				</div>

				<div class='panel panel-info'>
					<div class='panel-heading'>
						<strong>Gaming Profiles</strong>
					</div>
					{$forums}
					{$battlelog}
					{$bf4db}
				</div>

			</div>
			<!--/end left side bar-->

			<div class='col-md-9'>
				<div class='panel panel-primary'>
					<div class='panel-heading'><strong>Recent server activity</strong><small class='pull-right'>Last 30 days</small></div>
					<table class='table table-striped table-hover'>
						<tbody>
							{$games}
						</tbody>
					</table>

				</div>
			</div>
		</div>
	</div>
	";


} else {
	// member not found
	header('Location: /404/');
}


echo $out;

?>