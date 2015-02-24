# AOD_RCT
Activity tracking application for a gaming organization

## Author
Guybrush, Sc0rp10n66, Ichigobankai09

## Purpose
This application is intended to serve as an organizational tool for the AOD community. It can / will be expanded upon as a service that only assists with recruiting functions, but also helps with managing / maintaining subordinate members within the community. It is the goal of this application to be accessible not just by the BF division, but to all divisions.


## Local installation Notes

* This application relies on a configuration file outside the root http directory, so modification of the php include path is necessary in order for correct inclusion of the config file.

* Typically WAMP with rewrite_mod enabled is sufficient to run the tracking tool, although PHP GD is also useful.

  * WAMP installation will make use of the wamp/www directory completely; due to the nature of the router, attempting to run other projects from the same folder is discouraged

## Example Configuration File
This should reside in a folder one directory above the installation direction. The directory containing this file also needs to be added to the php.ini -> include_path in the appropriate OS section. 

### Include path example (using WAMP, Windows)
```
; Windows: "\path1;\path2"
include_path = ".;c:\Users\Chris\Dropbox\www\_include;c:\php\includes;"
```

### Configuration file contents
```php
<?php

//main 
define('APP_TITLE', 'Activity Tracker');

// user profile settings
define('MAX_GAMES', 30);

// db settings
define('DB_HOST', '127.0.0.1');
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASS', '');

// show output
define('SHOW_OUTPUT', false);

// debug mode
define('DEBUG_MODE', false);

// maintenance mode
define('MAINTENANCE', false);

// defines for website URLs
define('CLANAOD', 'http://www.clanaod.net/forums/member.php?u=');
define('BATTLELOG', 'http://battlelog.battlefield.com/bf4/user/');
define('BATTLEREPORT', 'http://battlelog.battlefield.com/bf4/battlereport/show/1/');
define('BF4DB', 'http://bf4db.com/players/');
define('PRIVMSG', 'http://www.clanaod.net/forums/private.php?do=newpm&u=');

// defines for BF4 division activity status display
define('PERCENTAGE_CUTOFF_GREEN', 75);
define('PERCENTAGE_CUTOFF_AMBER', 50);
define('INACTIVE_MIN', 0);
define('INACTIVE_MAX', 25);
```
