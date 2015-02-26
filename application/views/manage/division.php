<?php
$breadcrumb = "
<ul class='breadcrumb'>
	<li><a href='/'>Home</a></li>
	<li class='active'>Manage Division</li>
</ul>";

$platoons = get_platoons($user_game);
$platoonOut = NULL;
$i = 0;

foreach ($platoons as $platoon) {
	$gen_pop = get_gen_pop($platoon['platoon_id'], true);
	$gen_count = count($gen_pop);

	$platoonOut .= "
	<div style='float: left; width: 230px;'>
		<div class='panel panel-default'>
			<div class='panel-heading'>{$platoon['platoon_name']} ({$gen_count})</div>
			<div class='panel-body'>
				<ul class='sortable' data-platoon-id='{$platoon['platoon_id']}' id='plt{$platoon['platoon_id']}'>";

					foreach($gen_pop as $player) {
						$platoonOut .= "<li class='ui-state-default draggable' data-id='{$player['member_id']}'>{$player['rank']} {$player['forum_name']}</li>";
					}

					$platoonOut .="
				</ul>
			</div>
		</div>
	</div>";


	$i++;

}

$out = "
<div class='container fade-in'>
	<div class='row'>{$breadcrumb}</div>

	<div class='page-header'>
		<h2><strong>Manage</strong> <small>Division Members</small></h2>
	</div>

	<p>Manage the structure of the division general population by dragging players to respective platoons. Dragging a member to a new platoon immediately saves that change. Platoons should generally be even, and redistribution should be done along with the affected platoon leaders. </p>";


	$out .= "
	<div class='page-header'>
		<h3 class='margin-top-50'><strong>Division General Population</strong></h3>
	</div>

	<div class='row' style='overflow-x: scroll;'>
		{$platoonOut}
	</div>
</div>

";


echo $out;

?>

<script type="text/javascript" src="/public/js/manage.js"></script>

</body>
</html>
