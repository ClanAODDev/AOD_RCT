<?php

$out = NULL;
$game_list = NULL;

$games = get_games();


/**
 * generate games list
 */
foreach ($games as $game) {

	$shortname = strtolower($game['short_name']);
	$longname = $game['full_name'];
	$shortdescr = $game['short_descr'];

	$game_list .= "
	<a href='/{$shortname}' class='list-group-item'><strong>{$longname}</strong><i class='fa fa-angle-double-right pull-right text-muted'></i></a>";
}


/**
* is user on a guest account?
*/


// begin container
$out .= "
<div class='container fade-in margin-top-20'>";


	if ($userRole == 0) {
		$out .= "
		<div class='alert alert-warning' role='alert'><i class=\"fa fa-exclamation-triangle\"></i> You are currently a guest. You will need to have an administrator approve your account before you can use this application</div>";
	}

	$out .= "
	<div class='alert alert-info' role='alert'><i class=\"fa fa-exclamation-triangle\"></i> Application is still very much under construction, so certain features may not be available or fully functional. Please stay patient and check back often!</div>";


	$out .="
	<div class='row visible-lg-block visible-md-block'>
		<div class='col-md-12'>
			<div class='jumbotron'>
				<h1>Howdy <strong>{$curUser}</strong>!</h1>
				<p>This is the squad administration tool for the AOD organization. Leaders will be able to manage individual squad members, view activity trends of their respective divisions, and clan leaders will be able to see activity clan-wide and across divisions.</p>
			</div>
		</div> <!-- end col -->
	</div> <!-- end end row -->";


	// shortcuts
	/*$out .="
	<div class='row'>
		<div class='col-md-8'>
			<div class='panel panel-default'>
				<div class='panel-heading'>
					<h4>Your tools</h4>
				</div>
				<div class='panel-body text-center'>

					<div class='btn-toolbar' role='toolbar' aria-label='Toolbar with button groups'>
						<div class='btn-group-horizontal' role='group'>

							<button type='button' class='btn btn-primary btn-lg'><i class=\"fa fa-user fa-2x\"></i> <br />Add New Recruit</button>
							<button type='button' class='btn btn-primary btn-lg'><i class=\"fa fa-user fa-2x\"></i> <br />Add New Recruit</button>
						</div>
					</div>
				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end end row -->
	";
*/


	$out .= "

	<div class='row'>

		<div class='col-md-6'>
			<div class='panel panel-default'>
				<div class='panel-heading'><i class=\"fa fa-bolt text-muted\"></i> <strong>Your Toolbox</strong><span class='text-muted pull-right'><small>Squad Leader View</small></span></div>
				<div class='panel-body'>
					<div class='list-group'>

						<a href='#' class='list-group-item'>
							<h4 class='list-group-item-heading'><strong>Add new recruit</strong></h4>
							<p class='list-group-item-text'>Start the recruiting process with a brand new candidate</p>
						</a>

						<a href='#' class='list-group-item'>
							<h4 class='list-group-item-heading'><strong>Manage your squad</strong></h4>
							<p class='list-group-item-text'>Promote, demote, or kick members of your squad</p>
						</a>

						<a href='#' class='list-group-item disabled'>
							<h4 class='list-group-item-heading'><strong>Review inactive members</strong></h4>
							<p class='list-group-item-text'>View inactive members and flag for removal (available monthly)</p>
						</a>

					</div>
				</div>
			</div>
		</div>

		<div class='col-md-3'>
			<div class='panel panel-default'>
				<div class='panel-heading'><i class='fa fa-users text-muted'></i><strong> Your Squad</strong><span class='text-muted pull-right'>BF4</span></div>
				<div class='panel-body'>
					<div class='list-group'>
						<li class='list-group-item list-group-item-danger follow-tool' title='Inactive for more than 60 days'> Rct Snuffy<i class='fa fa-exclamation-circle pull-right'></i></li>
						<li class='list-group-item list-group-item-danger follow-tool' title='Inactive for more than 60 days'> Rct Snuffy<i class='fa fa-exclamation-circle pull-right'></i></li>
						<li class='list-group-item list-group-item-warning follow-tool' title='Needs reviewing for promotion'> Rct Snuffy<i class='fa fa-exclamation-circle pull-right'></i></li>
						<li class='list-group-item'> Cdt Snuffy</li>
						<li class='list-group-item'> Cdt Snuffy</li>
						<li class='list-group-item'> Pvt Snuffy</li>
						<li class='list-group-item'> Pfc Snuffy</li>
						<li class='list-group-item'> Pfc Snuffy</li>
						<li class='list-group-item'> Pfc Snuffy</li>
					</div>
				</div>
			</div>
		</div>

		<div class='col-md-3'>
			<div class='panel panel-default'>
				<div class='panel-heading'><i class=\"fa fa-gamepad text-muted\"></i> <strong>Gaming Divisions</strong></div>
				<div class='panel-body'>
					<div class='list-group'>
						{$game_list}
					</div>
				</div>
			</div>
		</div>
	</div>


	";



	$out .=" 
</div>
";

	// end container

echo $out;



?>