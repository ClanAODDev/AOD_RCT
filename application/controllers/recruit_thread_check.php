<?php

include('../lib.php');
$out = NULL;

if (isset($_GET['player']) && (isset($_GET['game']))) {

	$player = $_GET['player'];
	$game = $_GET['game'];

	if (!empty($_GET['player']) && (!empty($_GET['game']))) {  

		$success = "<span class=\"badge alert-success\"><i class=\"fa fa-check fa-lg\"></i></span>";
		$failure = "<span class=\"badge alert-danger\" title=\"User has not completed this step\"><i class=\"fa fa-times fa-lg\"></i></span>";


		$gameThreads = get_game_threads($game);

		// set up table
		$out .= "	
		<ul class=\"list-group thread-list text-left\">";

			foreach ($gameThreads as $gameThread) {

				$title = forceEmptyMessageIfNull($gameThread['thread_title']);
				$thread = forceEmptyMessageIfNull($gameThread['thread_url']);

				$status = checkThread($player, $thread);

				$out .= "
				<li class=\"list-group-item thread\">
					<a href=\"{$thread}\"  title='View forum thread' target=\"_blank\"><i class='fa fa-comment'></i> {$title}</a>";

					$out .= ($status) ? $success : $failure;

					$out .= "
				</li>";
			}


			$out .= "
		</ul>";

		$out .="
		<div class='text-left'>
		<span class=\"reload text-muted\" style=\"cursor: pointer;\">Refresh Thread Check <i class=\"fa fa-refresh glyphicon-xs\"></i></span>
		<span class='thread-status pull-right text-danger'></span>
		</div>
		";

		echo $out;
	} else {
		echo "<span class='text-danger'>Either you didn't provide a game or you forgot to provide a user.</span>";
	}

} 

?>