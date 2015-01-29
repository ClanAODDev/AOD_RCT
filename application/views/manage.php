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

	switch ($userRole) {
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
	foreach ($flagged_inactives as $member) {
		$last_seen = formatTime(strtotime($member['last_activity']));
		$joined = date("Y-m-d", strtotime($member['join_date']));
		$name = ucwords($member['forum_name']);
		$updatedBy = get_forum_name($member['flagged_by']);

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
			<div class='col-xs-3'><i class='fa fa-search view-profile'></i> {$member['rank']} {$name}</div>
			<div class='col-xs-3 text-{$status}'>Seen {$last_seen}</div>
			<div class='col-xs-4 removed-by'>Removed by {$updatedBy}</div>
			<div class='col-xs-2 text-right '><img src='/public/images/grab.svg' style='width: 8px; opacity: .20;' /></div>
			";
		}


		$inactives = get_my_inactives($id, $type);
		foreach ($inactives as $member) {
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
				<div class='col-xs-3'><i class='fa fa-search view-profile'></i> {$member['rank']} {$name}</div>
				<div class='col-xs-3 text-{$status}'>Seen {$last_seen}</div>
				<div class='col-xs-4 removed-by'>Removed by</div>

				<div class='col-xs-2 text-right '><img src='/public/images/grab.svg' style='width: 8px; opacity: .20;' /></div>
				";
			}


			$breadcrumb = "
			<ul class='breadcrumb'>
				<li><a href='/'>Home</a></li>
				<li class='active'>Manage Inactive Members</li>
			</ul>
			";


			$out .= "


			<div class='container fade-in'>
				<div class='row'>{$breadcrumb}</div>


				<div class='row'>
					<div class='platoon-name page-header'>
						<h2><strong>Manage Inactive Members</strong></h2>
					</div>
					<p>Inactive members are pruned on a monthly basis. Use this tool to view members who are considered inactive, that is, their last forum activity (login or otherwise) exceeds 30 days. In order to ensure your subordinate members receive fair warning, you must make every possible attempt to get this user back in good standing with the clan. Once all efforts have been exhausted, flag the member for removal by adding them to the 'flag for removal' list. </p>
					<p>Flagged members will be processed for removal by the date specified by the leader who is administering the inactivity clean up.</p>
				</div>";


				$out .= "<div class='row'><div class='alert alert-warning'><strong>Note: </strong> Lifting inactive flag is not possible yet. Once you have flagged an individual, it is not possible to remove them at this time. Be sure only to flag those you mean to have removed.</div>";


				// flagged inactives
				$out .="
				<div class='row'>
					<div class='col-md-12'>

						<div class='page-header'><h3><i class='fa fa-trash-o text-danger'></i> Flagged for removal</h3></div>

						<ul class='sortable' id='flagged-inactives' style='overflow-y: auto; max-height: 400px;'>
							{$inactive_flagged}
						</ul>
					</div>

				</div>";


				// inactive members not yet flagged
				$out .="
				<div class='row'>
					<div class='col-md-12 '>
						
						<div class='page-header'><h3><i class='fa fa-clock-o text-warning'></i> Currently inactive players</h3></div>
						<ul class='sortable inactive-list' id='inactives' style='overflow-y: auto; max-height: 400px;'>
							{$inactive_list}
						</ul>
					</div>

				</div>";


				$out .= "</div>";



			// end container
				$out .="
			</div>";


		}

		echo $out;


		?>


		<style type="text/css">
			.sortable { list-style-type: none !important; margin: 0 !important; padding: 0; margin-bottom: 0px; -webkit-padding-start: 0px !important; margin-left: -30px; background-color: rgba(0,0,0,.01); min-height: 45px; }
			.sortable li { margin: 0px; cursor: move; display: block; }
			.ui-state-highlight { height: 2em; line-height: 1.2em; }
			#flagged-inactives, #flagged-inactives div { color: rgba(0,0,0,.50) !important; }
			.view-profile { cursor: pointer;}
		</style>


		<script type="text/javascript">

			$(".draggable").draggable({
				connectToSortable: 'ul',
				revert: 'invalid',
				scroll: true, scrollSensitivity: 100
			});



			$(".view-profile").click(function() {
				var userId = $(this).closest('.list-group-item').attr('data-user-id');
				location.href = "/member/" + userId;
			});

			var itemMoved,  targetplatoon, sourcePlatoon, action = null;
			$(".sortable").sortable({
				revert: true,
				connectWith: 'ul',
				placeholder: "ui-state-highlight",
				receive: function(event, ui) {
					itemMoved = $(ui.item).attr('data-member-id');
					targetList = $(this).attr('id');

					if (targetList == "flagged-inactives") {
						$(ui.item).find('.removed-by').show().html("Removed by you");
						action = 1;
						context = " flagged for removal."

					} else {
						$(ui.item).find('.removed-by').empty();
						context = " no longer flagged for removal."
						action = 0;
					}

					$.ajax({
						type: 'POST',
						url: '/application/controllers/update_flagged.php',
						data: {
							action: action,
							id: itemMoved
						},
						dataType: 'json',
						success: function(response) {

							if (response.success === false) {

								message = response.message;   
								$(".alert-box").stop().html("<div class='alert alert-danger'>" + message + "</div>").effect('highlight').delay(1000).fadeOut();     
							} else {

								message = "Player " + itemMoved + context;
								$(".alert-box").stop().html("<div class='alert alert-success'>" + message + "</div>").effect('highlight').delay(1000).fadeOut();
							}

							

						},

						// fail: function()
					});

				}
			});

$("#flagged-inactives").draggable('disable');
</script>