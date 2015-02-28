<?php
#********************************************************************************
#
#	File Name:	refresh_server_activity_cron.php
#
#	Overview: 	This file reads a single AOD member's recent game activity
#               from BF4DB and stores it in a database table for later use.
#               It is scheduled as a cron job to run every 5 minutes, which
#               means that it will pull down the latest game activity for a
#               different AOD member every 5 minutes. This has the benefit of
#               not banging too hard on BF4DB so that they don't block us.
#
#   Crontab:
#
#    */5 * * * * /usr/bin/php -f /full_path/refresh_server_activity_cron.php >> /full_path/activity.log
#
#
#	Creation Date: 	11/7/2014
#		by: 		Sc0rp10n66	
#
#*******************************************************************************

require_once "config.php";

$right_now = new DateTime("now");

# Make a connection to the AOD database.
try {
	$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
	echo $e->getMessage()."\n";
	exit;
}

# Read the next member to pull activity for from the cron table.
try {
	$query = "SELECT next_member FROM cron";
	if (DEBUG_MODE) echo $query."\n";
	$query = $pdo->prepare($query);
	$query->execute();
	$query = $query->fetchAll();
} catch (PDOException $e) {
	echo $e->getMessage()."\n";
	exit;
}

$row = $query[0];
$next_member = $row['next_member'];

if (DEBUG_MODE) echo $next_member."\n";

# Read the member_id and bf4db_id from the member table.
try {
	$query = "SELECT member_id, bf4db_id FROM member WHERE id=".$next_member;
	if (DEBUG_MODE) echo $query."\n";
	$query = $pdo->prepare($query);
	$query->execute();
	$query = $query->fetchAll();
} catch (PDOException $e) {
	echo $e->getMessage()."\n";
	exit;
}

if (sizeof($query) == 0) {

	echo "No member with this ID: ".$next_member."\n";

} else {

	$row = $query[0];
	# Create the URL for the BF4DB site.
	$url = 'http://bf4db.com/players/'.$row['bf4db_id'].'/battlereports';
	# Use curl to read the page from BF4DB.
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	if(!$html = curl_exec($ch)) {
		echo curl_error($ch)."\n";
		exit;
	} else {
		if (DEBUG_MODE) echo $html."\n";

		# Use this fancy regex to find the servers and times played within the HTML.
		$regexp = "/<a href=\"http:\/\/bf4db\.com\/servers\/\d*\" title=\"([^\"]*)\">[^<]*<\/a>[^<]*<\/td>[^<]*<td\s+>[^<]*<abbr class=\"timeago\" title=\"([0-9-T:+]{25})\">/iU";
		if(preg_match_all($regexp, $html, $matches)) {

			# Go through each of the matches to create the query for insertion into the table.
			$query = "INSERT IGNORE INTO `activity` (`member_id`, `server`, `datetime`, `hash`) VALUES ";

			$len = count($matches[0]);
			for($i=0; $i < $len; $i++) {
				# This hash is computed from the member_id and time played to create a unique
				# value that we can use to only insert new records into the database.
				$hash = hash('sha256',$row['member_id'].$matches[2][$i]);
				$query .= "(".$row['member_id'].",'".$matches[1][$i]."','".$matches[2][$i]."','".$hash."')";

				if($i < $len-1) {
					$query .= ",";
				}
			}

			# Insert the new records into the activity table.
			try {
				if (DEBUG_MODE) echo $query."\n";
				$query = $pdo->prepare($query);
				$query->execute();
				
			} catch (PDOException $e) {
				echo $e->getMessage()."\n";
				exit;
			}
		
		}
	}
	curl_close($ch);

}

# Determine the id of the last member in the member table.
try {
	#$query = "SELECT COUNT(*) AS last FROM member";
	$query = "SELECT id from member ORDER BY id DESC";
	if (DEBUG_MODE) echo $query."\n";
	$query = $pdo->prepare($query);
	$query->execute();
	$query = $query->fetchAll();
} catch (PDOException $e) {
	echo $e->getMessage()."\n";
	exit;
}

$row = $query[0];
#$last = $row['last'];
$last = $row['id'];

if (DEBUG_MODE) echo $last."\n";

# Increment the id of the member to pull data for next, unless we
# just did the last member. In that case we will start over at 1.
if($next_member >= $last) {
	$new_next_member=1;
} else {
	$new_next_member=$next_member+1;
}

if (DEBUG_MODE) echo $new_next_member."\n";

# Record the new next_member id in the cron table.
try {
	$query = "UPDATE cron SET next_member=".$new_next_member." WHERE 1";
	if (DEBUG_MODE) echo $query."\n";
	$query = $pdo->prepare($query);
	$query->execute();
} catch (PDOException $e) {
	echo $e->getMessage()."\n";
	exit;
}

echo "Success: ".$next_member." -> ".$new_next_member." on ".$right_now->format('Y-m-d')." at ".$right_now->format('H:i')."\n";
?>
