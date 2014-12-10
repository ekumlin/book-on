<?php

//password_hash("eric", PASSWORD_BCRYPT);

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class User {
	public $cardNumber;
	public $isEmployee;
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
		$this->isEmployee = $row['IsEmployee'];
		$this->name = $row['Name'];
		$this->email = $row['Email'];
		$this->accountStatus = $row['AccountStatus'];
	}
}

?>
