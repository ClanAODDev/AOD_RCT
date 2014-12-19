<?php

$out = NULL;
$my_squad = NULL;


/**
 * only available for squad leaders
 */
if ($member_info['bf4_position_id'] == 5) {
	$squad_members = get_my_squad($forumId);

	if (count($squad_members)) {
		foreach ($squad_members as $squad_member) {
			$name = ucwords($squad_member['forum_name']);
			$id = $squad_member['id'];
			$rank = $squad_member['rank'];
			$last_seen = formatTime(strtotime($squad_member['last_activity']));

			$my_squad .= "
			<a href='/member/{$id}' class='list-group-item'><strong>{$rank} {$name}</strong><small class='pull-right text-muted'>{$last_seen}</small></a>
			";
		}
	} else {
		$my_squad .= "<p>Unfortunately it looks like you don't have any squad members! Do you need to <a href='/manage/squad/'>Add Members</a> to your squad?</p>";
	}

} 



// fetch posts for main page
$postsArray = get_posts("main_page");
$posts = NULL;

if (!empty($postsArray)) {
	foreach ($postsArray as $post) {
		$title = $post['title'];
		$authorId = $post['user'];
		$content = htmlspecialchars_decode($post['content']);
		$authorAva = get_user_avatar($post['member_id']). "<span style='margin-right: 15px;'></span>";
		$authorName = userColor($post['username'], $post['role']);
		$date = formatTime(strtotime($post['date']));
		$posts .= "
		<div class='panel panel-default'>
			<div class='panel-heading'>{$authorAva} {$title}<span class='pull-right text-muted'>Posted {$date} by <a href='/member/{$authorId}'>{$authorName}</a></span></div>
			<div class='panel-body'>{$content}</div>
		</div>";
	}
} else {
	$posts .= '<p>There are no posts to display.</p>';
}







// begin container
$out .= "
<div class='container fade-in margin-top-20'>";


	if ($userRole == 0) {
		$out .= "
		<div class='alert alert-warning' role='alert'><i class=\"fa fa-exclamation-triangle\"></i> You are currently a guest. You will need to have an administrator approve your account before you can use this application</div>";
	}


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

	// output alerts
	// should remain at the top of the homepage, but below jumbo
	$out .= "{$alerts_list}";


	$out .= "
	<div class='row'>

		";

		$out .= "
		<div class='col-md-5'>

			<div class='list-group'>

				<a href='#' class='list-group-item'>
					<h4 class='pull-right text-success'><i class='fa fa-plus-square fa-lg'></i></h4>
					<h4 class='list-group-item-heading'><strong>Add new recruit</strong></h4>
					<p class='list-group-item-text'>Start the recruiting process with a brand new candidate</p>
				</a>

				<a href='#' class='list-group-item'>
					<h4 class='pull-right text-success'><i class='fa fa-wrench fa-lg'></i></h4>
					<h4 class='list-group-item-heading'><strong>Manage your squad</strong></h4>
					<p class='list-group-item-text'>Promote, demote, or kick members of your squad</p>
				</a>

				<a href='#' class='list-group-item'>
					<h4 class='pull-right text-success'><i class='fa fa-user fa-lg'></i></h4>
					<h4 class='list-group-item-heading'><strong>Add an existing member</strong></h4>
					<p class='list-group-item-text'>Add an existing member of AOD to your squad or platoon</p>
				</a>

				<a href='#' class='list-group-item disabled'>
					<h4 class='pull-right'><i class='fa fa-flag-checkered text-muted fa-lg'></i></h4>
					<h4 class='list-group-item-heading'><strong>Review inactive members</strong></h4>
					<p class='list-group-item-text'>View inactive members and flag for removal</p>
				</a>

			</div>

			";



			// show data depending on role

			if (!is_null($my_squad)) {

				$out .= "
				<!-- if a squad leader -->

				<div class='panel panel-primary'>
					<div class='panel-heading'><strong> Your Squad</strong><span class='pull-right'>Last seen</span></div>

					<div class='list-group'>
						{$my_squad}

					</div>
				</div>

				<!-- end squad -->
			</div>
			";
		}



		// show main page posts

		$out .= "
		<div class='col-md-7'>
			{$posts}
		</div>";





		// end row
		$out .= "
	</div>";





	// end container
	$out .=" 
</div>
";


echo $out;



?>