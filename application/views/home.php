<?php

$out = NULL;
$my_squad = NULL;


/**
 * use this to show information dependent on 
 * user role? 
 * squad leader -> squad
 * platoon leader -> squad leaders
 * division co -> platoon leaders
 */

$squad_members = get_my_squad($forumId);
$squadCount = ($squad_members) ? "(" . count($squad_members) . ")" : NULL;


if ($squad_members) {
	foreach ($squad_members as $squad_member) {
		$name = ucwords($squad_member['forum_name']);
		$id = $squad_member['id'];
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
		<a href='/member/{$id}' class='list-group-item'>{$rank} {$name}<small class='pull-right text-{$status}'>{$last_seen}</small></a>
		";
	}
} else {
	$my_squad .= "<div class='panel-body'>Unfortunately it looks like you don't have any squad members! Do you need to <a href='/manage/squad/'>Add Members</a> to your squad?</div>";
}

// fetch announcements for main page
$postsArray = get_posts("main_page", 5);
$posts = NULL;

if (!empty($postsArray)) {
	foreach ($postsArray as $post) {
		$title = $post['title'];
		$authorId = $post['id'];  // needs member id
		$content = htmlspecialchars_decode($post['content']);
		$authorAva = get_user_avatar($post['forum_id']);  // needs forum id
		$authorName = $post['username'];
		$date = formatTime(strtotime($post['date']));
		$posts .= "
		<div class='panel panel-default'>
			<div class='panel-heading'>{$authorAva} {$title}</div>
			<div class='panel-body'>{$content}</div>
			<div class='panel-footer'>
				<small class='text-muted'>Posted {$date} by <a href='/member/{$authorId}'>{$authorName}</a></small>
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
		$icon = $tool['icon'];
		$class = $tool['class'];
		$title = $tool['title'];
		$descr = $tool['descr'];
		$link = $tool['link'];

		$tools .= "
		<a href='{$link}' class='list-group-item {$class}'>
			<h4 class='pull-right text-success'><i class='fa fa-{$icon} fa-lg'></i></h4>
			<h4 class='list-group-item-heading'><strong>{$title}</strong></h4>
			<p class='list-group-item-text'>{$descr}</p>
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
				<h1>Howdy <strong>{$curUser}</strong>!</h1>
				<p>This is the squad administration tool for the AOD organization. Leaders will be able to manage individual squad members, view activity trends of their respective divisions, and clan leaders will be able to see activity clan-wide and across divisions.</p><p><a class='btn btn-success btn-lg' role='button'>Give me a tour</a> <a class='btn btn-default btn-lg hide-tour' role='button'>Don't show again</a></p>
			</div>
		</div> <!-- end col -->
	</div> <!-- end end row -->";

	// alerts section
	$out .= "
	<div class='row'>
		<div class='col-md-12'>
			{$alerts_list}
		</div>
	</div>";

	// is user approved?
	if ($userRole == 0) {
		$out .= "
		<div class='alert alert-warning' role='alert'><i class=\"fa fa-exclamation-triangle\"></i> You are currently a guest. You will need to have an administrator approve your account before you can use this application</div>";
	} else {

		// player search bar
		$out .= "
		<div class='row'>
			<div class='col-md-12'>
				<div class='panel panel-primary'>
					<div class='panel-heading'><i class='fa fa-search'></i> <strong>Player Search</strong></div>
					<div class='panel-body'>
						<input type='text' class='form-control input-lg' name='member-search' id='member-search' placeholder='Type a player name' />
						<div id='member-search-results' class='scroll'></div> 
					</div>
				</div>
			</div>
		</div>
		";

		$out .= "
		<div class='row'>";

			// start leader tools
			$out .= "

			<div class='col-md-5'>
				<div class='panel panel-default'>
					<div class='panel-heading'><strong>{$roleName} Quick Tools</strong></div>
					<div class='list-group'>
						{$tools}
					</div>
				</div>
				";

				$out .= "
				<div class='panel panel-default'>
					<div class='panel-heading'><strong> Your Squad</strong> {$squadCount}<span class='pull-right text-muted'>Last seen</span></div>

					<div class='list-group'>
						{$my_squad}

					</div>
				</div>
				";


				$out .= "
			</div>";
			// end leader tools and info column


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