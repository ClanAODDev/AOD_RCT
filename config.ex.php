<?php

/**
 * configuration file
 * for AOD_RCT
 */

define('DB_HOST', '');
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASS', '');

// temporary until user management / credentials are introduced
define('FORUM_USER', '');
define('FORUM_PASS', '');

// show output
define('SHOW_OUTPUT', false);

// debug mode
define('DEBUG_MODE', false);

// defines for website URLs
define('CLANAOD', 'http://www.clanaod.net/forums/member.php?u=');
define('BATTLELOG', 'http://battlelog.battlefield.com/bf4/user/');
define('BF4DB', 'http://bf4db.com/players/');
define('BF4STATS', 'http://api.bf4stats.com/api/playerInfo?plat=pc&opt=names&output=lines&name=');

// defines for BF4 division activity status display
define('PERCENTAGE_CUTOFF_GREEN', 75);
define('PERCENTAGE_CUTOFF_AMBER', 50);
?>
