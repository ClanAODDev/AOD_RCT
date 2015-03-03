<?php

if (!isset($_SESSION['secure_access']) || (isset($_SESSION['secure_access']) && $_SESSION['secure_access'] !== true)) { header("Location: /404/"); }

$out = NULL;
$editPanel = NULL;
$alerts = NULL;
$userId = $params['id'];
$maxGames = MAX_GAMES;

$first_date_in_range = date("Y-m-d", strtotime("now - 30 days"));
$last_date_in_range = date("Y-m-d", strtotime("now"));

if ($member = get_member($userId)) {

	// member data
	$game_info = get_game_info($member['game_id']);
	$short_game_name = $game_info['short_name'];
	$game_name = $game_info['full_name'];
	$game_id = $game_info['id'];
	$rank = $member['rank'];
	$name = ucwords($member['forum_name']);
	$position = (isset($member['position'])) ? $member['position'] : "Not set";
	$position_id = $member['position_id'];
	$platoon_id = $member['platoon_id'];
	$platoon_info = get_platoon_info($platoon_id);
	$platoon_name = (!is_null($platoon_info['name'])) ? $platoon_info['name'] : false;
	$platoon = get_platoon_number_from_id($platoon_id, $game_id);
	$joined = date("Y-m-d", strtotime($member['join_date']));
	$last_seen = formatTime(strtotime($member['last_activity']));
	$last_post = formatTime(strtotime($member['last_forum_post']));
	$wng_last_seen = str_replace(" ago", "", $last_seen);
	$status = $member['desc'];
	$status_id = $member['status_id'];
	$bf4dbid = $member['bf4db_id'];
	$member_id = $member['member_id'];
	$userId = $member['id'];
	$battlelog_name = $member['battlelog_name'];
	$squad_leader_id = $member['squad_leader_id'];
	
	// recruiter info
	$recruiter_id = $member['recruiter'];
	$recruiter_name = (($recruiter_id != 0) && $recruiter_name = get_forum_name($recruiter_id)) ? ucwords($recruiter_name) : "Not set or invalid";
	$recruiter = (($recruiter_name) != "Not set or invalid") ? "<a class='list-group-item text-right' href='/member/{$recruiter_id}'><span class='pull-left'><strong>Recruiter: </strong></span> <span class='text-muted'>{$recruiter_name}</span></a>" : "<li class='list-group-item text-right'><span class='pull-left'><strong>Recruiter: </strong></span> <span class='text-muted'>{$recruiter_name}</span></li>";

	// member activity greater than 14 days (warning)
	if (strtotime($last_seen) < strtotime('-30 days')) {
		$alerts .= "<div class='alert alert-danger fade-in'><i class='fa fa-exclamation-triangle'></i> Player has not logged into the forums in {$wng_last_seen}!</div>";
	} else if (strtotime($last_seen) < strtotime('-14 days')) {
		$alerts .= "<div class='alert alert-warning fade-in'><i class='fa fa-exclamation-triangle'></i> Player has not logged into the forums in {$wng_last_seen}!</div>";
	} 

	// pending member warning
	if ($status_id == 999) {
		$alerts .= "<div class='alert alert-warning fade-in'><i class='fa fa-exclamation-triangle'></i> This member is pending, and will not have any forum specific information until their member status has been approved.</div>";
	}

	// server history
	$games_list = get_player_games($member_id, $first_date_in_range, $last_date_in_range);
	$games = NULL;
	$i = 1;

	if (count($games_list)) {

		foreach ($games_list as $game) {
			$date = formatTime(strtotime($game['datetime']));
			$games .= "
			<tr>
				<td>{$game['server']}</td>
				<td class='text-muted'>{$date}</td>
			</tr>";

			if ($i == $maxGames) {
				break;
			}

			$i++;
			
		}

	} else if (is_null($bf4dbid) || empty($bf4dbid)) {
		$games = "<li class='list-group-item text-muted'>This player does not have a BF4DB id stored. You should update it.</li>";
	} else {
		$games = "<li class='list-group-item text-muted'>Either this player has no recorded games or the data sync has not yet stored any data for this player.</li>";
	}


	// build tools if user can edit member
	if (canEdit($userId) == true) {
		$editPanel .= "

		<div class='btn-group pull-right' data-member-id='{$member_id}'  data-user-id='{$userId}'>
			<button type='button' class='btn btn-default edit-member'>Edit</button>
			<button type='button' class='btn btn-default disabled'>Promote</button>
			<button type='button' class='btn btn-default disabled'>Remove</button>
		</div>

		";
	}

	// loa infp
	$loaStatus = NULL;
	$loaHideClass = NULL;
	if (($status_id == 3) && ($loaInfo = get_member_loa($member_id))) {
		$loaStatus = "<div class='panel panel-warning'><div class='panel-heading'>Leave of Absence Information</div><div class='list-group'><li class='list-group-item'><strong>Reason:</strong> <span class='pull-right'>{$loaInfo['reason']}</span></li><li class='list-group-item'><strong>Expires:</strong><span class='pull-right'> {$loaInfo['date_end']}</span></li></div></div>";
		$loaHideClass = "hidden";
	}


	// profile data
	$battlelog = (empty($battlelog_name)) ? NULL : "<a target='_blank' href='" . BATTLELOG . $battlelog_name . "' class='list-group-item'>Battlelog <span class='pull-right'><i class='text-info fa fa-external-link'></i></span></a>";
	$bf4db = (empty($bf4dbid)) ? NULL : "<a target='_blank' href='" . BF4DB . $bf4dbid . "' class='list-group-item'>BF4DB <span class='pull-right'><i class='text-info fa fa-external-link'></i></span></a>";
	$forums = "<a target='_blank' href='" . CLANAOD . $member_id . "' class='list-group-item'>AOD Forum <span class='pull-right'><i class='text-info fa fa-external-link'></i></span></a>";
	$squadleader = (isset($squad_leader_id)) ? ucwords(get_forum_name($squad_leader_id)) : NULL;
	
	// is this player a squad member, and does he have a squad leader assigned?
	$squad_leader_item = (!is_null($squadleader) && $position_id == 6) ? "<li class='list-group-item text-right'><span class='pull-left'><strong>Squad Leader: </strong></span> <span class='text-muted'>{$squadleader}</span></li>" : NULL;

	// for platoon info if it exists
	$platoon_link = ($platoon_name) ? "<li><a href='/divisions/{$short_game_name}/{$platoon}'>{$platoon_name}</a></li>" : NULL;
	$platoon_item = ($platoon_name) ? "<li class='list-group-item text-right'><span class='pull-left'><strong>Platoon: </strong></span> <span class='text-muted'>{$platoon_name}</span></li>" : NULL;

	// page breadcrumb
	$breadcrumb = "
	<ul class='breadcrumb'>
		<li><a href='/'>Home</a></li>
		<li><a href='/divisions/{$short_game_name}'>{$game_name}</a></li>
		{$platoon_link}
		<li class='active'>{$name}</li>
	</ul>
	";

	// member stuff
	$avatar = get_user_avatar($member_id, 'large');
	$privmsg = "<a class='btn btn-default btn-xs popup-link' href='" . PRIVMSG . $member_id . "' target='_blank'><i class='fa fa-comment'></i> Send PM</a>";
	$email = "<a class='btn btn-default btn-xs popup-link' href='" . EMAIL . $member_id . "' target='_blank'><i class='fa fa-envelope'></i> Send Email</a>";

	// games bar
	$count_all_games = count_total_games($member_id, $first_date_in_range, $last_date_in_range);
	$aod_games = count_aod_games($member_id, $first_date_in_range, $last_date_in_range);

	// participation bar
	$percent_aod = ($aod_games > 0 ) ? (($aod_games)/($count_all_games))*100 : NULL;
	$percent_aod = number_format((float)$percent_aod, 2, '.', '');
	$aod_bar = "<div class='progress text-center follow-tool' title='<small><center>{$aod_games} of {$count_all_games}<br />{$percent_aod}%</center></small>' style='width: 100%; margin: 0 auto; height: 30px; vertical-align:middle;'><div class='progress-bar progress-bar-" . getPercentageColor($percent_aod) . " progress-bar-striped active' role='progressbar' aria-valuenow='72' aria-valuemin='0' aria-valuemax='50' style='width: ". $percent_aod . "%'><span style='display: none;'>{$percent_aod}%</span></div></div>";
	
	// output player view
	$out .= "
	<div class='container fade-in'>
		{$breadcrumb}

		{$alerts}
		
		<div class='page-header vertical-align'>
			<div class='col-xs-1 hidden-sm hidden-xs'>{$avatar}</div>
			<div class='col-xs-7'>
				<h2><strong>{$rank} {$name}</strong><br />{$privmsg} {$email}</h2>
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
						<li class='list-group-item text-right'><span class='pull-left'><strong>Division: </strong></span> <span class='text-muted'>{$game_name}</span></li>
						{$platoon_item}
						<li class='list-group-item text-right'><span class='pull-left'><strong>Position: </strong></span> <span class='text-muted'>{$position}</span></li>
						{$squad_leader_item}
						{$recruiter}
					</ul>
				</div>

				<div class='panel panel-info'>
					<div class='panel-heading'><strong>Forum Activity</strong></div>
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
				{$loaStatus}

				<div class='panel panel-info {$loaHideClass}'>
					<div class='panel-heading'><strong>AOD Participation</strong><span class='badge pull-right'>{$aod_games} Games</span></div>
					<div class='panel-body'>{$aod_bar}</div>
				</div>

				<div class='panel panel-primary {$loaHideClass}'>
					<div class='panel-heading'><strong>BF4 Server Activity</strong> ({$count_all_games} games in 30 days)<span class='pull-right'> Last {$maxGames} games</span></div>
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
