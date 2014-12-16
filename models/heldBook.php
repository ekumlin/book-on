<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class HeldBook {
	public $copyId;
	public $rentalDate;
	public $returnDate;
	public $book;
	public $heldBy;

	/**
	 * Creates a new held book instance from a database row.
	 *
	 * @param string $row The database row containing held book data. (If not provided, will populate empty data.)
	 */
	public function __construct($row = NULL) {
		$row = array_merge(array(
				'BookCopyId' => NULL,
				'Time' => NULL,
				'ExpectedReturn' => NULL,
			), $row ? $row : array());

		$this->copyId = $row['BookCopyId'];
		$this->rentalDate = strtotime($row['Time']);
		$this->returnDate = strtotime($row['ExpectedReturn']);
	}
}

?>
