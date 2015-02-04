<?php

if (!isset($_SESSION['secure_access']) || (isset($_SESSION['secure_access']) && $_SESSION['secure_access'] !== true)) { header("Location: /404/"); }


/**
 * handling inactive members
 * depending on user role
 */
if ($params['page'] == "inactive") {

	$out = NULL;
	$my_squad = NULL;
	$inactive_list = NULL;
	$flagged_copy = "[list]";

	switch ($userRole) {

		case isDev():
		$type = "div";
		$id = $user_game;
		break;

		case 1: 
		$type = "sqd";
		$id = $forumId;
		break;
		case 2:
		$type = "plt";
		$id = $user_platoon;
		break;
		case 3:
		$type = "div";
		$id = $user_game;
		break;
		default:
		$type = "div";
		$id = $user_game;
		break;
	}

	$flagged_inactives = get_my_inactives($id, $type, true);
	$flaggedCount = (count($flagged_inactives)) ? count($flagged_inactives) : 0;
	foreach ($flagged_inactives as $member) {
		
		$last_seen = formatTime(strtotime($member['last_activity']));
		$joined = date("Y-m-d", strtotime($member['join_date']));
		$name = ucwords($member['forum_name']);
		$updatedBy = get_forum_name($member['flagged_by']);



		$flagged_copy .= "[*]" . ucwords($member['forum_name']) . " - " . CLANAOD . "{$member['member_id']} - " . BATTLELOG . "{$member['battlelog_name']}"; 

		$aod_games = count_aod_games($member['id'], date("Y-m-d"), date(strtotime('-30 days')));

		// visual cue for inactive squad members
		if (strtotime($last_seen) < strtotime('-30 days')) {
			$status = 'danger';
		} else if (strtotime($last_seen) < strtotime('-14 days')) {
			$status = 'warning';
		} else {
			$status = 'muted';
		}

		$inactive_flagged .= "
		<li class='list-group-item clearfix' data-user-id='{$member['id']}' data-member-id='{$member['member_id']}'>
			<div class='col-xs-1'><img src='/public/images/grab.svg' style='width: 8px; opacity: .20;' /></div>
			<div class='col-xs-2'>{$member['rank']} {$name}</div>
			<div class='col-xs-3 text-{$status} text-center'>Seen {$last_seen}</div>
			<div class='col-xs-3 removed-by text-center text-muted'>Flagged by {$updatedBy}</div>
			<div class='col-xs-3 actions btn-group'><span class='pull-right'><a href='http://www.clanaod.net/forums/private.php?do=newpm&u={$member['member_id']}' class='popup-link btn btn-default btn-xs'><i class='fa fa-comment'></i> PM</a> <button class='btn btn-default btn-xs view-profile'><i class='fa fa-user'></i> View Profile</button></span> 
			</div>
		</li>
		";
	}

	$flagged_copy .= "[/list]";

	$inactive_ids = array();
	$inactives = get_my_inactives($id, $type);
	$inactiveCount = (count($inactives)) ? count($inactives) : 0;
	foreach ($inactives as $member) {

		$inactive_ids[] = $member['member_id'];
		$last_seen = formatTime(strtotime($member['last_activity']));
		$joined = date("Y-m-d", strtotime($member['join_date']));
		$name = ucwords($member['forum_name']);

		$aod_games = count_aod_games($member['id'], date("Y-m-d"), date(strtotime('-30 days')));

		// visual cue for inactive squad members
		if (strtotime($last_seen) < strtotime('-30 days')) {
			$status = 'danger';
		} else if (strtotime($last_seen) < strtotime('-14 days')) {
			$status = 'warning';
		} else {
			$status = 'muted';
		}

		$inactive_list .= "
		<li class='list-group-item clearfix' data-user-id='{$member['id']}' data-member-id='{$member['member_id']}'>
			<div class='col-xs-1'><img src='/public/images/grab.svg' style='width: 8px; opacity: .20;' /></div>
			<div class='col-xs-2'>{$member['rank']} {$name}</div>
			<div class='col-xs-3 text-{$status} text-center'>Seen {$last_seen}</div>
			<div class='col-xs-3 removed-by text-center text-muted'></div>
			<div class='col-xs-3 actions btn-group'><span class='pull-right'><a href='http://www.clanaod.net/forums/private.php?do=newpm&u={$member['member_id']}' class='popup-link btn btn-default btn-xs'><i class='fa fa-comment'></i> PM</a> <button class='btn btn-default btn-xs view-profile'><i class='fa fa-user'></i> View Profile</button></span> 
			</div>
		</li>";
	}

	// build inactive ids for PM function
	$inactive_ids = implode("&u[]=", $inactive_ids);

	$breadcrumb = "
	<ul class='breadcrumb'>
		<li><a href='/'>Home</a></li>
		<li class='active'>Manage inactive players</li>
	</ul>";

	$out .= "
	<div class='container fade-in'>
		<div class='row'>{$breadcrumb}</div>

		<div class='page-header'>
			<h2><strong>Manage inactive players</strong></h2>
		</div>
		<p>Inactive members are pruned on a monthly basis. Use this tool to manage members who are considered inactive, that is, their last forum activity (login or otherwise) exceeds 30 days. In order to ensure your subordinate members receive fair warning, you must <strong>make every possible attempt</strong> to get this user back in good standing with the clan. Once all efforts have been exhausted, flag the member for removal by adding them to the 'flag for removal' list. </p>
		<p>A member who returns, or corrects their inactivity will automatically be removed from this list, as long as they return before the end of the clean-up.</p>";



		$out .= "
		<div class='margin-top-50'></div>		
		<div class='page-header'>
			<h3>List Management
				<small class='text-muted'> To flag a member, drag them from the inactive list to the \"flagged\" list.</small></h3>
			</div>";

			if (count($inactives) || count($flagged_inactives)) {

				if (count($flagged_inactives)) {

					// flagged inactives
					$out .="
					<div class='row flagged-section'>
						<div class='col-md-12'>

							<div class='panel panel-danger'>
								<div class='panel-heading'><i class='fa fa-trash-o fa-lg'></i> Members flagged for removal <span class='flagCount pull-right badge'>{$flaggedCount}</span></div>
								<ul class='sortable striped-bg' id='flagged-inactives' style='overflow-y: auto; max-height: 193px;'>
									{$inactive_flagged}
								</ul>
								<div class='panel-footer clearfix'><button type='button' class='copy-button btn btn-default tool pull-right' 	title='Copy to clipboard' data-clipboard-text='{$flagged_copy}'><i class='fa fa-copy'></i> Copy player list</button>
								</div>

							</div>
						</div>

					</div>";

				} else {

					$out .="
					<div class='row'>
						<div class='col-md-12'>
							<div class='panel panel-danger'>
								<div class='panel-heading'><i class='fa fa-trash-o fa-lg'></i> Members flagged for removal <span class='flagCount pull-right badge'>{$flaggedCount}</span></div>
								<ul class='sortable striped-bg' id='flagged-inactives' style='overflow-y: auto; max-height: 193px;'>
								</ul>

							</div>
						</div>
					</div>";
				}
				

				//if (count($inactives)) {

					// inactive members not yet flagged
				$out .="
				<div class='row inactives-section'>
					<div class='col-md-12'>

						<div class='panel panel-info'>

							<div class='panel-heading'><i class='fa fa-clock-o fa-lg'></i> Your inactive members <span class='inactiveCount pull-right badge'>{$inactiveCount}</span></div>

							<ul class='sortable inactive-list striped-bg' id='inactives' style='overflow-y: auto; max-height: 193px;'>
								{$inactive_list}
							</ul>
							<div class='panel-footer clearfix'><a href='http://www.clanaod.net/forums/private.php?do=newpm&u[]={$inactive_ids}' class='mass-pm-btn pull-right popup-link btn btn-default'><i class='fa fa-users'></i> Mass PM Players</a></div>
						</div>
					</div>
				</div>";




			// no members flagged or inactive... yet
			} else {

				$out .="
				<div class='row margin-top-50'>
					<div class='col-md-12 '>

						<div class='panel panel-success'>

							<div class='panel-heading'><i class='fa fa-check'></i> Congratulations! <span class='inactiveCount pull-right badge'>{$inactiveCount}</span></div>
							<ul class='striped-bg'>
								<li class='list-group-item'>None of your members are currently inactive!</li>
							</ul>
						</div>
					</div>
				</div>";
			}


			$out .= "</div>";


		// end container
			$out .="
		</div>

		<script type='text/javascript' src='/public/js/manage.js'></script>";


	}

	echo $out;


	?>