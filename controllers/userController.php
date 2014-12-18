<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class UserController {
	/**
	 * Makes an API call to list all users.
	 *
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function allUsers($request, &$jsonResult) {
		global $DB;

		$query = "
			SELECT
				u.*
			FROM
				User AS u
			ORDER BY u.CardNumber
		";

		$results = $DB->query($query);

		$jsonResult['success'] = true;
		foreach ($results as $result) {
			$jsonResult['data'][] = new User($result);
		}
	}

	/**
	 * Checks if a user exists by email address.
	 * 
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function createAccount($request, &$jsonResult) {
		global $DB;

		$cardNumber = $DB->query('SELECT CardNumber+1 FROM User ORDER BY CardNumber DESC LIMIT 1');
		$query = "
			INSERT INTO User
				(`CardNumber`,
					`IsEmployee`,
					`Name`,
					`Password`,
					`Email`,
					`AccountStatus`)
			VALUES
				(:cardNo,
					0,
					:name,
					:password,
					:email,
					0)
		";
		$DB->query($query, array(
				'cardNo' => $cardNumber[0][0],
				'name' => $request['name'],
				'password' => $request['password'],
				'email' => $request['email'],
			));

		$query = "
			INSERT INTO Collection
				(`Name`,
					`CardNumber`)
			VALUES
				('Favorites',
					:cardNo)
		";
		$DB->query($query, array(
				'cardNo' => $cardNumber[0][0],
			));

		$jsonResult['success'] = true;
		$jsonResult['cardNo'] = $cardNumber[0][0];
	}

	/**
	 * Gets a user by card number.
	 * 
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function getUserByCard($request, &$jsonResult) {
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
			$jsonResult['success'] = true;
			$jsonResult['data'][0] = $user;
		}
	}

	/**
	 * Attempts to log in a user.
	 * 
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function login($request, &$jsonResult) {
		global $DB;

		$this->getUserByCard($request, $jsonResult);
		$users = $jsonResult['data'][0];

		$jsonResult['success'] = false;
		$jsonResult['data'] = array();

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

	/**
	 * Checks if a user exists by email address.
	 * 
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function userEmailExists($request, &$jsonResult) {
		global $DB;

		$query = "
			SELECT u.*
			FROM User AS u
			WHERE u.Email = :email
		";
		$users = $DB->query($query, array(
				'email' => $request['email'],
			));

		$jsonResult['success'] = true;
		$jsonResult['data'] = false;
		foreach ($users as $user) {
			$jsonResult['data'] = true;
		}
	}
}

?>
