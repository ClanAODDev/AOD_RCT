<?php
header('Content-Type: image/png');
include("../../application/lib.php");

$im = imagecreatetruecolor(900,330);
$white = imagecolorallocate($im, 255, 255, 255);
$grey = imagecolorallocate($im, 128, 128, 128);
$black = imagecolorallocate($im, 255,255,255);
$im = imagecreatefrompng("../images/big-bg.png");

$text = "Battlefield Division";
$dateText = date('d M', strtotime('-30 days')) . "-" . date('d M');



// color
$r = 255;
$g = 255;
$b = 255;

$font = "../fonts/reaction.ttf";


// x value positions
$games_col_1 = 160;
$num_col_1 = 23;
$name_col_1 = 45;

$games_col_2 = 435;
$num_col_2 = 300;
$name_col_2 = 320;


/**
 * get data
 */

$daily = get_division_toplist("daily", 10);
$monthly = get_division_toplist("monthly", 10);




/**
 * create elements
 */

// date
imagettftext($im, 4, 0, 720, 240, imagecolorallocate($im, 80, 80, 80), $font, strtoupper($dateText));


// daily stats

$y = 65;
$i = 1;

imagettftext($im, 6, 0, $num_col_1, $y, imagecolorallocate($im, 80,80,80), $font, strtoupper("#"));
imagettftext($im, 6, 0, $name_col_1, $y, imagecolorallocate($im, 80,80,80), $font, strtoupper("Player"));
imagettftext($im, 6, 0, $games_col_1, $y, imagecolorallocate($im, 80,80,80), $font, strtoupper("AOD Games"));

foreach ($daily as $player) {
	$y = $y+17;
 	// number
	imagettftext($im, 6, 0, $num_col_1, $y, imagecolorallocate($im, 80, 80, 80), $font, "{$i}.");
	// name
	imagettftext($im, 6, 0, $name_col_1, $y, imagecolorallocate($im, $r, $g, $b), $font, substr(strtoupper($player['forum_name']), 0, 12));
	// games
	imagettftext($im, 6, 0, $games_col_1, $y, imagecolorallocate($im, $r, $g, $b), $font, "{$player['aod_games']}");
	$i++;

}


// monthly stats

$y = 65;
$i = 1;

imagettftext($im, 6, 0, $num_col_2, $y, imagecolorallocate($im, 80,80,80), $font, strtoupper("#"));
imagettftext($im, 6, 0, $name_col_2, $y, imagecolorallocate($im, 80,80,80), $font, strtoupper("Player"));
imagettftext($im, 6, 0, $games_col_2, $y, imagecolorallocate($im, 80,80,80), $font, strtoupper("AOD Games"));

foreach ($monthly as $player) {
	$y = $y+17;
 	// number
	imagettftext($im, 6, 0, $num_col_2, $y, imagecolorallocate($im, 80, 80, 80), $font, "{$i}.");
	// name
	imagettftext($im, 6, 0, $name_col_2, $y, imagecolorallocate($im, $r, $g, $b), $font, substr(strtoupper($player['forum_name']), 0, 12));
	// games
	imagettftext($im, 6, 0, $games_col_2, $y, imagecolorallocate($im, $r, $g, $b), $font, "{$player['aod_games']}");
	$i++;

}





imagepng($im);
imagepng($im, "../toplist-cache.png");
imagedestroy($im); 

/*

 */


?>