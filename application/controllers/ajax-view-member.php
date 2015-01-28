<?php

session_start();
require_once("../lib.php");

$out = NULL;
$data = NULL;
$platoons = NULL;
$squadLeaders = NULL;
$positions = NULL;

if ($_POST && $_POST['id']) {

	$member = get_member($_POST['id']);

	$forum_name = ucwords($member['forum_name']);
	$rank = $member['rank'];
	$battlelog_name = $member['battlelog_name'];
	$member_id = $member['member_id'];
	$id = $member['id'];
	$game_info = get_game_info($member['game_id']);
	$short_game_name = $game_info['short_name'];
	$game_name = $game_info['full_name'];
	$game_id = $game_info['id'];
	$platoon_id = $member['platoon_id'];
	$squadldr = $member['squad_leader_id'];
	$cur_position = $member['bf4_position_id'];

	switch ($userRole) {
		case 1:
		$allowPltAssignmentEdit = false;
		$allowSqdAssignmentEdit = false;
		$allowPosAssignmentEdit = false;
		break;

		case 2:
		$allowPltAssignmentEdit = false;
		$allowSqdAssignmentEdit = true;
		$allowPosAssignmentEdit = true;
		break;

		case 3:
		$allowPltAssignmentEdit = true;
		$allowSqdAssignmentEdit = true;
		$allowPosAssignmentEdit = true;
		break;
	}

	// allow developers to see all fields regardless of role
	if (isDev()) {
		$allowPltAssignmentEdit = true;
		$allowSqdAssignmentEdit = true;
		$allowPosAssignmentEdit = true;
	}

	// if assignment editing is allowed, show fields
	$assignmentPltFieldDisplay = ($allowPltAssignmentEdit) ? "block" : "none";
	$assignmentSqdFieldDisplay = ($allowSqdAssignmentEdit) ? "block" : "none";
	$assignmentPosFieldDisplay = ($allowPosAssignmentEdit) ? "block" : "none";

	// platoons and squads are based on game, for clarity
	$platoonArray = get_platoons($game_id);
	$squadleadersArray = get_squad_leaders($game_id);
	$positionsArray = get_positions($user_position);

	// build platoons
	if (count($platoonArray)) {
		foreach($platoonArray as $platoon) {
			$platoons .= "<option value='{$platoon['platoon_id']}'>{$platoon['platoon_name']}</option>";
		}
		$platoons .= "<option value='0'>None - Division Leader</option>";
	} else {
		$platoons = "<option>No platoons exist.</option>";
	}

	// build squad leaders
	if (count($squadleadersArray)) {
		foreach($squadleadersArray as $squadLeader) {
			$squadLeaders .= "<option value='{$squadLeader['member_id']}'>{$squadLeader['name']} - {$squadLeader['platoon_name']}</option>";
		}

		// add empty squad leader option
		$squadLeaders .= "<option value='0' selected>None (Gen Pop or Division Leader)</option>";
	} else {
		$squadLeaders = "<option>No squad leaders exist.</option>";
	}

	// build positions dropdown
	foreach ($positionsArray as $position) {
		$positions .= "<option value='{$position['id']}'>{$position['desc']}</option>";
	}


	$out .= "
	<div class='modal-header'>
		<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
		<h4 class='modal-title'>Editing {$rank} {$forum_name}</h4>
	</div>
	<form id='edit-form'>
		<div class='modal-body'>
			<div class='message alert' style='display: none;'></div>

			<input type='hidden' id='uid' name='uid' value='{$id}' />
			<input type='hidden' id='cur_plt' name='cur_plt' value='{$platoon_id}' />
			<input type='hidden' id='cur_sqd' name='cur_sqd' value='{$squadldr}' />
			<input type='hidden' id='cur_pos' name='cur_pos' value='{$cur_position}' />

			<div class='form-group'>
				<label for='forum_name' class='control-label'>Forum Name</label>
				<input type='text' class='form-control' id='forum_name' value='{$forum_name}'>
			</div>

			<div class='form-group'>
				<label for='member_id' class='control-label'>Forum ID</label>
				<input type='number' class='form-control' id='member_id' value='{$member_id}'>
			</div>


			<div class='form-group battlelog-group'>
				<label for='battlelog' class='control-label'>Battlelog Name</label>
				<input type='text' class='form-control' id='battlelog' value='{$battlelog_name}'>
			</div>

			<div class='form-group platoon-group' style='display: {$assignmentPltFieldDisplay}'>
				<label for='platoon' class='control-label'>Platoon</label>
				<select name='platoon' id='platoon' class='form-control'>{$platoons}</select>
			</div>

			<div class='form-group sqdldr-group' style='display: {$assignmentSqdFieldDisplay}'>
				<label for='sqdldr' class='control-label'>Squad Leader</label>
				<select name='sqdldr' id='sqdldr' class='form-control'>{$squadLeaders}</select>

			</div>

			<div class='form-group position-group' style='display: {$assignmentPosFieldDisplay}'>
				<label for='position' class='control-label'>Position</label>
				<select name='position' id='position' class='form-control'>{$positions}</select>
			</div>


		</div>
		<div class='modal-footer'>
			<button type='submit' class='btn btn-block btn-success'>Save Info</button> 
		</div>
	</form>
	<script src='/public/js/view.js'></script>";

} else {

	echo "Some error where we didn't get the id";
}

echo $out;

?>