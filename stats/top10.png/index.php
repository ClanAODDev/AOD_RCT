<?php
header('Content-Type: image/png');

date_default_timezone_set('America/New_York');
include("../../application/lib.php");

$im = imagecreatetruecolor(960, 330);

// color
$white      = imagecolorallocate($im, 255, 255, 255);
$grey       = imagecolorallocate($im, 128, 128, 128);
$darkergrey = imagecolorallocate($im, 30, 30, 30);
$orange     = imagecolorallocate($im, 255, 108, 0);
$darkGrey   = imagecolorallocate($im, 50, 50, 50);
$im         = imagecreatefrompng("../images/big-bg.png");

$text     = "Battlefield Division";
$dateText = date('d M', strtotime('-30 days')) . " - " . date('d M');

$tinyfont     = "../fonts/copy0855.ttf";
$tinyboldfont = "../fonts/copy0866.ttf";
$bigfont      = "../fonts/din-black.otf";

// x value positions
$games_col_1 = 190;
$num_col_1   = 23;
$name_col_1  = 45;

$games_col_2 = 465;
$num_col_2   = 300;
$name_col_2  = 320;

$total_percent_x = 590;
$total_percent_y = 160;

/**
 * get data
 */
try {

	$daily   = get_division_toplist("daily", 10);
	$monthly = get_division_toplist("monthly", 10);

	if (!$daily || !$monthly) {
		throw new Exception("Query could not be completed", 1);
	}


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
    	$y    = $y + 20;
    	$name = strtoupper($player['rank']) . " " . strtoupper($player['forum_name']);
        // number
    	imagettftext($im, 6, 0, $num_col_1, $y, $orange, $tinyfont, "{$i}.");
        // name
    	imagettftext($im, 6, 0, $name_col_1, $y, $white, $tinyfont, $name);
        // games
    	imagettftext($im, 6, 0, $games_col_1, $y, $white, $tinyboldfont, "{$player['aod_games']}");
    	$i++;

    }
    
    
    // monthly stats
    
    $y = 65;
    $i = 1;
    
    imagettftext($im, 6, 0, $num_col_2, $y, $orange, $tinyfont, strtoupper("#"));
    imagettftext($im, 6, 0, $name_col_2, $y, $orange, $tinyfont, strtoupper("Player"));
    imagettftext($im, 6, 0, $games_col_2, $y, $orange, $tinyfont, strtoupper("AOD Games"));
    
    foreach ($monthly['players'] as $player) {
    	$y    = $y + 20;
    	$name = strtoupper($player['rank']) . " " . strtoupper($player['forum_name']);
        // number
    	imagettftext($im, 6, 0, $num_col_2, $y, $orange, $tinyfont, "{$i}.");
        // name
    	imagettftext($im, 6, 0, $name_col_2, $y, $white, $tinyfont, $name);
        // games
    	imagettftext($im, 6, 0, $games_col_2, $y, $white, $tinyboldfont, "{$player['aod_games']}");
    	$i++;

    }
    
    // total aod games stat
    
    $total = $monthly['total_percentage'];
    imagettftext($im, 48, 0, $total_percent_x, $total_percent_y, $white, $bigfont, $total . "%");
    
}
catch (Exception $e) {

	$im = imagecreatefrompng("../images/big-bg-error.png");
	imagettftext($im, 6, 0, 10, 320, $darkGrey, $tinyfont, strtoupper($e->getMessage()));

}


imagepng($im);
imagepng($im, "../toplist-cache.png");
imagedestroy($im);

/*

*/


?>