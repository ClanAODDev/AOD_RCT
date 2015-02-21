<?php

session_start();

if( empty($_SESSION['logged_in']) && !defined('EMBED_PAGE') ) {
  header('Location: login.php');
}

// make sure files are included correctly
define('APP_ROOT', realpath(dirname(__FILE__)));

function exception_handler($exception) {
  include APP_ROOT . '/common/error-page.php';
}

set_exception_handler('exception_handler');

include APP_ROOT . '/config.php';

// needs to be installed
if( !defined('GITHUB_ACCESS_TOKEN') && !defined('EMBED_PAGE') ) {
  header('Location: install.php');
}

// include the GitHub API wrapper
include APP_ROOT . '/classes/github.php';

// register an autoloader for the API wrapper
spl_autoload_register(array('GitHub', 'autoload'));