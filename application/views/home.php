<?php

if (!isset($_SESSION['secure_access']) || (isset($_SESSION['secure_access']) && $_SESSION['secure_access'] !== true)) { header("Location: /404/"); }

$out = NULL;
$my_squad = NULL;
$my_platoon = NULL;


/**
 * alter home view based on user role
 */

// squad leader personnel view
if ($userRole == 1) {
	$squad_members = get_my_squad($forumId);
	$squadCount = ($squad_members) ? "(" . count($squad_members) . ")" : NULL;
	if ($squad_members) {
		foreach ($squad_members as $squad_member) {
			$name = ucwords($squad_member['forum_name']);
			$id = $squad_member['member_id'];
			$rank = $squad_member['rank'];
			$last_seen = formatTime(strtotime($squad_member['last_activity']));

			// visual cue for inactive squad members
			if (strtotime($last_seen) < strtotime('-30 days')) {
				$status = 'danger';
			} else if (strtotime($last_seen) < strtotime('-14 days')) {
				$status = 'warning';
			} else {
				$status = 'muted';
			}

			$my_squad .= "
			<a href='/member/{$id}' class='list-group-item'><input type='checkbox' data-id='{$id}' style='margin-right: 10px; display: none;'>{$rank} {$name}<small class='pull-right text-{$status}'>{$last_seen}</small></a>
			";
		}
	} else {
		$my_squad .= "<div class='panel-body'>Unfortunately it looks like you don't have any squad members!</div>";
	}


// platoon leader personnel view
} else if ($userRole == 2) {
	$squad_leaders = get_squad_leaders($user_game, $user_platoon);
	$platoonCount = ($squad_leaders) ? "(" . count(get_platoon_members($user_platoon)) . ")" : NULL;

	$i = 1;

	if ($platoonCount) {

		foreach ($squad_leaders as $squad_leader) {

			$rank = $squad_leader['rank'];
			$name = ucwords($squad_leader['name']);
			$squad_members = get_my_squad($squad_leader['member_id']);
			$last_seen = formatTime(strtotime($squad_leader['last_activity']));
			$status = lastSeenColored($last_seen);
			$squadCount = count($squad_members);

			$my_platoon .= "
			<a href='#collapseSquad{$i}' data-toggle='collapse' class='list-group-item active accordion-toggle' data-parent='#squads'>{$rank} {$name} ({$squadCount})</a>
			<div class='squad-group collapse' id='collapseSquad{$i}'>";

				foreach ($squad_members as $squad_member) {
					$rank = $squad_member['rank'];
					$id = $squad_member['member_id'];
					$name = ucwords($squad_member['forum_name']);
					$last_seen = formatTime(strtotime($squad_member['last_activity']));
					$status = lastSeenColored($last_seen);

					$my_platoon .= "<a href='/member/{$id}' class='list-group-item'><input type='checkbox' data-id='{$id}' class='pm-checkbox'><span class=' member-item'>{$rank} {$name}</span><small class='pull-right text-{$status}'>{$last_seen}</small></a>";
				}

				$my_platoon .= "</div>";
				$i++;

			}


			// add general population to list items
			$gen_pop = get_gen_pop($user_platoon);
			$genPopCount = count($gen_pop);
			$my_platoon .= "
			<a href='#collapseSquad{$i}' data-toggle='collapse' class='list-group-item active accordion-toggle' data-parent='#squads'>General Population ({$genPopCount})</a>
			<div class='squad-group collapse' id='collapseSquad{$i}'>";

				foreach ($gen_pop as $gen_member) {
					$rank = $gen_member['rank'];
					$id = $gen_member['member_id'];
					$name = ucwords($gen_member['forum_name']);
					$last_seen = formatTime(strtotime($gen_member['last_activity']));
					$status = lastSeenColored($last_seen);

					$my_platoon .= "<a href='/member/{$id}' class='list-group-item'><input type='checkbox' data-id='{$id}' class='pm-checkbox'><span class='member-item'>{$rank} {$name}</span><small class='pull-right text-{$status}'>{$last_seen}</small></a>";
				}
				$my_platoon .= "</div>";


			} else {
				$my_platoon .= "<div class='panel-body'>Unfortunately it looks like you don't have any platoon members!</div>";
			}

		}



		// fetch announcements for main page
		$postsArray = get_posts("main_page", 3, $userRole);
		$posts = NULL;

		if (!empty($postsArray)) {
			foreach ($postsArray as $post) {
				$title = $post['title'];
				$authorId = $post['member_id']; 
				$content = htmlspecialchars_decode($post['content']);
				$authorAva = get_user_avatar($post['forum_id']); 
				$authorName = $post['username'];
				$date = formatTime(strtotime($post['date']));
				$posts .= "
				<div class='panel panel-default'>
					<div class='panel-heading'>{$authorAva} {$title}</div>
					<div class='panel-body'>{$content}</div>
					<div class='panel-footer text-muted text-right'>
						<small>Posted {$date} by <a href='/member/{$authorId}'>{$authorName}</a></small>
					</div>
				</div>";
			}
		} else {
			$posts .= '<p>There are no posts to display.</p>';
		}



		// fetch tools based on role
		$toolsArray = build_user_tools($userRole);
		$tools = NULL;
		$roleName = getUserRoleName($userRole);

		if (count($toolsArray)) {
			foreach($toolsArray as $tool) {
				$disabled = ($tool['disabled']) ? 'disabled' : NULL;
				$tools .= "
				<a href='{$tool['link']}' class='list-group-item {$tool['class']} {$disabled}'>
					<h4 class='pull-right text-muted'><i class='fa fa-{$tool['icon']} fa-lg'></i></h4>
					<h4 class='list-group-item-heading'><strong>{$tool['title']}</strong></h4>
					<p class='list-group-item-text text-muted'>{$tool['descr']}</p>
				</a>";
			}

		} else {
			$tools .= "<li class='list-group-item'>No tools currently available to you</li>";
		}



/**
 * start page structure
 */

$out .= "
<div class='container fade-in margin-top-20'>";


	// tour jumbo tron
	$out .="
	<div class='row tour-intro'>
		<div class='col-md-12'>
			<div class='jumbotron striped-bg'>
				<h1>{$welcomeWord}, <strong>{$curUser}</strong>!</h1>
				<p>This is the activity tracker for the {$longname} division! Visit the help section for more information.</p>
				<p><a class='btn btn-primary btn-lg' href='/help'' role='button'>Learn more <i class='fa fa-arrow-right'></i></a></p>
			</div>
		</div> <!-- end col -->
	</div> <!-- end end row -->";

	// alerts section
	$out .= "
	<div class='row'>
		<div class='col-md-12'>
			{$obligAlerts}
			{$alerts_list}
			
		</div>
	</div>";



	// division list
	$out .= "
	<div class='row'>
		<div class='col-md-12'>
			<div class='panel panel-default'>
				<div class='panel-heading'><i class='fa fa-gamepad fa-lg pull-right text-muted'></i> <strong>Gaming Divisions</strong></div>
				<div class='list-group'>
					{$main_game_list}
				</div>
			</div>
		</div>
	</div>";


	// is user a regular member, no editing privileges?
	if ($userRole == 0) {
		$out .= "
		<div class='panel panel-info'>
			<div class='panel-heading'>Welcome to the activity tracker!</div>
			<div class='panel-body'>
				<p>As a clan member, you have access to see the activity data for all members within the clan, so long as your particular division is supported by this tool. To get started, select your division from the \"divisions\" dropdown above.</p>
				<p>To view a particular member, simply type their name in the search box above.</p>
			</div>
		</div>

		{$posts}";


	} else {


		// left side
		$out .= "
		<div class='row'>";


			// start leader tools
			$out .= "
			<div class='col-md-5'>
				<div class='panel panel-primary'>
					<div class='panel-heading'><strong>{$roleName} Quick Tools</strong></div>
					<div class='list-group'>
						{$tools}
					</div>
				</div>
				";



				if ($userRole == 1) {

					// squad
					$out .= "
					<div class='panel panel-default'>
						<div class='panel-heading'><strong> Your Squad</strong> {$squadCount}<span class='pull-right text-muted'>Last seen</span></div>

						<div class='list-group' id='squad'>
							{$my_squad}

						</div>
						<div class='panel-footer'><button id='pm-checked' class='btn btn-success btn-sm toggle-pm pull-right' style='display: none;'>Send PM (<span class='count-pm'>0</span>)</button>  <button class='btn btn-default btn-sm toggle-pm pull-right'>PM MODE</button><div class='clearfix'></div></div>
					</div>";

				} else if ($userRole == 2) {

					// platoon
					$out .= "				
					<div class='panel panel-default'>
						<div class='panel-heading'><strong> Your Platoon</strong> {$platoonCount}<span class='pull-right text-muted'>Last seen</span></div>

						<div class='list-group' id='squads'>

							{$my_platoon}

						</div>
						<div class='panel-footer'><button id='pm-checked' class='btn btn-success btn-sm toggle-pm pull-right' style='display: none;'>Send PM (<span class='count-pm'>0</span>)</button>  <button class='btn btn-default btn-sm toggle-pm pull-right'>PM MODE</button><div class='clearfix'></div></div>
					</div>";

				}

				$out .= "
			</div>";


			// announcements
			$out .= "
			<div class='col-md-7'>
				{$posts}
			</div>
			";

			$out .="
		</div>";
		// end announcements

	}

	// end container
	$out .=" 
</div>";


echo $out;

?>