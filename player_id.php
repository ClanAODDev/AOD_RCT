<?php

if (!isset($_GET) || (isset($_GET['personaId']) && isset($_GET['username']))) {

	echo "No arguments provided, or too many provided. Need user or personaId.";

} else {

	if (isset($_GET['username'])) {
		$name = $_GET['username'];

		$url = "http://api.bf4stats.com/api/playerInfo?plat=pc&name={$name}";
		$json = file_get_contents($url);
		$data = json_decode($json);
		$player_id = $data->player->id;

		echo "<h2>Fetched player id: {$player_id}</h2>";

	} else if (isset($_GET['personaId'])) {
		$player_id = $_GET['personaId'];
	}

	$new_url = "http://battlelog.battlefield.com/bf4/warsawbattlereportspopulate/{$player_id}/2048/1/";
	$json = file_get_contents($new_url);
	$data = json_decode($json);

	$reports = $data->data->gameReports;

	foreach ($reports as $report) {
		$date = DateTime::createFromFormat('U', $report->createdAt)->format('M d');
		echo "{$report->gameReportId}<br />{$report->name}<br />{$date}<br /><br />";
	}
	
}

?>