<?php

$out = NULL;

$breadcrumb = "
<ul class='breadcrumb'>
	<li><a href='/'>Home</a></li>
	<li class='active'>Add a new recruit</li>
</ul>
";

$game_info = get_game_info($member_info['game_id']);
$short_game_name = $game_info['short_name'];
$game_name = $game_info['full_name'];
$game_id = $game_info['id'];

// show wizard links only to dev
$showLinksDisplay = (isDev()) ? "block" : "none";

// bf4db link for player search
$BF4DB = BF4DB;


$out .= "
<div class='container fade-in'>
	{$breadcrumb}

	<div class='page-header'>
		<h1><strong>Recruiting</strong> <small>Adding a new member</small></h1>
	</div>
	<div class='recruit-intro'>
		<div class='row'>
			<div class='col-md-12'>
				<p class='lead'>Ready to start a new recruit? To make things easier, you should have a couple items handy. Make sure you have them copied somewhere so you don't get backed up later:</p>

			</div>
		</div>

		<div class='row margin-top-20'>
			<div class='col-xs-4'>
				<div class='panel panel-primary'>
					<div class='panel-heading'><strong>AOD Forum ID</strong></div>
					<img src='public/images/forum_id_ss.jpg' class='img-responsive'/>
				</div>
			</div>

			<div class='col-xs-4'>
				<div class='panel panel-primary'>
					<div class='panel-heading'><strong>Battlelog Name</strong></div>
					<img src='public/images/bf4_name_ss.jpg' class='img-responsive'/>			
				</div>
			</div>
			<div class='col-xs-4'>
				<div class='panel panel-primary'>
					<div class='panel-heading'><strong>BF4DB ID</strong></div>
					<img src='public/images/bf4db_id_ss.jpg' class='img-responsive'/>			
				</div>
			</div>
		</div>

		<div class='row margin-top-20'>
			<div class='col-md-12'>
				<p>This information is necessary for us to keep track of our players' activity, and also to provide integration with the AOD forums. If you do not know how to obtain this information, consult your leadership.</p>
				<p>Once you are ready to begin, click below to start. Be prepared to complete the process entirely once started. <span class='text-danger'>You cannot return to an unfinished recruit session.</span></p>
			</div>
		</div>

		<div class='row margin-top-50'>
			<div class='col-md-12 text-center'>
				<a href='#' class='recruit-init'><button type='button' class='btn btn-lg btn-primary'>Begin Recruit Process <i class='fa fa-arrow-right'></i></button></a>
			</div>
		</div>

	</div>

	<!---->
	<div id='rootwizard' style='display: none;'>

		<!-- necessary for step functionality -->
		<div class='navbar' style='display: {$showLinksDisplay}'>
			<div class='navbar-inner'>
				<ul>
					<li><a href='#tab1' data-toggle='tab' class='disabled' disabled='disabled'>Slide One</a></li>
					<li><a href='#tab2' data-toggle='tab' class='disabled' disabled='disabled'>Slide Two</a></li>
					<li><a href='#tab3' data-toggle='tab' class='disabled' disabled='disabled'>Slide Three</a></li>
					<li><a href='#tab4' data-toggle='tab' class='disabled' disabled='disabled'>Slide Four</a></li>
					<li><a href='#tab5' data-toggle='tab' class='disabled' disabled='disabled'>Slide Five</a></li>
				</ul>
			</div>
		</div>

		<div class='progress' style='height: 40px;'>
			<div class='bar progress-bar progress-bar-striped progress-bar-info active' ></div>


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
									<label for='bf4db' class='col-sm-3 control-label'>BF4DB ID <a href='{$BF4DB}search?name=' class='bf4dbid-search tool' title='Search BF4DB using BL Name' ><i class='fa fa-search'></i></a></label>
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
								<p>Listed are the recruiting threads required for each of your division's members to read and understand. The status indicates whether or not your new recruit has made a post in each of those threads.</p><p>You can right-click to copy and paste each of these links to your recruit to have them complete them, but you should take the time to explain each of these threads, hitting the high (important) notes. Ensure each thread is completed (and that they understand them), before continuing.</p><p><strong>Note: </strong>If the thread checks don't seem to be working, ensure the 'forum name' on the previous step matches the user's name exactly.</p><p>For ease, you can copy all of the recruit thread links to your clipboard using the button below.</p>
								<p class='text-center'><button class='tool btn btn-primary' id='copy-button' data-clipboard-text='http://google.com/ - http://google.com/ -http://google.com/' 
									title='Click to copy recruit threads' type='button'>Copy Thread Links</button>
								</p>
							</div>
							<div class='col-sm-6'>
								<div class='thread-results text-center'></div>
							</div>
						</div>					
						<div class='tab-pane' id='tab4'>
							4
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
					<li class='next'><a href='#'>Next</a></li>
				</ul>
			</div>

		</div>
	</div>
</div>


<script src='/public/js/recruit.js'></script>";


echo $out;

?>