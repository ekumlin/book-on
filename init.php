<?php

define('_ROOT', rtrim(realpath(dirname(__FILE__)), '/') . '/');

global $CONFIG, $DB;

require(_ROOT . 'config.php');
require(_ROOT . 'util/db.php');

$DB = new Connection($CONFIG['db-host'], $CONFIG['db-user'], $CONFIG['db-pass'], $CONFIG['db-name']);

?>
