<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

$isLoggedIn = isset($_SESSION['User']);

$content = '';

if (!$isLoggedIn) {
	$givenCardNo = isset($_POST['cardNumber']) ? $_POST['cardNumber'] : NULL;
	$givenPwd = isset($_POST['password']) ? $_POST['password'] : NULL;

	if ($givenPwd) {
		$canLogin = json_decode(apiCall(array(
				'controller' => 'user',
				'action' => 'login',
				'cardNumber' => $givenCardNo,
				'password' => $givenPwd,
			)));

		if ($canLogin->success) {
			$isLoggedIn = true;
		} else {
			$content .= View::toString('error', array(
					'error' => $canLogin->errstr,
				));
		}
	}
}

if ($isLoggedIn) {
	Http::back('/login/');
} else {
	$content .= View::toString('login');
}

print View::toString('page', array(
		'title' => 'Log in',
		'styles' => array('forms'),
		'scripts' => array(),
		'searchTarget' => 'books/search',
		'body' => $content,
	));

?>
