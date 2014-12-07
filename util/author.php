<?php

class Author {
	private $firstName;
	private $lastName;
	private $birthdate;
	private $homeCountry;

	/**
	 * Creates a new author instance from a database row.
	 *
	 * @param string $row The database row containing author data.
	 */
	public function __construct($row) {
		$this->firstName = $row['FirstName'];
		$this->lastName = $row['LastName'];
		$this->birthdate = $row['Birthdate'];
		$this->homeCountry = $row['HomeCountry'];
	}
}

?>
