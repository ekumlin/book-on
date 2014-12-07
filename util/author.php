<?php

class Author {
	private $firstName;
	private $lastName;
	private $birthDate;
	private $homeCountry;

	/**
	 * Creates a new author instance from a database row.
	 *
	 * @param string $row The database row containing author data.
	 */
	public function __construct($row) {
		$this->firstName = $row['FirstName'];
		$this->lastName = $row['LastName'];
		$this->birthDate = $row['BirthDate'];
		$this->homeCountry = $row['HomeCountry'];
	}
}

?>
