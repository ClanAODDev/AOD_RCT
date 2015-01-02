<?php

$out = NULL;

$game_info = get_game_info($member_info['game_id']);
$short_game_name = $game_info['short_name'];
$game_name = $game_info['full_name'];
$game_id = $game_info['id'];

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
		<h1><strong>Recruiting</strong> <small>Add New Member</small></h1>
	</div>

	<div id='rootwizard'>

		<!-- necessary for step functionality -->
		<div class='navbar guide-nav' style='display: {$showLinksDisplay}'>
			<div class='navbar-inner'>
				<ul>
					<li class='slide1'><a href='#tab1' data-toggle='tab'>Recruit Introduction</a></li>
					<li class='slide2'><a href='#tab2' data-toggle='tab'>Add Member Information</a></li>
					<li class='slide3'><a href='#tab3' data-toggle='tab'>Recruit Thread Completion</a></li>
					<li class='slide4'><a href='#tab4' data-toggle='tab'>Final Steps</a></li>
					<li class='slide5'><a href='#tab4' data-toggle='tab'>Confirm Information</a></li>
					<li class='slide6'><a href='#tab5' data-toggle='tab'>Automated Tasks</a></li>
				</ul>
			</div>
		</div>

		<div class='progress' style='height: 40px;'>
			<div class='bar progress-bar progress-bar-striped progress-bar-success active' ></div>
		</div>


		<div class='panel panel-default'>
			<div class='panel-heading tab-title'>
				<strong></strong>
			</div>

			<div class='panel-body'>

				<form class='form-horizontal'>

					<input type='hidden' value='{$game_id}' id='game' name='game' />

					<div class='tab-content'>


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
						</div>

						<div class='tab-pane' id='tab2'>
							<div class='col-sm-6 well'>
								<div class='form-group memberid-group'>
									<label for='member_id' class='col-sm-3 control-label'>Forum ID</label>
									<div class='col-sm-9'>
										<input type='text' class='form-control' id='member_id' name='member_id'>
									</div>
								</div>

								<div class='form-group forumname-group'>
									<label for='forumname' class='col-sm-3 control-label'>Forum Name</label>
									<div class='col-sm-9'>
										<input type='text' class='form-control' id='forumname' name='forumname'>
									</div>
								</div>

								<div class='form-group battlelog-group'>
									<label for='battlelog' class='col-sm-3 control-label'>Battlelog Name</label>
									<div class='col-sm-9'>
										<input type='text' class='form-control' id='battlelog' name='battlelog'>
									</div>
								</div>

								<div class='form-group bf4db-group'>
									<label for='bf4db' class='col-sm-3 control-label'>BF4DB ID <a href='{$BF4DB}search?name=' class='bf4dbid-search tool-e' title='Search BF4DB using BL Name' ><i class='fa fa-search'></i></a></label>
									<div class='col-sm-9'>
										<input type='text' class='form-control' id='bf4db'  name='bf4db'>
									</div>
								</div>
								<div class='text-center message text-danger'></div>
							</div>

							<div class='col-sm-6'>
								<p>Let's gather some information about our new member. Please fill out and check the form completely for accuracy. </p>
								<p>The information you provide will be maintained throughout the process and will be used to put the player in the right platoon and squad. If you are a squad leader, they will be assigned to you by default.</p>
							</div>

						</div>
						<div class='tab-pane' id='tab3'>
							<div class='col-sm-6'>

								<p>Listed are the recruiting threads required for each of your division's members to read and understand. The status indicates whether or not your new recruit has made a post in each of those threads.</p><p>You can right-click to copy and paste each of these links to your recruit to have them complete them, but you should take the time to explain each of these threads, hitting the high (important) notes. Ensure each thread is completed (and that they understand them), before continuing.</p>

								<p>For ease, you can copy all of the recruit thread links to your clipboard using the button below.</p>

								<p class='text-center'><button class='tool btn btn-primary' id='copy-button' data-clipboard-text='{$copy_links}' 
									title='Click to copy recruit threads' type='button'>Copy Thread Links</button>
								</p>
							</div>
							<div class='col-sm-6 well'>
								<div class='search-subject text-center'></div>
								<div class='thread-results text-center'></div>

							</div>
						</div>					
						<div class='tab-pane' id='tab4'>

							<p>At this point, you are now ready to finalize your new recruit and take care of the paperwork associated with each new recruit. <strong>Be sure to ask</strong> if there are any questions or concerns your recruit may have. You should also remind him/her that <strong>you will be their squad leader</strong> and can come to you if they have any issues in the relative future.</p><p>Your next steps should include:</p>
							<ul>
								<li>Having them adjust their forum member profile settings</li>
								<li>Changing their name on ventrilo <code class='player-name'></code></li>
								<li>Accepting them into the BF4 platoon on Battlelog</li>
								<li>Give them the <a href='http://www.clanaod.net/forums/showthread.php?t=3293' target='_blank'>channel password</a> and introduce them to the other members</li>
							</ul>

						</div>
						<div class='tab-pane' id='tab5'>
							5
						</div>


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