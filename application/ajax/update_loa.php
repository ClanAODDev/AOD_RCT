<?php
session_start();
include "../lib.php";

$id = $_POST['id'];
$member_id = $member_info['forum_id'];



	// removing loa
if (isset($_POST['remove'])) {

	if ($userRole < 2) {
		$data = array('success' => false, 'message' => "You are not authorized to perform that action.");

	} else {


		// revoking an LOA
		$id = $_POST['id'];

		if ( $revoked = ( revoke_loa($id) ) ) {
			if ( $revoked['success'] == false ) {
				$data = array('success' => false, 'message' => $revoked['message']);
			} else {
				$data = array('success' => true, 'message' => "Leave of absence successfully removed.");
			}
		}

	}



	// approving an LOA
} else if (isset($_POST['approve'])) {

	if ($userRole < 2) {
		$data = array('success' => false, 'message' => "You are not authorized to perform that action.");

	} else {


		// is LOA member id the same as user member id?
		if ($member_id != $id) {
			if ( $approved = approve_loa($id, $member_id) ) {
				$data = array('success' => true, 'message' => "Leave of absence successfully approved.");
			} else {
				$data = array('success' => false, 'message' => $loa['message']);
			}
		} else {
			$data = array('success' => false, 'message' => 'You can\'t approve your own leave of absence!');
		}

	}


	// adding an LOA	
} else {

	$data = NULL;
	$date = date('Y-m-d', strtotime($_POST['date']));
	$reason = $_POST['reason'];
	$comment = htmlentities($_POST['comment'], ENT_QUOTES);
	$name = get_member_name($id);

		// validate member id and get name
	if ($name != false) {


		if (strtotime($date) > strtotime('now')) {

				// validate submission
			if ( $loa = ( addLoa($id, $date, $reason, $comment) ) ) {

					// if submission failed
				if ( $loa['success'] == false ) {
					$data = array('success' => false, 'message' => $loa['message']);
				} else {
					$data = array('success' => true, 'Request successfully submitted!', 'id' => $id, 'name' => $name, 'date' => date('M d, Y', strtotime($date)), 'reason' => $reason);
				}
			} else {
				$data = array('success' => false, 'message' => $loa['message']);
			}

		} else {
			$data = array('success' => false, 'message' => "Date cannot be before today's date.");
		}

	} else {
		$data = array('success' => false, 'message' => 'Invalid member id');
	}
}


echo json_encode($data);