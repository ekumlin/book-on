<?php

define('_ROOT', rtrim(realpath(dirname(__FILE__)), '/') . '/');

global $CONFIG, $DB;

require(_ROOT . 'config.php');
require(_ROOT . 'util/database.php');
require(_ROOT . 'util/template.php');

$DB = new Connection($CONFIG['db-host'], $CONFIG['db-user'], $CONFIG['db-pass'], $CONFIG['db-name']);

?>
