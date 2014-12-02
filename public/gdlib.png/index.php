<?php
// Set the content-type
header('Content-Type: image/png');

// Create the image
$im = imagecreatetruecolor(400, 90);

// Create some colors
$white = imagecolorallocate($im, 255, 255, 255);
$grey = imagecolorallocate($im, 128, 128, 128);
$black = imagecolorallocate($im, 0, 0, 0);
imagefilledrectangle($im, 0, 0, 399, 89, $white);

// The text to draw
$text = 'AOD_Guybrush';
$subtext = 'The most awesomest';

// Replace path by your own font path
$blkfont = 'din-black.ttf';
$ltfont = 'din-light.ttf';

// Add the text
imagettftext($im, 18, 0, 10, 20, $grey, $blkfont, $text);
imagettftext($im, 14, 0, 10, 45, $grey, $ltfont, $subtext);

// Using imagepng() results in clearer text compared with imagejpeg()
imagepng($im);
imagedestroy($im);
?>