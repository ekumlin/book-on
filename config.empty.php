<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

// Edit me and save me as config.php!
// Do not commit any config files!

$CONFIG['db-host'] = '';
$CONFIG['db-user'] = '';
$CONFIG['db-pass'] = '';
$CONFIG['db-name'] = '';

$CONFIG['language'] = 'en';
$CONFIG['datetime-format'] = 'D M j, Y, g:i:sa';
$CONFIG['date-format'] = 'j M Y';
$CONFIG['time-format'] = 'H:i:s';

?>