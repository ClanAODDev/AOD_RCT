<?php

include "../lib.php";

$data = NULL;
$id = $_POST['id'];
$date = date('Y-m-d', strtotime($_POST['date']));
$reason = $_POST['reason'];
$name = get_member_name($id);

// validate member id and get name
if ($name != false) {

	// validate submission
	if ( $loa = ( updateLoa($id, $date, $reason) ) ) {

		// if submission failed
		if ($loa['success'] == false) {
			$data = array('success' => false, 'message' => $loa['message']);
			
		} else {
			$data = array('success' => true, 'name' => $name, 'date' => date('M d, Y', strtotime($date)), 'reason' => $reason);
			
		}

	} else {
		$data = array('success' => false, 'message' => $loa['message']);
		
	}

} else {
	$data = array('success' => false, 'message' => 'Invalid member id');
	
}

echo json_encode($data);