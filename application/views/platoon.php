<?php

if (isLoggedIn()) {

	// eventually need to check if the current user is 
	// either an admin or is actually a part of the platoon
	// being requested, else reject request

	$out = NULL;
	$platoon = $params['platoon'];



	$right_now = new DateTime("now");

	$first_day_of_last_month = date("Y-m-d", strtotime("first day of previous month"));
	$last_day_of_last_month = date("Y-m-d", strtotime("last day of previous month"));


	$out .= "
	<div class='container fade-in margin-top-20'>
		<div class='row'>{$platoon}

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