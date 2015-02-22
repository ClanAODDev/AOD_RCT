<?php

/**
 * Division Structure Generator
 * Generates a BB-code division structure based on current database data
 *
 * Hardcoded values for BF division. Could be used to generate other division
 * structures later, but not pertinent at the moment.
 */

include "config.php";
include "application/lib.php";

// BF division = 2
$game = 2;

// colors
$division_leaders_color = "#00FF00";
$platoon_leaders_color = "#00FF00";
$squad_leaders_color = "#FFA500";

$div_name_color = "#FF0000";
$platoon_num_color = "#FF0000";
$platoon_pos_color = "#40E0D0";

// misc settings
$min_num_squad_leaders = 2;


// game icons
$bf4_icon = "[img]http://i.imgur.com/WjKYT85.png[/img]";
$bfh_icon = "[img]http://i.imgur.com/L51wBk8.png[/img]";




/**
 * build output
 */


// header
$out = "[table='width: 1100']";
$i = 1;
$out .= "[tr][td]";

// banner
$out .= "[center][img]http://i.imgur.com/iWpjGZG.png[/img][/center]<br />";



/**
 * ---------------------------
 * ------division leaders-----
 * ---------------------------
 */


$out .= "<br /><br />[center][size=5][color={$div_name_color}][b][i][u]Division Leaders[/u][/i][/b][/color][/size][/center]<br />";
$out .= "[center][size=4]";
$divleaders = get_division_ldrs($game);

foreach ($divleaders as $leader) {
	$aod_url = "[url=" . CLANAOD . $leader['forum_id'] . "]";
	$bl_url = "[url=" . BATTLELOG . $leader['battlelog_name']. "]";
	$out .= "{$aod_url}[color={$division_leaders_color}]{$leader['rank']} {$leader['forum_name']}[/url]{$bl_url}  {$bf4_icon}[/url][/color] - {$leader['position_desc']}<br />";
}

$out .= "[/size][/center]<br /><br />";









/**
 * ---------------------------
 * -----general sergeants-----
 * ---------------------------
 */


$genSgts = get_general_sergeants($game);
$out .= "[center][size=3][color={$platoon_pos_color}]General Sergeants[/color]<br />";
foreach ($genSgts as $sgt) {
	$aod_url = "[url=" . CLANAOD . $sgt['forum_id'] . "]";
	$bl_url = "[url=" . BATTLELOG . $sgt['battlelog_name']. "]";
	$out .= "{$aod_url}{$sgt['rank']} {$sgt['forum_name']}[/url]{$bl_url}  {$bf4_icon}[/url]<br />";
}
$out .= "[/size][/center]";

$out .= "[/td][/tr][/table]";







/**
 * ---------------------------
 * ---------platoons----------
 * ---------------------------
 */



$out .= "<br /><br />[table='width: 1200']";

$platoons = get_platoons($game);
foreach ($platoons as $platoon) {
	if ($i == 1) {
		$out .= "[tr]";
		$out .= "[td]";
	} else {
		$out .= "[td]";
	}


	$out .= "[size=5][color={$platoon_num_color}]Platoon {$i}[/color][/size] <br />[i][size=3]{$platoon['platoon_name']}[/size][/i]<br /><br />";

	// platoon leader
	$leader = get_member($platoon['leader_id']);
	$aod_url = "[url=" . CLANAOD . $leader['member_id'] . "]";
	$bl_url = "[url=" . BATTLELOG . $leader['battlelog_name']. "]";
	$out .= "{$aod_url}[size=3][color={$platoon_pos_color}]Platoon Leader[/color]<br />[color={$platoon_leaders_color}]{$leader['rank']} {$leader['forum_name']}[/color][/size][/url]{$bl_url}  {$bf4_icon}[/url]<br /><br />";
	
	// squad leaders
	$squadleaders = get_squad_leaders($game, $platoon['platoon_id'], true);

	$mcount = 0;
	foreach ($squadleaders as $sqdldr) {

		$aod_url = "[url=" . CLANAOD . $sqdldr['member_id'] . "]";
		$bl_url = "[url=" . BATTLELOG . $sqdldr['battlelog_name']. "]";
		$out .= "[size=3][color={$platoon_pos_color}]Squad Leader[/color]<br />{$aod_url}[color={$squad_leaders_color}]{$sqdldr['rank']} {$sqdldr['name']}[/color][/url]{$bl_url}  {$bf4_icon}[/url][/size]<br />";

		// squad members
		$squadmembers = get_my_squad($sqdldr['member_id'], true);
		$out .= "[size=1][list=1]";

		foreach ($squadmembers as $member) {
			$aod_url = "[url=" . CLANAOD . $member['member_id'] . "]";	
			$bl_url = "[url=" . BATTLELOG . $member['battlelog_name']. "]";
			$out .= "[*]{$aod_url}{$member['rank']} {$member['forum_name']}[/url]{$bl_url}  {$bf4_icon}[/url]<br />";
		}

		$out .= "[/list][/size]<br />";
		$mcount++;
	}

	if ($mcount < $min_num_squad_leaders) {
		// minimum of 2 squad leaders per platoon
		$min_num_squad_leaders = ($min_num_squad_leaders < 2) ? 2 : $min_num_squad_leaders;
		for ($mcount = $mcount; $mcount < $min_num_squad_leaders; $mcount++)
			$out .= "[size=3][color={$platoon_pos_color}]Squad Leader[/color]<br />[color={$squad_leaders_color}]TBA[/color][/size]<br />";
	}

	$out .= "<br /><br />";


	/**
	 * ---------------------------
	 * ----general population-----
	 * ---------------------------
	 */

	$genpop = get_gen_pop($platoon['platoon_id'], true);
	$out .= "[size=3][color={$platoon_pos_color}]Members[/color][/size]<br />[size=1]";
	foreach ($genpop as $member) {
		$bl_url = "[url=" . BATTLELOG . $member['battlelog_name']. "]";
		$aod_url = "[url=" . CLANAOD . $member['member_id'] . "]";
		$out .= "{$aod_url}{$member['rank']} {$member['forum_name']}[/url]{$bl_url}  {$bf4_icon}[/url]<br />";

	}

	$out .= "[/size]";
	$out .= "[/td]";

	$i++;

}
// end last platoon
$out .= "[/tr][/table]<br /><br />";



/**
 * ---------------------------
 * --------part timers--------
 * ---------------------------
 */
$i = 1;

$out .= "<br />[table='width: 1100']";
$out .= "[tr][td]<br />[center][size=3][color={$platoon_pos_color}][b]Part Time Members[/b][/color][/size][/center][/td][/tr]";
$out .= "[/table]<br /><br />";


$out .= "[table='width: 1100']";
$out .= "[tr][td][center]";


$partTimers = get_part_timers($game);

foreach ($partTimers as $member) {

	if ($i % 10 == 0) {
		$out .= "[/td][td]";
	}
	$bl_url = "[url=" . BATTLELOG . $member['battlelog_name']. "]";
	$aod_url = "[url=" . CLANAOD . $member['member_id'] . "]";
	$out .= "{$aod_url}AOD_{$member['forum_name']}[/url]{$bl_url}  {$bf4_icon}[/url]<br />";

	$i++;
	
}

$out .= "[/center][/td]";

$out .= "[/tr][/table]<br /><br />";






/**
 * ---------------------------
 * -----------LOAS------------
 * ---------------------------
 */
$i = 1;

$out .= "<br />[table='width: 1100']";
$out .= "[tr][td]<br />[center][size=3][color={$platoon_pos_color}][b]Leaves of Absence[/b][/color][/size][/center][/td][/tr]";
$out .= "[/table]<br /><br />";


$out .= "[table='width: 1100']";
$out .= "[tr][td][center]";


$loas = get_leaves_of_absence($game);
foreach ($loas as $member) {
	$date_end = date("M d, Y", strtotime($member['date_end']));
	$aod_url = "[url=" . CLANAOD . $member['member_id'] . "]";
	$out .= "{$aod_url}{$member['rank']} {$member['forum_name']}[/url]<br />[b]Ends[/b] {$date_end}<br />{$member['reason']}<br /><br />";

	$i++;
	
}

$out .= "[/center][/td]";

$out .= "[/tr][/table]";




echo $out;