<?php


if (!isset($_SESSION['secure_access']) || (isset($_SESSION['secure_access']) && $_SESSION['secure_access'] !== true)) { header("Location: /404/"); }

$out = NULL;
$my_squad = NULL;
$my_platoon = NULL;





// squad leader personnel view
if ($userRole == 1) {
	$squad_members = get_my_squad($forumId);
	$squadCount = ($squad_members) ? "(" . count($squad_members) . ")" : NULL;
	if ($squad_members) {
		foreach ($squad_members as $squad_member) {
			$name = ucwords($squad_member['forum_name']);
			$id = $squad_member['id'];
			$rank = $squad_member['rank'];
			$last_seen = formatTime(strtotime($squad_member['last_activity']));

			// visual cue for inactive squad members
			if (strtotime($last_seen) < strtotime('-30 days')) {
				$status = 'danger';
			} else if (strtotime($last_seen) < strtotime('-14 days')) {
				$status = 'warning';
			} else {
				$status = 'muted';
			}


			$my_squad .= "
			<a href='/member/{$id}' class='list-group-item'>{$rank} {$name}<small class='pull-right text-{$status}'>{$last_seen}</small></a>
			";
		}
	} else {
		$my_squad .= "<div class='panel-body'>Unfortunately it looks like you don't have any squad members!</div>";
	}






// platoon leader personnel view
} else if ($userRole == 2) {
	$squad_leaders = get_squad_leaders($user_game, $user_platoon);
	$platoonCount = ($squad_leaders) ? "(" . count(get_platoon_members($user_platoon)) . ")" : NULL;

	$i = 1;

	if ($platoonCount) {

		foreach ($squad_leaders as $squad_leader) {

			$rank = $squad_leader['rank'];
			$name = ucwords($squad_leader['name']);
			$squad_members = get_my_squad($squad_leader['member_id']);
			$last_seen = formatTime(strtotime($squad_leader['last_activity']));
			$status = lastSeenColored($last_seen);
			$squadCount = count($squad_members);

			$my_platoon .= "
			<a href='#collapseSquad{$i}' data-toggle='collapse' class='list-group-item active accordion-toggle' data-parent='#squads'>{$rank} {$name} ({$squadCount})</a>
			<div class='squad-group collapse' id='collapseSquad{$i}'>";

				foreach ($squad_members as $squad_member) {
					$rank = $squad_member['rank'];
					$id = $squad_member['id'];
					$name = ucwords($squad_member['forum_name']);
					$last_seen = formatTime(strtotime($squad_member['last_activity']));
					$status = lastSeenColored($last_seen);

					$my_platoon .= "<a href='/member/{$id}' class='list-group-item'>{$rank} {$name}<small class='pull-right text-{$status}'>{$last_seen}</small></a>";
				}

				$my_platoon .= "</div>";
				$i++;

			}

		} else {
			$my_platoon .= "<div class='panel-body'>Unfortunately it looks like you don't have any platoon members!</div>";
		}

	}

	// add general population to list items
	$gen_pop = get_gen_pop($user_platoon);
	$genPopCount = count($gen_pop);
	$my_platoon .= "
	<a href='#collapseSquad{$i}' data-toggle='collapse' class='list-group-item active accordion-toggle' data-parent='#squads'>General Population ({$genPopCount})</a>
	<div class='squad-group collapse' id='collapseSquad{$i}'>";

		foreach ($gen_pop as $gen_member) {
			$rank = $gen_member['rank'];
			$id = $gen_member['id'];
			$name = ucwords($gen_member['forum_name']);
			$last_seen = formatTime(strtotime($gen_member['last_activity']));
			$status = lastSeenColored($last_seen);

			$my_platoon .= "<a href='/member/{$id}' class='list-group-item'>{$rank} {$name}<small class='pull-right text-{$status}'>{$last_seen}</small></a>";
		}
		$my_platoon .= "</div>";

		?>