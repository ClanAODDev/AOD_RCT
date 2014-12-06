<?php
if (isLoggedIn()) {

	$out = NULL;

	$out .= "
	<div class='container fade-in margin-top-20'>
		<div class='row'>

			<div class='jumbotron'>
				<h1>Howdy <strong>{$curUser}</strong>!</h1>
				<p>This is the squad administration tool for the AOD organization. Leaders will be able to manage individual squad members, view activity trends of their respective divisions, and clan leaders will be able to see activity clan-wide and across divisions.</p>
			</div>

			<div class='panel panel-info'>
				<div class='panel-heading'>
					<strong>Platoons section now added</strong>
				</div>
				<div class='panel-body'>
					Users can now access platoon data via the navigation bar. Eventually this data will cater specifically to the user logged in. For now, all data is visible.
				</div>
			</div>

		</div>
	</div>
	";

	echo $out;

}

?>