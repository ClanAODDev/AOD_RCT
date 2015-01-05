<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');


$out = NULL;
$platoons = NULL;
$squadLeaders = NULL;

$game_info = get_game_info($member_info['game_id']);
$short_game_name = $game_info['short_name'];
$game_name = $game_info['full_name'];
$game_id = $game_info['id'];




/**
 * is user a squad leader or platoon leader
 * if not, they won't have any platoon information
 * ---
 * role is also checked on form submit to prevent abuse
 * - if squad leader -> any value provided is ignored
 * - value will be based on user's assingment
 */

switch ($userRole) {
	case 1:
	$platoon_info = get_platoon_info($member_info['platoon_id']);
	$allowPltAssignmentEdit = false;
	$allowSqdAssignmentEdit = false;
	break;

	case 2:
	$platoon_info = get_platoon_info($member_info['platoon_id']);
	$allowPltAssignmentEdit = false;
	$allowSqdAssignmentEdit = true;
	break;

	case 3:
	$allowPltAssignmentEdit = true;
	$allowSqdAssignmentEdit = true;
	break;
}

// allow developers to see all fields regardless of role
if (isDev()) {
	$allowPltAssignmentEdit = true;
	$allowSqdAssignmentEdit = true;
}

// if assignment editing is allowed, show fields
$assignmentPltFieldDisplay = ($allowPltAssignmentEdit) ? "block" : "none";
$assignmentSqdFieldDisplay = ($allowSqdAssignmentEdit) ? "block" : "none";

// if platoon leader, provide platoon id for squad leader search
$platoon_id = (($userRole == 2) && (!isDev())) ? $member_info['platoon_id'] : false;

// platoons and squads are based on game, for clarity
$platoonArray = get_platoons($game_id);
$squadleadersArray = get_squad_leaders($game_id, $platoon_id);

// build platoons
if (count($platoonArray)) {
	foreach($platoonArray as $platoon) {
		$platoons .= "<option value='{$platoon['platoon_id']}'>{$platoon['platoon_name']}</option>";
	}
} else {
	$platoons = "<option>No platoons exist.</option>";
}

// build squad leaders
if (count($squadleadersArray)) {
	foreach($squadleadersArray as $squadLeader) {
		$squadLeaders .= "<option value='{$squadLeader['member_id']}'>{$squadLeader['name']} - {$squadLeader['platoon_name']}</option>";
	}

	// add empty squad leader option
	$squadLeaders .= "<option value='0' selected>None (Gen Pop)</option>";
} else {
	$squadLeaders = "<option>No squad leaders exist.</option>";
}





// fetch division thread links
$gameThreads = get_game_threads($game_id);
$links = array();
foreach ($gameThreads as $thread) {
	$links[] = $thread['thread_title'] . " - " . $thread['thread_url'];
}

$copy_links = implode("\r\n", $links);

// show wizard links only to dev
$showLinksDisplay = (isDev()) ? "block" : "none";

// bf4db link for player search
$BF4DB = BF4DB;




$breadcrumb = "
<ul class='breadcrumb'>
	<li><a href='/'>Home</a></li>
	<li><a href='/recruiting/'>Recruiting</a></li>
	<li class='active'>Add New Member</li>
</ul>
";



$out .= "
<div class='container fade-in'>
	{$breadcrumb}

	<div class='page-header'>
		<h1><strong>Recruiting</strong> <small>Add New Member <span class='text-warning'>[Not fully functional]</span></small></h1>
	</div>

	<div id='rootwizard'>

		<!-- necessary for step functionality -->
		<div class='navbar guide-nav centered-pills' style='display: {$showLinksDisplay}'>
			<div class='navbar-inner'>
				<ul>
					<li class='slide1'><a href='#tab1' data-toggle='tab'>Recruit Introduction</a></li>
					<li class='slide2'><a href='#tab2' data-toggle='tab'>Add Member Information</a></li>
					<li class='slide3'><a href='#tab3' data-toggle='tab'>Recruit Thread Completion</a></li>
					<li class='slide4'><a href='#tab4' data-toggle='tab'>Final Steps</a></li>
					<li class='slide5'><a href='#tab5' data-toggle='tab'>\"Dreaded Paperwork\"</a></li>
					<li class='slide6'><a href='#tab6' data-toggle='tab'>Complete</a></li>
				</ul>
			</div>
		</div>

		<div class='progress striped-bg'>
			<div class='bar progress-bar progress-bar-striped progress-bar-success active' ></div>
		</div>


		<div class='panel panel-default'>
			<div class='panel-heading tab-title'>
				<strong></strong><span class='pull-right text-muted'>{$game_name} Division</span>
			</div>

			<div class='panel-body'>

				<form class='form-horizontal'>

					<input type='hidden' value='{$game_id}' id='game' name='game' />

					<div class='tab-content'>";

						// start tabbed content

						// tab 1 - introduction

						$out .="
						<div class='tab-pane' id='tab1'>
							<div class='col-xs-12'>
								<p>At this point, you have already established a potential recruit. The next step is to get him or her through AOD's recruiting process, and added to our division structure. If you have not already, you need to get your recruit into ventrilo. Your relationship to your new recruit is vital and begins with the first impression. Get to know them and make them feel welcome. This will make a huge difference down the road.</p>

								<p>Immediately, you need to emphasize the most important parts about being an active member of the AOD community:
									<ul>
										<li>You must <strong>be in Ventrilo</strong> whenever you're ingame.</li>
										<li>You must <strong>login to the forums</strong> at least once a month.</li>
										<li>You should strive to <strong>be a contributing member</strong> of the clan. This includes helping us populate the server, and staying loyal to our servers whenever possible.</li>
										<li>You must <strong>show respect</strong> to other members as well as all other public players.</li>
									</ul>
								</p>
								<p>These are things you will rehash with the forum thread stickies, but reiterating is far better than leaving it out entirely. Don't give them any opportunity not to get that information.</p>

								<p><a href='http://www.clanaod.net/forums/showthread.php?t=3293' target='_blank'><button type='button' class='btn btn-primary'>Vent Server Information</button></a> <a href='http://www.ventrilo.com/download.php' target='_blank'><button type='button' class='tool btn btn-primary' title='Right click to copy link'>Client Download</button></a></p>

							</div>
						</div>";


						// tab 2 - member information form

						$out .="
						<div class='tab-pane' id='tab2'>

							<div class='col-sm-6'>
								<p class='margin-top-20'>Let's gather some information about our new member. Please fill out and check the form completely for accuracy. </p>
								<p>The information you provide will be maintained throughout the process and will be used to put the player in the right platoon and squad. If you are a squad leader, they will be assigned to you by default.</p>
								<p>If you are a squad leader or platoon leader, the squad and/or platoon will be determined by your assignment.</p>
							</div>
							<div class='col-sm-6 well'>
								<div class='form-group memberid-group'>
									<label for='member_id' class='col-sm-3 control-label'>Forum ID</label>
									<div class='col-sm-9'>
										<input type='text' class='form-control' placeholder='12345' id='member_id' name='member_id' tabindex='1'>
									</div>
								</div>

								<div class='form-group forumname-group'>
									<label for='forumname' class='col-sm-3 control-label'>Forum Name</label>
									<div class='col-sm-9'>
										<input type='text' class='form-control' placeholder='JoeSnuffy25' id='forumname' name='forumname' tabindex='2'>
									</div>
								</div>

								<div class='form-group battlelog-group'>
									<label for='battlelog' class='col-sm-3 control-label'>Battlelog Name</label>
									<div class='col-sm-9'>
										<input type='text' class='form-control' placeholder='JoeSnuffy25' id='battlelog' name='battlelog' tabindex='3'>
									</div>
								</div>

								<div class='form-group bf4db-group'>
									<label for='bf4db' class='col-sm-3 control-label'>BF4DB ID <a href='{$BF4DB}search?name=' class='bf4dbid-search tool-e' title='Search BF4DB using BL Name' ><i class='fa fa-search'></i></a></label>
									<div class='col-sm-9'>
										<input type='text' class='form-control' id='bf4db' placeholder='123456' name='bf4db' tabindex='4'>
									</div>
								</div>

								<div class='form-group platoon-group' style='display: {$assignmentPltFieldDisplay}'>
									<label for='platoon' class='col-sm-3 control-label'>Platoon</label>
									<div class='col-sm-9'>
										<select name='platoon' id='platoon' class='form-control'>
											{$platoons}
										</select>
									</div>
								</div>
								
								<div class='form-group squadldr-group' style='display: {$assignmentSqdFieldDisplay}'>
									<label for='squadldr' class='col-sm-3 control-label'>Squad Leader</label>
									<div class='col-sm-9'>
										<select name='squadldr' id='squadldr' class='form-control'>
											{$squadLeaders}
										</select>
									</div>
								</div>
								

								
								<div class='text-center message text-danger'></div>
							</div>

						</div>";


						// tab 3 - Recruiting thread status check

						$out .="
						<div class='tab-pane' id='tab3'>
							<div class='col-sm-6'>

								<p class='margin-top-20'>Listed are the recruiting threads required for each of your division's members to read and understand. The status indicates whether or not your new recruit has made a post in each of those threads.</p><p>You can right-click to copy and paste each of these links to your recruit to have them complete them, but you should take the time to explain each of these threads, hitting the high (important) notes. Ensure each thread is completed (and that they understand them), before continuing.</p>

								<p>For ease, you can copy all of the recruit thread links to your clipboard using the button below.</p>

								<p class='text-center'><button class='tool btn btn-primary copy-button' data-clipboard-text='{$copy_links}' 
									type='button'>Copy Thread Links</button>
								</p>
							</div>
							<div class='col-sm-6 well'>

								<div class='search-subject text-center'></div>
								<div class='thread-results text-center'></div>

							</div>
						</div>";


						// tab 4 - Final steps with recruit

						$out .="					
						<div class='tab-pane' id='tab4'>

							<p>Now, you are ready to finalize your new recruit and take care of the paperwork associated with each new recruit. <strong>Be sure to ask</strong> if there are any questions or concerns your recruit may have. You should also remind him/her that <strong>you will be their squad leader</strong> and can come to you if they have any issues in the relative future.</p><p>Your next steps should include:</p>
							<ul>
								<li>Having them adjust their forum (AOD Member Info) profile settings</li>
								<li>Changing their name on ventrilo <code class='rank-name'>NaN</code></li>
								<li>Accepting or inviting them into the BF4 platoon on Battlelog</li>
								<li>Give them the <a href='http://www.clanaod.net/forums/showthread.php?t=3293' target='_blank'>channel password</a> and introduce them to the other members</li>
							</ul>

						</div>";


						// tab 5 - Confirm member information

						$out .="
						<div class='tab-pane' id='tab5'>
							

							<div role='tabpanel'>

								<ul class='nav nav-tabs' role='tablist'>
									<li role='presentation' class='active'><a href='#division-post' aria-controls='division-post' role='tab' data-toggle='tab'>Post to division structure</a></li>
									<li role='presentation'><a href='#welcome-post' aria-controls='welcome-post' role='tab' data-toggle='tab'>Post Welcome thread</a></li>
									<li role='presentation'><a href='#welcome-pm' aria-controls='welcome-pm' role='tab' data-toggle='tab'>Send Welcome PM</a></li>
									<li role='presentation'><a href='#member-request' aria-controls='member-request' role='tab' data-toggle='tab'>Request new member status</a></li>
								</ul>

								<div class='tab-content'>
									<div role='tabpanel' class='tab-pane active' id='division-post'>
										<div class='row margin-top-20'>

											<div class='col-md-6'>
												<p>A division structure post needs to be made so that your new recruit can be added to the forum thread in addition to being tracked here. The box to the right shows what your division structure post should look like, including the information you have provided.</p>
												<p>Click the copy button to copy the contents of the box to your clipboard. Then follow the division structure link to make your post.</p>
												<p class='margin-top-20'><a href='http://www.clanaod.net/forums/newreply.php?p=73448&noquote=1' class='text-center' target='_blank'><button type='button' class='btn btn-default'>Open Division Structure</button></a></p>
											</div>

											<div class='col-md-6'>
												<div class='well'>
													<button type='button' class='division-code-btn copy-button btn btn-default tool pull-right' title='Copy to clipboard'><i class='fa fa-clipboard'></i></button> 
													<code class='post-code'></code>
												</div>
											</div>

										</div>
									</div>

									<div role='tabpanel' class='tab-pane' id='member-request'><p>Nulla rhoncus enim vel diam sagittis, eget dictum dolor auctor. Mauris placerat, dolor vitae luctus auctor, tortor mi tempor tellus, sed aliquam purus neque eget urna. Vestibulum massa lorem, tincidunt vel sapien et, ultrices eleifend magna. Aenean id neque ut odio euismod ullamcorper vitae et turpis. Etiam varius libero ut hendrerit faucibus. Nam sit amet ante vehicula, volutpat est sit amet, tincidunt ipsum. Aliquam vestibulum, sem ac egestas hendrerit, dolor elit malesuada justo, in tincidunt sem risus nec arcu. Praesent at risus rhoncus orci pharetra laoreet. Nullam sed turpis dui. Duis luctus nibh ipsum. Aliquam in molestie enim, sed pretium nisl. Quisque at diam at lectus porttitor auctor et ut nisl. Ut tempor odio a magna elementum, in porttitor leo posuere. Sed tempus volutpat mauris, finibus blandit magna fringilla sit amet. Nullam tempus diam dolor, quis convallis velit pretium eget. Nullam in tincidunt risus.</p>
									</div>
									<div role='tabpanel' class='tab-pane' id='welcome-post'><p>Vivamus congue, ex vel lacinia accumsan, turpis massa lacinia arcu, tristique ullamcorper velit ligula sed augue. Sed sit amet mi mi. Maecenas sed ipsum maximus, aliquam diam vitae, viverra est. Duis feugiat ipsum a nunc dapibus feugiat. Vestibulum est ipsum, condimentum non sodales a, pulvinar sed velit. Donec porttitor neque nulla, ac maximus justo cursus vel. Suspendisse potenti. Cras rutrum purus gravida tempus vestibulum. Donec sagittis vitae felis quis accumsan.</p>
									</div>
									<div role='tabpanel' class='tab-pane' id='welcome-pm'><p>Mauris quis tempor nisi. Nullam accumsan tempus eros sit amet finibus. Nam ac orci vel velit pulvinar convallis. Donec sit amet ipsum at metus scelerisque mattis. Quisque sollicitudin arcu eu ligula mattis volutpat. Nulla facilisi. Etiam suscipit lobortis scelerisque. Proin malesuada aliquet scelerisque. Aenean tristique felis ac sem lacinia tempor. Cras et maximus nisl. Aenean laoreet hendrerit lectus, et euismod ante rutrum ut. Nunc pretium magna sit amet libero mattis, sed condimentum elit efficitur. Quisque auctor ligula nibh, eget consectetur lectus laoreet nec. Fusce laoreet, magna eu pretium rhoncus, neque neque sollicitudin lorem, vel rutrum lacus nunc a enim. Etiam in hendrerit mi. Aliquam cursus augue suscipit sem volutpat, sed consequat est finibus.</p>
									</div>

								</div>




							</div>
						</div>";


						// tab 6 - forum integration actions

						$out .="

						<div class='tab-pane' id='tab6'>
							<p class='lead'><i class='fa fa-check text-success'></i> You have successfully completed <span class='player-name'>NaN</span>'s recruiting process!</p>
						</div>";


						// end tabbed content

						$out .="

					</div>	
				</form>
			</div>


			<div class='panel-footer'>
				<ul class='pager wizard'>
					<li class='previous first' style='display:none;'><a href='#'>First</a></li>
					<li class='previous'><a href='#'>Previous</a></li>
					<li class='next last' style='display:none;'><a href='#'>Last</a></li>
					<li class='next'><a href='#'>Continue</a></li>
				</ul>
			</div>

		</div><!-- end panel -->
	</div><!-- end root wizard -->
</div><!-- end container -->

<script src='/public/js/recruit.js'></script>";

echo $out; 

?>