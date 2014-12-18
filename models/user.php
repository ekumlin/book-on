<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class User {
	const MIN_PASSWORD_LENGTH = 8;

	const USER_NONE = -1;
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
	 * @param object $row The database row containing user data, or a standard object with the identical fields already set.
	 */
	public function __construct($row = array()) {
		if (is_object($row)) {
			$row = array(
					'CardNumber' => $row->cardNumber,
					'IsEmployee' => $row->employeeLevel,
					'Name' => $row->name,
					'Email' => $row->email,
					'AccountStatus' => $row->accountStatus,
				);
		}

		$row = array_merge(array(
				'CardNumber' => NULL,
				'IsEmployee' => self::USER_BASIC,
				'Name' => NULL,
				'Email' => NULL,
				'AccountStatus' => self::STATUS_NONE,
			), $row ? $row : array());

		$this->cardNumber = $row['CardNumber'];
		$this->employeeLevel = intval($row['IsEmployee']);
		$this->name = $row['Name'];
		$this->email = $row['Email'];
		$this->accountStatus = intval($row['AccountStatus']);
	}

	public function getAccountStatus() {
		switch ($this->accountStatus) {
			case self::STATUS_BANNED:
				return "Banned";

			case self::STATUS_SUSPENDED:
				return "Temporarily suspended";

			default:
		}

		return "Active";
	}

	public function getUserType() {
		switch ($this->employeeLevel) {
			case self::USER_STAFF:
				return "Staff";

			case self::USER_ADMIN:
				return "Administrator";

			default:
		}

		return "User";
	}
}

?>
