<?php

include('../lib.php');

$out = NULL;

if (isset($_GET['player']) && (isset($_GET['game']))) {

	$player = trim($_GET['player']);
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
				<li class=\"list-group-item thread\">{$title} <i class='fa fa-copy copy-button-rct text-primary' title='Copy link to clipboard' href='#' data-clipboard-text='{$thread}'></i>";
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
		echo "<span class='text-danger'>You forgot to provide a user!</span>";
	}

} 

?>

<script src='/public/js/check_threads.js'></script>