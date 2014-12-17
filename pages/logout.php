<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

session_destroy();

Http::back('/./');

?>
