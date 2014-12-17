<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

$isLoggedIn = isset($_SESSION['User']);

$content = '';
$errors = array();

if (!$isLoggedIn) {
	$givenNames = isset($_POST['name']) ? $_POST['name'] : NULL;
	$givenEmail = isset($_POST['email']) ? $_POST['email'] : NULL;
	$givenPwd = isset($_POST['password']) ? $_POST['password'] : NULL;
	$givenPwdC = isset($_POST['passwordConfirm']) ? $_POST['passwordConfirm'] : NULL;

	if ($givenPwd) {
		if (!$givenNames) {
			$errors[] = 'The name field is required.';
		}

		if (!$givenEmail) {
			$errors[] = 'The email field is required.';
		} else if (!strstr($givenEmail, '@')) {
			$errors[] = 'The provided email address is not valid. It should be in format <tt>name@example.com</tt>.';
		}

		if (strlen($givenPwd) < User::MIN_PASSWORD_LENGTH) {
			$errors[] = 'The provided password is not long enough. It must be at least ' . User::MIN_PASSWORD_LENGTH . ' characters.';
		} else if ($givenPwd != $givenPwdC) {
			$errors[] = 'The provided passwords do not match.';
		}

		$userExists = json_decode(apiCall(array(
				'controller' => 'user',
				'action' => 'userEmailExists',
				'email' => $givenEmail,
			)));

		if ($userExists->data) {
			$errors[] = 'This email address is already registered.';
		}

		if (count($errors) == 0) {
			$createAccount = json_decode(apiCall(array(
					'controller' => 'user',
					'action' => 'createAccount',
					'name' => $givenNames,
					'password' => password_hash($givenPwd, PASSWORD_BCRYPT),
					'email' => $givenEmail,
				)));

			if ($createAccount->success) {
				$canLogin = json_decode(apiCall(array(
						'controller' => 'user',
						'action' => 'login',
						'cardNumber' => $createAccount->cardNo,
						'password' => $givenPwd,
					)));

				if ($canLogin->success) {
					$isLoggedIn = true;
				}
			}
		}
	}
}

if ($isLoggedIn) {
	Http::back('/register/');
} else {
	if (count($errors)) {
		$content .= View::toString('error', array(
				'error' => '<ul><li>' . join('</li><li>', $errors) . '</li></ul>',
			));
	}

	$content .= View::toString('register');
}

print View::toString('page', array(
		'title' => 'Register account',
		'styles' => array('forms'),
		'scripts' => array(),
		'searchTarget' => 'books/search',
		'body' => $content,
	));

?>
