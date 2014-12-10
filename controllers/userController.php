<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class UserController {
	/**
	 * Attempts to log in a user.
	 * 
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function login($request, &$jsonResult) {
		global $DB;

		$query = "
			SELECT u.*
			FROM User AS u
			WHERE u.CardNumber = :id
		";
		$users = $DB->query($query, array(
				'id' => $request['cardNumber'],
			));

		foreach ($users as $user) {
			$pwdHash = $user['Password'];

			if (password_verify($request['password'], $pwdHash)) {
				$jsonResult['success'] = true;

				$_SESSION['User'] = new User($user);
			}
		}

		if (!$jsonResult['success']) {
			$jsonResult['errno'] = 0;
			$jsonResult['errstr'] = "No such card number and password combination.";
		}
	}
}

?>
