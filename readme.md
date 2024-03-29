# AOD_RCT
Activity tracking application for a gaming organization

## Author
Guybrush, Sc0rp10n66, Ichigobankai09, Gofish

*Thanks to Sc0rp10n66 for his contributions and work on integration with BF4DB*

## Purpose
This application is intended to serve as an organizational tool for the AOD community. It can / will be expanded upon as a service that only assists with recruiting functions, but also helps with managing / maintaining subordinate members within the community. It is the goal of this application to be accessible not just by the BF division, but to all divisions.

---

## Local installation

* This application relies on a configuration file outside the root http directory, so modification of the php include path is necessary in order for correct inclusion of the config file.

* Typically WAMP (http://www.wampserver.com/en/) with rewrite_mod enabled is sufficient to run the tracking tool, although PHP GD is also useful.

  * WAMP installation will make use of the wamp/www directory completely; due to the nature of the router, attempting to run other projects from the same folder is discouraged

## Example Configuration File
This should reside in a folder one directory above the installation direction. The directory containing this file also needs to be added to the php.ini -> include_path in the appropriate OS section. 

### Include path example (using WAMP, Windows)
```
; Windows: "\path1;\path2"
include_path = ".;c:\Users\Chris\Dropbox\www\_include;c:\php\includes;"
```
