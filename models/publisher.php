<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class Publisher {
	public $id;
	public $name;
	public $address;
	public $phone;

	/**
	 * Creates a new publisher instance from a database row.
	 *
	 * @param object $row The database row containing user data, or a standard object with the identical fields already set.
	 */
	public function __construct($row = array()) {
		if (is_object($row)) {
			$row = array(
					'PublisherId' => $row->id,
					'Name' => $row->name,
					'Address' => $row->address,
					'PhoneNumber' => $row->phone,
				);
		}

		$row = array_merge(array(
				'PublisherId' => NULL,
				'Name' => NULL,
				'Address' => NULL,
				'PhoneNumber' => NULL,
			), $row ? $row : array());

		$this->id = $row['PublisherId'];
		$this->name = $row['Name'];
		$this->address = $row['Address'];
		$this->phone = $row['PhoneNumber'];
	}
}

?>
