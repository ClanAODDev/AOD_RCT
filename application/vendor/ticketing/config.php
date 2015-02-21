<?php

/**
 * This should be the full url to this directory
 */
define('SITE_URL', 'http://gh-issues.dev/');

/**
 * Authentication for logging into
 * this application. Not your GitHub account.
 */
define('USERNAME', 'admin@example.com');
define('PASSWORD', 'secret312');

/**
 * Your secret hash for verifying
 * the forms. YOU SHOULD CHANGE THIS
 */
define('VALIDATING_HASH', 'abc123itsasecret');

// path to the cache directory
define('CACHE_PATH', APP_ROOT . '/github_cache');

// how long should the cache be stored (in seconds)
define('CACHE_TIME', 3600); // 1 hour

/**
 * These definitions are only used before
 * you have defined your access_token.
 * But, you should leave them here just in case
 */
// define('GITHUB_CLIENT_ID', '');
// define('GITHUB_CLIENT_SECRET', '');

/**
 * Once you have run through the
 * installation paste your access
 * token below
 */
// define('GITHUB_ACCESS_TOKEN', '');