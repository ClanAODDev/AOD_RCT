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

			<div class='panel panel-warning'>
				<div class='panel-heading'>
					<strong>Under Construction</strong>
				</div>
				<div class='panel-body'>
					Application is currently being developed. More tools and services will soon be available for use.
				</div>
			</div>

		</div>
	</div>
	";

	echo $out;

}

?>