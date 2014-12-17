<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

$_GET['q'] = '';
require('bookSearch.php');

?>
