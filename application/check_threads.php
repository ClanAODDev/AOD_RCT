<?php

include "lib.php";
$out = NULL;

if (isset($_GET['player']) && (isset($_GET['game']))) {

	$player = $_GET['player'];
	$game = $_GET['game'];

	$success = "<span class=\"badge alert-success\"><i class=\"glyphicon glyphicon-ok\"></i></span>";
	$failure = "<span class=\"badge alert-danger\" title=\"User has not completed this step\"><i class=\"glyphicon glyphicon-remove\"></i></span>";


	$gameThreads = get_game_threads($game);

	// set up table
	$out .= "	
	<ul class=\"list-group text-left\">";

		/*if (count($gameThreads) >2) {*/
			foreach ($gameThreads as $gameThread) {

				$title = forceEmptyMessageIfNull($gameThread['thread_title']);
				$thread = forceEmptyMessageIfNull($gameThread['thread_url']);

				$status = checkThread($player, $thread);

				$out .= "
				<li class=\"list-group-item\">
					<a href=\"{$thread}\"  title='View forum thread' target=\"_blank\"><i class='glyphicon glyphicon-comment'></i> {$title}</a>";

					$out .= ($status) ? $success : $failure;

					$out .= "
				</li>";
			}
		/*} else {
			$out .= "<div class=\"alert alert-danger\" role=\"alert\"><i class=\"glyphicon glyphicon-warning-sign\"></i> It looks like your division doesn't have any associated threads. They will need to be added to the database. It is also possible that your divis</div>";
		}*/


		$out .= "
	</ul>";

	/*if (count($gameThreads) >2) {*/
		$out .="
		<small class=\"pull-left text-muted\">Checks the  page of the latest post in a thread(last 25 replies) to see if recruit has posted.</small><span class=\"reload pull-right text-muted\" style=\"cursor: pointer;\"><small title='Refresh threads'>Refresh <i class=\"glyphicon glyphicon-refresh glyphicon-xs\"></i></small></span>
		";
	/*}*/

	echo $out;
} else {
	echo "Either you didn't provide a game or you forgot to provide a user.";
}

?>