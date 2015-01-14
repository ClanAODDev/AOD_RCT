<?php

session_start();
include("../lib.php");


$out = NULL;

// build dropdown for status
$statusOptions = NULL;
$statuses = get_statuses();
foreach($statuses as $status) {
	$statusOptions.= "<option value='{$status['id']}'>{$status['desc']}</option>";
}

// build dropdown for position
$posOptions = NULL;
$positions = get_positions();
foreach($positions as $position) {
	$posOptions.= "<option value='{$position['id']}'>{$position['desc']}</option>";
}


// only show to squad leaders
if ($params['form'] == "squad" && $userRole == 1) {

	$my_squad = NULL;
	$squad_members = get_my_squad($forumId);
	$squadCount = ($squad_members) ? count($squad_members) : NULL;
	$squad_member_ids = array();

	if ($squad_members) {

		foreach ($squad_members as $squad_member) {
			$name = ucwords($squad_member['forum_name']);
			$id = $squad_member['id'];
			$forum_id = $squad_member['member_id'];
			$rank = $squad_member['rank'];
			$last_seen = formatTime(strtotime($squad_member['last_activity']));
			$squad_member_ids[$name] = $forum_id;


			$my_squad .= "
			<a href='#' class='member list-group-item' data-member-id='{$forum_id}' data-user-id='{$id}'>
				{$rank} {$name}<div class='pull-right box' style='display: none;'><i class='fa fa-check text-success'></i></div>
			</a>
			";
		}
	} else {
		$my_squad .= "<div class='panel-body'>Unfortunately it looks like you don't have any squad members! Do you need to <a href='/manage/squad/'>Add Members</a> to your squad?</div>";
	}

	$pm_squad_url = "http://www.clanaod.net/forums/private.php?do=newpm&u[]=" . implode($squad_member_ids, "&u[]=");




	$content = "



	<div role='tabpanel'>

		<!-- Nav tabs -->
		<ul class='nav nav-tabs' role='tablist'>
			<li role='presentation' class='active'><a href='#manage' aria-controls='manage' role='tab' data-toggle='tab'>Manage Squad</a></li>
			<li role='presentation' class='disabled'><a href='#inactivity' aria-controls='inactivity' role='tab' data-toggle='tab' disabled>Inactivity Report</a></li>

		</ul>

		<!-- Tab panes -->
		<div class='tab-content'>
			<div role='tabpanel' class='tab-pane fade in active' id='manage'>


				<div class='row margin-top-20'>
					<div class='col-md-5'>
						<div class='panel panel-primary'>
							<div class='panel-heading'><strong> Your Squad</strong> <span class='pull-right'>{$squadCount} Members</span></div>

							<table class='table table-hover table-striped'>
								{$my_squad}

							</table> 
						</div>
					</div>

					<div class='col-md-7'>
						<div class='panel panel-info data-box'>
							<div class='panel-heading'>Member Data</div>
							<div class='panel-body'>
								<p class='loading text-center' style='display: none;'><img src='/public/images/loading.gif' /></p>
								<p class='intro'>Select the member you wish to make changes to or manage.</p>
								<div class='edit-form' style='display: none;'>

									<form id='edit-form-squad'>

										<input type='hidden' id='uid' name='uid' />

										<div class='form-group'>
											<label for='forum_name'>Forum Name</label>
											<input type='text' class='form-control' id='forum_name'>
										</div>

										<div class='form-group'>
											<label for='member_id'>Forum ID</label>
											<input type='number' class='form-control' id='member_id'>
										</div>

										<div class='form-group battlelog-group'>
											<label for='battlelog' class='control-label'>Battlelog Name</label>
											<input type='text' class='form-control' id='battlelog'>
										</div>

										<div class='message alert' style='display: none;'></div>

										<button type='submit' class='text-right btn btn-success'>Save Info</button>

									</form>

								</div>

							</div>
							<div class='panel-footer actions-box' style='display: none;'>
								<small class='text-muted'><strong>Note: </strong>Battlelog name must be valid. It is needed to look up the player's BF4DB id.</small>
							</div>
						</div>

					</div>

				</div>


			</div>
			<div role='tabpanel' class='tab-pane fade' id='inactivity'>...</div>

		</div>

	</div>






	";

// only show to platoon leaders
} else if ($params['form'] == "platoon" && $userRole == 2) {

	// add ability to pm specific members in a platoon
	// possibly a member search with select to PM

	$content = "";

// only show to division commanders, executive officers
} else if ($params['form'] == "division" && $userRole == 3) {

	$content = "";

} else {

	header("Location: /404/");
	exit;

}



$page = ucwords($params['form']);

$breadcrumb = "
<ul class='breadcrumb'>
	<li><a href='/'>Home</a></li>
	<li class='active'>Manage {$page}</li>
</ul>
";

$out .= "
<div class='container fade-in'>

	{$breadcrumb}

	<div class='alert-box'></div>

	<div class='page-header'>
		<h1><strong>Manage</strong> <small>My {$page}</small></h1>
	</div>

	{$content}

</div>

<script src='/public/js/manage.js'></script>";

echo $out;

?>