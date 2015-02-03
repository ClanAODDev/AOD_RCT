<?php
header('Content-Type: image/png');
include("../../application/lib.php");



$im = imagecreatetruecolor(900,900);
$white = imagecolorallocate($im, 255, 255, 255);
$grey = imagecolorallocate($im, 128, 128, 128);
$black = imagecolorallocate($im, 255,255,255);

//$im = imagecreatefrompng("red-stripe-bg.png");
$im = imagecreatefrompng("battlefield_bg.png");

$date_string = date('d M', strtotime('-30 days')) . "-" . date('d M');


$text = "Battlefield Division";
$subtext = "({$date_string})";


// settings
$y = 90;
$i = 1;

// color
$r = 255;
$g = 255;
$b = 255;

$font = "business.ttf";

imagettftext($im, 22, 0, 80, 57, imagecolorallocate($im, $r, $g, $b), "din-light.ttf", strtoupper($subtext));
imagettftext($im, 6, 0, 23, $y, imagecolorallocate($im, 187,96,39), $font, strtoupper("#"));
imagettftext($im, 6, 0, 45, $y, imagecolorallocate($im, 187,96,39), $font, strtoupper("Player"));
imagettftext($im, 6, 0, 170, $y, imagecolorallocate($im, 187,96,39), $font, strtoupper("AOD Games"));


$top10 = getTop10();

foreach ($top10 as $player) {

	$y = $y+20;


 	// number
	imagettftext($im, 6, 0, 23, $y, imagecolorallocate($im, $r, $g, $b), $font, "{$i}.");

	// name
	imagettftext($im, 6, 0, 45, $y, imagecolorallocate($im, $r, $g, $b), $font, strtoupper("{$player['forum_name']}."));

	// games
	imagettftext($im, 6, 0, 170, $y, imagecolorallocate($im, $r, $g, $b), $font, "{$player['aod_games']}");
	
	$i++;

}




function getTop10()
{

	global $pdo;

	if (dbConnect()) {

		try {
			$query = "SELECT forum_name, platoon.number, ( SELECT count(*) FROM activity WHERE activity.member_id = member.member_id AND activity.server LIKE 'AOD%' AND activity.datetime BETWEEN DATE_SUB(NOW(), INTERVAL 30 day) AND CURRENT_TIMESTAMP ) AS aod_games FROM member LEFT JOIN platoon ON member.platoon_id = platoon.id ORDER BY aod_games DESC LIMIT 10";

			$query = $pdo->prepare($query);
			$query->execute();
			$query = $query->fetchAll();

		}
		catch (PDOException $e) {
			return "ERROR:" . $e->getMessage();
		}
	}
	return $query;
}




imagepng($im);
imagedestroy($im); 

/*

 */


?>