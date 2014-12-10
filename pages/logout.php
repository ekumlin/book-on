<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

session_destroy();

$returnUrl = _HOST;
if (isset($_SERVER['HTTP_REFERER']) && strstr($_SERVER['HTTP_REFERER'], 'login') === false) {
	$returnUrl = $_SERVER['HTTP_REFERER'];
}
header("Location: $returnUrl");
exit;

?>
