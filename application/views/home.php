<?php
if (isLoggedIn()) {

	$out = NULL;

	$out .= "
	<div class='container fade-in margin-top-20'>
		<div class='row'>
			<div class='col-md-12'>
				<div class='jumbotron'>
					<h1>Howdy <strong>{$curUser}</strong>!</h1>
					<p>This is the squad administration tool for the AOD organization. Leaders will be able to manage individual squad members, view activity trends of their respective divisions, and clan leaders will be able to see activity clan-wide and across divisions.</p>
				</div>
			</div> <!-- end col -->
		</div> <!-- end end row -->

		";



/**
 * is user on a guest account?
 */

if ($userRole == 0) {
	$out .= "
	<div class='alert alert-warning' role='alert'><strong>Notice:</strong> You are currently a guest. You will need to have an administrator approve your account before you can use this application</div>
	";
}



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
			<div class='col-lg-12'>
				<div class='panel panel-default'>
					<div class='panel-heading'>
						<h4>Under Construction</h4>
					</div>
					<div class='panel-body'>
						The squad tracker is still under construction. However, some features are still available via the navigation bar.
					</div>
				</div>
			</div> <!-- end col -->
		</div>
		";

		$out .=" 
	</div> <!-- end container -->
	";

	echo $out;

}

?>