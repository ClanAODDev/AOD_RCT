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
	<div class='row'>
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
		<div class='col-md-8'>
			<div class='panel panel-default'>
				<div class='panel-heading'><strong>Your Squad</strong><span class='text-muted'> (". ordSuffix(2) . " Platoon, Deity's Demons)</span></div>
				<div class='panel-body'>
					<ol class='list-group'>
						<li class='list-group-item'><strong>Rct Snuffy</strong></li>
						<li class='list-group-item'><strong>Rct Snuffy</strong></li>
						<li class='list-group-item list-group-item-info follow-tool' title='Needs reviewing for promotion'><strong>Rct Snuffy</strong></li>
						<li class='list-group-item'><strong>Rct Snuffy</strong></li>
						<li class='list-group-item list-group-item-danger follow-tool' title='Inactive for more than 60 days'><strong>Rct Snuffy</strong></li>
						<li class='list-group-item list-group-item-danger follow-tool' title='Inactive for more than 60 days'><strong>Rct Snuffy</strong></li>

					</ol>
				</div>
			</div>
		</div>


		<div class='col-md-4 pull-right'>
			<div class='panel panel-primary'>
				<div class='panel-heading'>Gaming Divisions</div>
				<div class='panel-body'>
					<div class='list-group'>
						{$game_list}
					</div></div>
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