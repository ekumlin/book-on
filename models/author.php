<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class Author {
	public $id;
	public $firstName;
	public $lastName;
	public $birthdate;
	public $homeCountry;

	/**
	 * Creates a new author instance from a database row.
	 *
	 * @param string $row The database row containing author data.
	 */
	public function __construct($row) {
		$this->id = $row['AuthorId'];
		$this->firstName = $row['FirstName'];
		$this->lastName = $row['LastName'];
		$this->birthdate = $row['Birthdate'];
		$this->homeCountry = $row['HomeCountry'];
	}
}

?>
