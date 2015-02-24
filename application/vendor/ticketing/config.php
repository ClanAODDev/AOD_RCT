<?php

/**
 * This should be the full url to this directory
 */
define('SITE_URL', 'https://aod.sitespot.com/application/vendor/ticketing/');

/**
 * Authentication for logging into
 * this application. Not your GitHub account.
 */
define('USERNAME', 'ticketing');
define('PASSWORD', 'ticketing');

/**
 * Your secret hash for verifying
 * the forms. YOU SHOULD CHANGE THIS
 */
define('VALIDATING_HASH', '*$gHjKQ2@$kPyU&^t%$');

// path to the cache directory
define('CACHE_PATH', '/application/vendor/ticketing/github_cache');

// how long should the cache be stored (in seconds)
define('CACHE_TIME', 3600); // 1 hour

/**
 * These definitions are only used before
 * you have defined your access_token.
 * But, you should leave them here just in case
 */
define('GITHUB_CLIENT_ID', '6cdd154d7430c5a480c0');
define('GITHUB_CLIENT_SECRET', '2cbb9ef0e0d24c12e800a527b7b39c68ac1f9485');

/**
 * Once you have run through the
 * installation paste your access
 * token below
 */
define('GITHUB_ACCESS_TOKEN', '8ec16eced80d4fb41a388007525eddaf0010ae56');