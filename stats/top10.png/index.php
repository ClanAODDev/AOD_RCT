<?php
header('Content-Type: image/png');
include("../../application/lib.php");

$im = imagecreatetruecolor(900,330);

// color
$white = imagecolorallocate($im, 255, 255, 255);
$grey = imagecolorallocate($im, 128, 128, 128);
$black = imagecolorallocate($im, 255,255,255);
$orange = imagecolorallocate($im, 255,108,0);
$darkGrey = imagecolorallocate($im, 50,50,50);
$im = imagecreatefrompng("../images/big-bg.png");

$text = "Battlefield Division";
$dateText = date('d M', strtotime('-30 days')) . "-" . date('d M');

$tinyfont = "../fonts/copy0855.ttf";
$tinyboldfont = "../fonts/copy0866.ttf";
$bigfont = "../fonts/din-black.otf";

// x value positions
$games_col_1 = 160;
$num_col_1 = 23;
$name_col_1 = 45;

$games_col_2 = 435;
$num_col_2 = 300;
$name_col_2 = 320;

$total_percent_x = 615;
$total_percent_y = 160;


/**
 * get data
 */

$daily = get_division_toplist("daily", 10);
$monthly = get_division_toplist("monthly", 10);



/**
 * create elements
 */

// date
imagettftext($im, 6, 0, 715, 240, $darkGrey, $tinyfont, strtoupper($dateText));


// daily stats

$y = 65;
$i = 1;

imagettftext($im, 6, 0, $num_col_1, $y, $orange, $tinyfont, strtoupper("#"));
imagettftext($im, 6, 0, $name_col_1, $y, $orange, $tinyfont, strtoupper("Player"));
imagettftext($im, 6, 0, $games_col_1, $y, $orange, $tinyfont, strtoupper("AOD Games"));

foreach ($daily['players'] as $player) {
	$y = $y+20;
 	// number
	imagettftext($im, 6, 0, $num_col_1, $y, $orange, $tinyfont, "{$i}.");
	// name
	imagettftext($im, 6, 0, $name_col_1, $y, $white, $tinyboldfont, substr(strtoupper($player['forum_name']), 0, 12));
	// games
	imagettftext($im, 6, 0, $games_col_1, $y, $white, $tinyfont, "{$player['aod_games']}");
	$i++;

}


// monthly stats

$y = 65;
$i = 1;

imagettftext($im, 6, 0, $num_col_2, $y, $orange, $tinyfont, strtoupper("#"));
imagettftext($im, 6, 0, $name_col_2, $y, $orange, $tinyfont, strtoupper("Player"));
imagettftext($im, 6, 0, $games_col_2, $y, $orange, $tinyfont, strtoupper("AOD Games"));

foreach ($monthly['players'] as $player) {
	$y = $y+20;
 	// number
	imagettftext($im, 6, 0, $num_col_2, $y, $orange, $tinyfont, "{$i}.");
	// name
	imagettftext($im, 6, 0, $name_col_2, $y, $white, $tinyboldfont, substr(strtoupper($player['forum_name']), 0, 12));
	// games
	imagettftext($im, 6, 0, $games_col_2, $y, $white, $tinyfont, "{$player['aod_games']}");
	$i++;

}



// total aod games stat

$total = $monthly['total_percentage'];
imagettftext($im, 48, 0, $total_percent_x, $total_percent_y, $white, $bigfont, $total . "%");



imagepng($im);
imagepng($im, "../toplist-cache.png");
imagedestroy($im); 

/*

 */


?>