<?php
$out = NULL;


// SELECT member.id, rank.abbr as rank, forum_name, member_id, battlelog_name, bf4db_id, rank_id,  platoon_id, bf4_position_id, squad_leader_id, status_id, game_id, join_date, last_forum_login, last_activity, last_forum_post, forum_posts 

if ($member = get_member($params['id'])) {

	$rank = $member['rank'];
	$name = ucwords($member['forum_name']);

	$platoon = $member['platoon_id'];
	$game_info = get_game_info($member['game_id']);
	$short_name = $game_info['short_name'];
	$game_name = $game_info['full_name'];
	$game_id = $game_info['id'];

	// member data9
	$joined = date("Y-m-d", strtotime($member['join_date']));
	$last_seen = formatTime(strtotime($member['last_activity']));
	$last_post = formatTime(strtotime($member['last_forum_post']));
	$status = $member['desc'];

	// profile data
	$battlelog = (empty($member['battlelog_name'])) ? "N/A" : BATTLELOG . $member['battlelog_name'];
	$forums = (empty($member['member_id'])) ? "N/A" : CLANAOD . $member['member_id'];
	$bf4db = (empty($member['bf4db_id'])) ? "N/A" : BF4DB . $member['bf4db_id'];


	$breadcrumb = "
	<ul class='breadcrumb'>
		<li><a href='/'>Home</a></li>
		<li><a href='/divisions/{$short_name}'>{$game_name}</a></li>
		<li class='active'>{$name}</li>
	</ul>
	";

	$avatar = get_user_avatar($member['member_id'], 'large');

	$out .= "

	<div class='container'>
		{$breadcrumb}
		<div class='row page-header'>
			<div class='col-sm-10'>
				<h1><strong>{$rank} {$name}</strong></h1>
			</div>
			<div class='col-sm-2'><span class='pull-right'>{$avatar}</span>

			</div>
		</div>
		<br>
		<div class='row'>
			<div class='col-sm-3'>
				<div class='panel panel-default'>
					<div class='panel-heading'>Member Information</div>
					<ul class='list-group'>
						<li class='list-group-item text-right'><span class='pull-left'><strong class=''>Status: </strong></span> {$status}</li>
						<li class='list-group-item text-right'><span class='pull-left'><strong class=''>Joined:</strong></span> {$joined}</li>
						<li class='list-group-item text-right'><span class='pull-left'><strong class=''>Last seen:</strong></span> {$last_seen}</li>
						<li class='list-group-item text-right'><span class='pull-left'><strong class=''>Last posted:</strong></span>  {$last_post}</li>
						
					</ul>
				</div>



				<div class='panel panel-default'>
					<div class='panel-heading'>Gaming Profiles</div>
					<a target='_blank' href='{$battlelog}' class='list-group-item'>BattleLog <span class='pull-right'><i class='text-info fa fa-external-link'></i></span></a>
					<a target='_blank' href='{$bf4db}' class='list-group-item'>BF4DB <span class='pull-right'><i class='text-info fa fa-external-link'></i></span></a>
					<a target='_blank' href='{$forums}' class='list-group-item'>AOD Forum <span class='pull-right'><i class='text-info fa fa-external-link'></i></span></a>
				</div>

				<ul class='list-group'>
					<li class='list-group-item text-muted'>Activity <i class='fa fa-dashboard fa-1x'></i>

					</li>
					<li class='list-group-item text-right'><span class='pull-left'><strong class=''>Shares</strong></span> 125</li>
					<li class='list-group-item text-right'><span class='pull-left'><strong class=''>Likes</strong></span> 13</li>
					<li class='list-group-item text-right'><span class='pull-left'><strong class=''>Posts</strong></span> 37</li>
					<li class='list-group-item text-right'><span class='pull-left'><strong class=''>Followers</strong></span> 78</li>
				</ul>





				<div class='panel panel-default'>
					<div class='panel-heading'>Social Media</div>
					<div class='panel-body'>	<i class='fa fa-facebook fa-2x'></i>  <i class='fa fa-github fa-2x'></i> 
						<i class='fa fa-twitter fa-2x'></i> <i class='fa fa-pinterest fa-2x'></i>  <i class='fa fa-google-plus fa-2x'></i>

					</div>
				</div>
			</div>
			<!--/col-3-->
			<div class='col-sm-9' contenteditable='false' style=''>
				<div class='panel panel-default'>
					<div class='panel-heading'>Starfox221's Bio</div>
					<div class='panel-body'> A long description about me.

					</div>
				</div>
				<div class='panel panel-default target'>
					<div class='panel-heading' contenteditable='false'>Pets I Own</div>
					<div class='panel-body'>
						<div class='row'>
							<div class='col-md-4'>
								<div class='thumbnail'>
									<img alt='300x200' src='http://lorempixel.com/600/200/people'>
									<div class='caption'>
										<h3>
											Rover
										</h3>
										<p>
											Cocker Spaniel who loves treats.
										</p>
										<p>

										</p>
									</div>
								</div>
							</div>
							<div class='col-md-4'>
								<div class='thumbnail'>
									<img alt='300x200' src='http://lorempixel.com/600/200/city'>
									<div class='caption'>
										<h3>
											Marmaduke
										</h3>
										<p>
											Is just another friendly dog.
										</p>
										<p>

										</p>
									</div>
								</div>
							</div>
							<div class='col-md-4'>
								<div class='thumbnail'>
									<img alt='300x200' src='http://lorempixel.com/600/200/sports'>
									<div class='caption'>
										<h3>
											Rocky
										</h3>
										<p>
											Loves catnip and naps. Not fond of children.
										</p>
										<p>

										</p>
									</div>
								</div>

							</div>

						</div>

					</div>

				</div>
				<div class='panel panel-default'>
					<div class='panel-heading'>Battlefield 4 Activity</div>
					<div class='panel-body'> A long description about me.

					</div>
				</div>
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