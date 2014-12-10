<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class Collection {
	public $collectionId;
	public $name;
	public $cardNumber;

	/**
	 * Creates a new collection instance from a database row. Does not retain password.
	 *
	 * @param string $row The database row containing collection data.
	 */
	public function __construct($row) {
		$this->collectionId = $row['CollectionId'];
		$this->name = $row['Name'];
		$this->cardNumber = $row['CardNumber'];
	}
}

?>
