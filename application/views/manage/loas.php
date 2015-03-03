<?php

if (!isset($_SESSION['secure_access']) || (isset($_SESSION['secure_access']) && $_SESSION['secure_access'] !== true)) { header("Location: /404/"); }

$root = ROOT;

$breadcrumb = "
<ul class='breadcrumb'>
	<li><a href='/'>Home</a></li>
	<li class='active'>Manage Leaves of Absence</li>
</ul>";

$game_info = get_game_info($user_game);
$game_icon = strtolower($game_info['short_name']);
$game_icon = "<img class='pull-right' src='/public/images/game_icons/large/{$game_icon}.png'/>";

// fetch leaves of absence
$loas = get_leaves_of_absence($user_game);
$loaList = NULL;

foreach ($loas as $member) {
	$date_end = date("M d, Y", strtotime($member['date_end']));
	$expired = ( strtotime($date_end) < strtotime('now')) ? true : false;
	$date_end = ($expired) ? "<span class='text-danger' title='Expired'>{$date_end}</span>" : $date_end;
	$status_icon = ($expired) ? "<i class='fa fa-times-circle text-danger fa-lg' title='Expired'></i>" : "<i class='fa fa-check text-success fa-lg' title='Active'></i>";


	$loaList .= "
	<tr data-id='{$member['member_id']}'>
		<td>{$member['forum_name']}</td> 
		<td>{$date_end}</td>
		<td>{$member['reason']}</td>
		<td class='text-center'>{$status_icon}</td>
	</tr>";

	$i++;

}


// header section
$out = "
<div class='container fade-in'>
	{$breadcrumb}

	<div class='page-header'>
		<h2><strong>Manage</strong> <small>Leaves of Absence</small>{$game_icon}</h2>
	</div>";

	// current loas
	$out .= "
	<div class='alert hide'></div>
	<div class='panel panel-primary margin-top-20'>
		<div class='panel-heading'>Current Leaves of Absence</div>
		<table class='table table-striped table-hover' id='loas'>
			<thead>
				<tr>
					<th>Member name</th>
					<th>End Date</th>
					<th>Reason</th>
					<th class='text-center'>Status</th>
				</tr>
			</thead>
			<tbody>
				{$loaList}
			</tbody>
		</table>
	</div>";


	// add loa form
	$out .= "
	<div class='panel panel-primary margin-top-50'>

		<div class='panel-heading'>Add New Leave of Absence</div>
		<table class='table'>
			<tbody>
				<tr>
					<form id='loa-update' action='#'>
						<td><input type='number' class='form-control' name='id' placeholder='Member id' required></input></td>
						<td><input type='date' class='form-control' name='date' required></input></td>
						<td><input type='text' class='form-control' name='reason' placeholder='Brief reason' required></input></td>
						<td class='text-center'><button class='btn btn-success' type='submit'>ADD <i class='fa fa-plus-circle'></i></button></td>
					</form>
				</tr>
			</tbody>
		</table>
	</div>";


	$out .= "
</div>";


echo $out;

?>

<script type="text/javascript" src="/public/js/manage.js"></script>



