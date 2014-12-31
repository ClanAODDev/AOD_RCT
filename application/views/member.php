<?php
$out = NULL;

// SELECT member.id, rank.abbr as rank, forum_name, member_id, battlelog_name, bf4db_id, rank_id,  platoon_id, bf4_position_id, squad_leader_id, status_id, game_id, join_date, last_forum_login, last_activity, last_forum_post, forum_posts 

if ($member = get_member($params['id'])) {

	$rank = $member['rank'];
	$name = ucwords($member['forum_name']);

	$game_info = get_game_info($member['game_id']);
	$short_game_name = $game_info['short_name'];
	$game_name = $game_info['full_name'];
	$game_id = $game_info['id'];

	$platoon_id = $member['platoon_id'];
	$platoon_info = get_platoon_info($platoon_id);
	$platoon_name = (!is_null($platoon_info['name'])) ? $platoon_info['name'] : false;
	$platoon = get_platoon_number_from_id($platoon_id, $game_id);



	// server history
	$past_games = get_player_games($member['member_id']);
	$games = NULL;
	foreach ($past_games as $game) {
		$date = formatTime(strtotime($game['datetime']));
		$games .= "
		<tr>
			<td>{$game['server']}</td>
			<td class='text-muted'>{$date}</td>
		</tr>";
	}

	// member data
	$joined = date("Y-m-d", strtotime($member['join_date']));
	$last_seen = formatTime(strtotime($member['last_activity']));
	$last_post = formatTime(strtotime($member['last_forum_post']));
	$status = $member['desc'];

	// profile data
	$battlelog = (empty($member['battlelog_name'])) ? "N/A" : BATTLELOG . $member['battlelog_name'];
	$forums = (empty($member['member_id'])) ? "N/A" : CLANAOD . $member['member_id'];
	$bf4db = (empty($member['bf4db_id'])) ? "N/A" : BF4DB . $member['bf4db_id'];

	$platoon_link = ($platoon_name) ? "<li><a href='/divisions/{$short_game_name}/{$platoon}'>{$platoon_name}</a></li>" : NULL;

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
		<div class='row page-header'>
			<div class='col-md-10'>
				<h1><strong>{$rank} {$name}</strong></h1>
			</div>
			<div class='col-md-2'><span class='pull-right'>{$avatar}</span>

			</div>
		</div>
		<br>
		<div class='row'>
			<div class='col-md-3'>
				<div class='panel panel-info'>
					<div class='panel-heading'><strong>Member Information</strong></div>
					<ul class='list-group'>
						<li class='list-group-item text-right'><span class='pull-left'><strong>Status: </strong></span> <span class='text-muted'>{$status}</span></li>
						<li class='list-group-item text-right'><span class='pull-left'><strong>Divisions: </strong></span> <span class='text-muted'>{$game_name}</span></li>


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
					<div class='panel-heading'><strong>Gaming Profiles</strong></div>
					<a target='_blank' href='{$battlelog}' class='list-group-item'>BattleLog <span class='pull-right'><i class='text-info fa fa-external-link'></i></span></a>
					<a target='_blank' href='{$bf4db}' class='list-group-item'>BF4DB <span class='pull-right'><i class='text-info fa fa-external-link'></i></span></a>
					<a target='_blank' href='{$forums}' class='list-group-item'>AOD Forum <span class='pull-right'><i class='text-info fa fa-external-link'></i></span></a>
				</div>

			</div>
			<!--/end left side bar-->




			<div class='col-md-9'>

				<div class='panel panel-default'>
					<div class='panel-heading'>Starfox221's Bio</div>
					<div class='panel-body'> A long description about me.
					</div>
				</div>
				
				<div class='panel panel-default'>
					<div class='panel-heading'>Recent server activity</div>
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