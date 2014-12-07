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

		
		$out .="
		<div class='row'>
			";

			// shortcuts
			$out .="
			<div class='col-md-12'>
				<div class='panel panel-default'>
					<div class='panel-heading'>
						<h4><i class=\"fa fa-suitcase\"></i> Action Bar</h4>
					</div>
					<div class='panel-body'>

						<div class='btn-toolbar' role='toolbar' aria-label='Toolbar with button groups'>
							<div class='btn-group' role='group' aria-label='First group'>
								<button type='button' class='btn btn-default'><i class=\"fa fa-suitcase fa-2x\"></i> <br />Add new session</button>
								<button type='button' class='btn btn-primary'><i class=\"fa fa-suitcase fa-2x\"></i> <br />Add new session</button>
								<button type='button' class='btn btn-default'><i class=\"fa fa-suitcase fa-2x\"></i> <br />Add new session</button>
								<button type='button' class='btn btn-success'><i class=\"fa fa-suitcase fa-2x\"></i> <br />Add new session</button>
								<button type='button' class='btn btn-warning'><i class=\"fa fa-suitcase fa-2x\"></i> <br />Add new session</button>
								<button type='button' class='btn btn-danger'><i class=\"fa fa-suitcase fa-2x\"></i> <br />Add new session</button>
								<button type='button' class='btn btn-default'><i class=\"fa fa-suitcase fa-2x\"></i> <br />Add new session</button>
							</div>
						</div>
					</div>
				</div>
			</div> <!-- end col -->
		</div> <!-- end end row -->


		<div class='row'>

			<div class='col-lg-4'>
				<div class='bs-component'>
					<div class='panel panel-default'>
						<div class='panel-body'>
							Basic panel
						</div>
					</div>

					<div class='panel panel-default'>
						<div class='panel-heading'>Panel heading</div>
						<div class='panel-body'>
							Panel content
						</div>
					</div>

					<div class='panel panel-default'>
						<div class='panel-body'>
							Panel content
						</div>
						<div class='panel-footer'>Panel footer</div>
					</div>
					<div id='source-button' class='btn btn-primary btn-xs' style='display: none;'>&lt; &gt;</div></div>
				</div>


				<div class='col-lg-4'>
					<div class='bs-component'>
						<div class='panel panel-primary'>
							<div class='panel-heading'>
								<h3 class='panel-title'>Panel primary</h3>
							</div>
							<div class='panel-body'>
								Panel content
							</div>
						</div>

						<div class='panel panel-success'>
							<div class='panel-heading'>
								<h3 class='panel-title'>Panel success</h3>
							</div>
							<div class='panel-body'>
								Panel content
							</div>
						</div>

						<div class='panel panel-warning'>
							<div class='panel-heading'>
								<h3 class='panel-title'>Panel warning</h3>
							</div>
							<div class='panel-body'>
								Panel content
							</div>
						</div>
					</div>
				</div>


				<div class='col-lg-4'>
					<div class='bs-component'>
						<div class='panel panel-danger'>
							<div class='panel-heading'>
								<h3 class='panel-title'>Panel danger</h3>
							</div>
							<div class='panel-body'>
								Panel content
							</div>
						</div>

						<div class='panel panel-info'>
							<div class='panel-heading'>
								<h3 class='panel-title'>Panel info</h3>
							</div>
							<div class='panel-body'>
								Panel content
							</div>
						</div>
					</div>
				</div>
			</div>


			";


			$out .= "
			<div class='col-lg-6'>
				<div class='panel panel-default'>
					<div class='panel-heading'>
						<h4>Recent Activity</h4>
					</div>
					<div class='panel-body'>
						Users can now access platoon data via the navigation bar. Eventually this data will cater specifically to the user logged in. For now, all data is visible.
					</div>
				</div>
			</div> <!-- end col -->
			";

			$out .=" 
		</div> <!-- end container -->
		";

		echo $out;

	}

	?>