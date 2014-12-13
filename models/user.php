<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class User {
	const USER_BASIC = 0;
	const USER_STAFF = 1;
	const USER_ADMIN = 2;

	const STATUS_NONE = 0;
	const STATUS_BANNED = 1;
	const STATUS_SUSPENDED = 2;

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

	public function getAccountStatus() {
		switch ($this->accountStatus) {
			case STATUS_BANNED:
				return "Banned";

			case STATUS_SUSPENDED:
				return "Temporarily suspended";

			default:
		}

		return "Active";
	}

	public function getUserType() {
		switch ($this->employeeLevel) {
			case USER_STAFF:
				return "Staff";

			case USER_ADMIN:
				return "Administrator";

			default:
		}

		return "User";
	}
}

?>
