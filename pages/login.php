<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

$content = '';
$givenCardNo = isset($_POST['cardNumber']) ? $_POST['cardNumber'] : NULL;
$givenPwd = isset($_POST['password']) ? $_POST['password'] : NULL;

if ($givenPwd) {
	$canLogin = json_decode(apiCall(array(
			'controller' => 'user',
			'action' => 'login',
			'cardNumber' => $givenCardNo,
			'password' => $givenPwd,
		)));

	$returnUrl = _HOST;
	if (isset($_SERVER['HTTP_REFERER']) && strstr($_SERVER['HTTP_REFERER'], 'login') === false) {
		$returnUrl = $_SERVER['HTTP_REFERER'];
	}
	header("Location: $returnUrl");
	exit;
} else {
	$content .= View::toString('login');
}

print View::toString('page', array(
		'title' => 'Log in :: Book-On',
		'styles' => array('login'),
		'scripts' => array(),
		'body' => $content,
	));

?>
