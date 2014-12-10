<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class User {
	const USER_BASIC = 0;
	const USER_STAFF = 1;
	const USER_ADMIN = 2;

	public $cardNumber;
	public $employeeLevel;
	public $name;
	public $email;
	public $accountStatus;

	/**
	 * Creates a new user instance from a database row. Does not retain password.
	 *
	 * @param string $row The database row containing user data.
	 */
	public function __construct($row) {
		$this->cardNumber = $row['CardNumber'];
		$this->employeeLevel = $row['IsEmployee'];
		$this->name = $row['Name'];
		$this->email = $row['Email'];
		$this->accountStatus = $row['AccountStatus'];
	}
}

?>
