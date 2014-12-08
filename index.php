<?php

define('IS_PAGEVIEW', true);

require_once('init.php');
require('api.php');

$view = isset($_GET['view']) ? $_GET['view'] : null;
if (!$view || !preg_match('@^[^/?*:;{}\\\\]+$@', $view)) {
	$view = 'index';
}

require(_ROOT . "pages/{$view}.php");

?>
