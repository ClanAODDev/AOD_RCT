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

// count expired
$obligAlerts = NULL;
$loa_expired = count_expired_loas($user_game);
if ($loa_expired > 0) {
	$obligAlerts = "<div class='alert alert-info'><p><i class='fa fa-exclamation-triangle'></i> Your division has ({$loa_expired}) expired leaves of absence which need to be handled.</p></div>";
}

// revoke power?
$revokeBtn = NULL;
$ploaTable = NULL;

if ($userRole >= 2) {
	$revokeBtn = "<button class='btn btn-danger revoke-loa-btn' title='Revoke LOA'><i class='fa fa-trash-o'></i></button>";
}

// fetch leaves of absence
$appLoas = get_approved_loas($user_game);
$pendLoas = get_pending_loas($user_game);



// do we have any pending leaves of absence?
if (count($pendLoas)) {
	foreach ($pendLoas as $member) {
		$date_end = date("M d, Y", strtotime($member['date_end']));
		$expired = ( strtotime($date_end) < strtotime('now')) ? true : false;
		$status_icon =  "<h4><span class='label bg-warning'><i class='fa fa-clock-o' title='Pending'></i> Pending</span></h4>";
		$contact = "<a class='btn btn-default btn-sm popup-link' href='" . PRIVMSG . "{$member['member_id']}'>PM</a>";
		$approve = "<a class='btn btn-success btn-sm approve-loa-btn' href='#'>Approve</a>";

		$ploaList .= "
		<tr data-id='{$member['member_id']}'>
			<td>{$member['forum_name']}</td> 
			<td>{$member['reason']}</td>
			<td>{$date_end}</td>
			<td class='text-center' style='vertical-align: middle;'>{$status_icon}</td>
			<td class='text-right loa-actions' style='opacity: .2;'><div class='btn-group'> {$contact} {$approve}</div></td>
		</tr>";

		$i++;
	}

	$ploaTable = "
	<div class='panel panel-default margin-top-20' id='pending-loas'>
		<div class='panel-heading'>Pending Leaves of Absence</div>
		<table class='table table-striped table-hover' id='ploas'>
			<thead>
				<tr>
					<th>Member name</th>
					<th>Reason</th>
					<th>End Date</th>
					<th class='text-center'>Status</th>
				</tr>
			</thead>
			<tbody>
				{$ploaList}
			</tbody>
		</table>
	</div>";

}



// do we have any active leaves of absence?
if (count($appLoas)) {
	foreach ($appLoas as $member) {
		$date_end = date("M d, Y", strtotime($member['date_end']));
		$expired = ( strtotime($date_end) < strtotime('now')) ? true : false;
		$date_end = ($expired) ? "<span class='text-danger' title='Expired'>{$date_end}</span>" : $date_end;
		$status_icon = ($expired) ? "<h4><span class='label bg-danger'><i class='fa fa-times-circle' title='Expired'></i> Expired</span></h4>" : "<h4><span class='label bg-success'><i class='fa fa-check' title='Active'></i> Active</span></h4>";
		$contact = "<a class='btn btn-default btn-sm popup-link' href='" . PRIVMSG . "{$member['member_id']}'>PM</a>";

		$loaList .= "
		<tr data-id='{$member['member_id']}'>
			<td>{$member['forum_name']}</td> 
			<td>{$member['reason']}</td>
			<td>{$date_end}</td>
			<td class='text-center' style='vertical-align: middle;'>{$status_icon}</td>
			<td class='text-right loa-actions' style='opacity: .2;'><div class='btn-group'>{$contact} {$revokeBtn}</div></td>
		</tr>";

		$i++;
	}

}

// header section
$out = "

<div class='container fade-in'>
	{$breadcrumb}

	<div class='page-header'>
		<h1><strong>Manage</strong> <small>Leaves of Absence</small>{$game_icon}</h1>
	</div>
	{$obligAlerts}";


	// pending loas
	$out .= $ploaTable;


	// current loas
	$out .= "
	<div class='alert hide loa-alerts'></div>
	<div class='panel panel-default margin-top-20' id='active-loas'>
		<div class='panel-heading'>Approved Leaves of Absence</div>
		<table class='table table-striped table-hover' id='loas'>
			<thead>
				<tr>
					<th>Member name</th>
					<th>Reason</th>
					<th>End Date</th>
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
	<div class='panel panel-default margin-top-20'>

		<div class='panel-heading'>Add New Leave of Absence</div>
		<table class='table'>
			<tbody>
				<tr>
					<form id='loa-update' action='#'>
						<td><input type='number' class='form-control' name='id' placeholder='Member id' required></input></td>
						<td><input type='date' class='form-control' name='date' required></input></td>
						<td><select class='form-control' name='reason' required><option>Military</option><option>School</option><option>Work</option><option>Medical</option><option>Personal</option></select></td>
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




