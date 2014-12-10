<?php

define('IS_PAGEVIEW', true);

if (!defined('VALID_REQUEST')) {
	define('VALID_REQUEST', true);
}

require_once('init.php');
require('api.php');

$view = isset($_GET['view']) ? $_GET['view'] : NULL;
if (!$view || !preg_match(String::FILE_TITLE_REGEX, $view)) {
	$view = 'index';
}

require(_ROOT . "pages/{$view}.php");

?>
