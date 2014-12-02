<?php

$out = NULL;




/*

	// if user is not logged in
	if (!isset($_GET['game'])) {

		$out .= "
		<div class='container game-select fade-in '>
			<div class='row'>
				<h3>Log In</h3>
				<p>Please fill out the following information</p>
			</div>
			<div class='row'>
				<form class='form-horizontal' role='form'>
					<div class='form-group'>
						<label class='sr-only' for='inputEmail'>Recruit</label>
						<input type='text' class='form-control' id='player' name='player' placeholder='Recruit Name' required />
					</div>
					<div class=\"form-group\">
						<select class=\"form-control\" name=\"game\" id=\"game\" required>";

							foreach ($games as $game) {
								// $out .= "<li><a href='?game=". $game['id'] . "'>" . $game['full_name'] . "</a></li>";
								$out .= "<option value=\"{$game['id']}\">{$game['full_name']}</option>";
							}

							$out .= "
						</select>
					</div>

					<button type='submit' class='btn btn-primary'>Continue</button>

				</form>
			</div>

			<small class='text-muted'>Application is currently under construction.</small>
		</div>


		";


	// game exists, let's move forward
	} else {

		$game_id = $_GET['game'];	
		$game = get_game_info($game_id);

		if (count($game)) {

			while ($game_info = $game->fetch()) {
				$name = forceEmptyMessageIfNull($game_info['full_name']);
				$description = forceEmptyMessageIfNull($game_info['description']);
				$abbrev = forceEmptyMessageIfNull($game_info['short_name']);
				$subforum = forceEmptyMessageIfNull($game_info['subforum']);
			}


			$out .="

			<div class='container fade-in'>
				<div class='row'>
					<h3><strong>{$name}</strong> Division <a href='{$subforum}' class='pull-right' target='_blank' title='AOD Clan Forums'><button type='button' class='btn btn-info'>{$abbrev} Division Forums <i class='glyphicon glyphicon-comment'></i> </button></a></h3>
					<hr />
					<p>{$description}</p>
					<hr />
				</div>
			</div>


			";
		}


		if (!is_null($player)) {

			$out .= "
			<div class='container fade-in'>
				<div class='row'>
					<h4>Thread Completion</h4><hr />
					<div class='thread-results text-center'></div>
				</div>
			</div>

			";

		}
	}
*/



	echo $out;

	?>