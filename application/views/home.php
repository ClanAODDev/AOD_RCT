<?php

$out = NULL;

// begin container
$out .= "
<div class='container fade-in margin-top-20'>";


	if ($userRole == 0) {
		$out .= "
		<div class='alert alert-warning' role='alert'><i class=\"fa fa-exclamation-triangle\"></i> You are currently a guest. You will need to have an administrator approve your account before you can use this application</div>";
	}

	// output alerts
	$out .= "{$alerts_list}";

	// jumbotron permanent or disable-able?
	$out .="
	<div class='row visible-lg-block visible-md-block'>
		<div class='col-md-12'>
			<div class='jumbotron striped-bg'>
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
				<div class='panel-heading'><i class=\"fa fa-bolt fa-lg text-muted\"></i> <strong>Your Toolbox</strong><span class='text-muted pull-right'><small>Squad Leader View</small></span></div>
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

						<a href='#' class='list-group-item'>
							<h4 class='list-group-item-heading'><strong>Add an existing member</strong></h4>
							<p class='list-group-item-text'>Add an existing member of AOD to your squad or platoon</p>
						</a>

						<a href='#' class='list-group-item disabled'>
							<h4 class='list-group-item-heading'><strong>Review inactive members</strong></h4>
							<p class='list-group-item-text'>View inactive members and flag for removal (available monthly)</p>
						</a>

					</div>
				</div>
			</div>
		</div>

		<div class='col-md-6'>
			<div class='panel panel-default'>
				<div class='panel-heading'><i class='fa fa-users fa-lg text-muted'></i><strong> Your Squad</strong><span class='text-muted pull-right'><small>Battlefield 4</small></span></div>
				<div class='panel-body'>
					<div class='list-group'>
						<a href='#' class='list-group-item list-group-item-danger follow-tool' title='Inactive for more than 60 days'> Rct Snuffy<i class='fa fa-exclamation-circle pull-right'></i></a>
						<a href='#' class='list-group-item list-group-item-danger follow-tool' title='Inactive for more than 60 days'> Rct Snuffy<i class='fa fa-exclamation-circle pull-right'></i></a>
						<a href='#' class='list-group-item list-group-item-warning follow-tool' title='Needs reviewing for promotion'> Rct Snuffy<i class='fa fa-exclamation-circle pull-right'></i></a>
						<a href='#' class='list-group-item'> Cdt Snuffy</a>
						<a href='#' class='list-group-item'> Cdt Snuffy</a>
						<a href='#' class='list-group-item'> Pvt Snuffy</a>

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