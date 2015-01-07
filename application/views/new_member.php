<?php

if (!isset($_SESSION['secure_access']) || (isset($_SESSION['secure_access']) && $_SESSION['secure_access'] !== true)) { header("Location: /404/"); }

error_reporting(E_ALL);
ini_set('display_errors', '1');


$out = NULL;
$platoons = NULL;
$squadLeaders = NULL;

$game_info = get_game_info($member_info['game_id']);
$short_game_name = $game_info['short_name'];
$game_name = $game_info['full_name'];
$game_id = $game_info['id'];
$division_structure_thread = $game_info['division_structure_thread'];
$welcome_forum = $game_info['welcome_forum'];




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
								<p class='margin-top-20'>Does your new recruit have a forum account? They will need one for you to complete this section. Please fill out and check the form completely for accuracy once this has been done. </p>
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

								<p class='margin-top-20'>Listed are the recruiting threads required for each of your division's members to read and understand. The status indicates whether or not your new recruit has made a post in each of those threads (checking last 5 pages of a thread ensures we don't miss a post).</p><p>You can right-click to copy and paste each of these links to your recruit to have them complete them, but you should take the time to explain each of these threads, hitting the high (important) notes. Ensure each thread is completed (and that they understand them), before continuing.</p>
		
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
								<li>Changing their name on ventrilo <code class='rank-name'>NaN</code><i class='fa fa-copy copy-link text-primary player-name-copy' title='Copy link to clipboard' href='#'></i></li>
								<li>Accepting or inviting them into the BF4 platoon on Battlelog</li>
								<li>Give them the <a href='http://www.clanaod.net/forums/showthread.php?t=3293' target='_blank'>channel password</a> and introduce them to the other members</li>
							</ul>

						</div>";


						// tab 5 - forum integration actions

						$out .="
						<div class='tab-pane' id='tab5'>
							
							<div class='col-md-12'>

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
													<p class='margin-top-20'><a href='http://www.clanaod.net/forums/showthread.php?t={$division_structure_thread}' class='text-center' target='_blank'><button type='button' class='btn btn-primary'>Open Division Structure</button></a></p>
												</div>

												<div class='col-md-6'>
													<div class='well code'>
														<button type='button' class='division-code-btn copy-button btn btn-default tool pull-right' title='Copy to clipboard'><i class='fa fa-copy'></i></button> 
														<code class='post-code'></code>
													</div>
												</div>

											</div>
										</div>

										
										<div role='tabpanel' class='tab-pane' id='welcome-post'>
											<div class='row margin-top-20'>
												<div class='col-md-12'>
													<p>A welcome thread is created for each new recruit. It serves multiple purposes: It makes the recruit feel welcome. This is very important., It gives everyone a chance to know who is new in the division, which includes people from other divisions and even the leadership. and lastly, if you do nothing else on our forums, say hi to the new members.</p>

													<p>It also wouldn't hurt to let your new recruit know you made a new post for them, so they can introduce themselves to everyone.</p>

													<p class='margin-top-20'><a href='http://www.clanaod.net/forums/newthread.php?do=newthread&f={$welcome_forum}' class='text-center' target='_blank'><button type='button' class='btn btn-primary'>Create welcome thread</button></a></p>
												</div>

											</div>
										</div>

										<div role='tabpanel' class='tab-pane' id='welcome-pm'>
											<div class='row margin-top-20'>
												<div class='col-md-6'>
													<p>In addition to your discussion with your new recruit, it's always a good idea to recap. For this reason, we like to send follow-up PMs to our new members summarizing what we went over in case they have any questions. It's also a good way to start a conversation with them on the forums, and generally a good way to close things up.</p>
													<p>Click the copy button to copy the contents of the box to your clipboard. Then follow the link to send a PM to your recruit.</p>
													<p class='margin-top-20'><a href='#' class='text-center pm-link' target='_blank'><button type='button' class='btn btn-primary'>Send Forum PM</button></a></p>
												</div>

												<div class='col-md-6'>
													<div class='well code'>
														<button type='button' class='welcome-pm-btn copy-button btn btn-default tool pull-right' title='Copy to clipboard'><i class='fa fa-copy'></i></button> 
														<code class='welcome-code'></code>
													</div>
												</div>

											</div>
										</div>


										<div role='tabpanel' class='tab-pane' id='member-request'>
											<div class='row margin-top-20'>
												<div class='col-md-12'>
													<p>Finally, a request must be made so your new recruit can be set as an AOD member on the forums, and be able to see all the hidden content specifically for our division.</p>
													<p class='margin-top-20'><a href='http://www.clanaod.net/forums/newreply.php?&t={$division_structure_thread}' class='text-center' target='_blank'><button type='button' class='btn btn-primary'>Submit Request</button></a></p>
												</div>
											</div>
										</div>

										
									</div>
								</div>
							</div>
						</div>";


							// tab 6 - completion

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