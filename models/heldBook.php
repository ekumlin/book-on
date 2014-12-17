<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class HeldBook {
	public $copyId;
	public $isForSale;
	public $heldBy; //TODO: consider splitting this into its own BookCopy Class
	public $isbn;
	public $rentalDate;
	public $returnDate;
	public $transKey; //required to check in book. Perhaps this should go in Transaction model
	public $book;

	/**
	 * Creates a new held book instance from a database row.
	 *
	 * @param string $row The database row containing held book data. (If not provided, will populate empty data.)
	 */
	public function __construct($row = NULL) {
		$row = array_merge(array(
				'BookCopyId' => NULL,
				'IsForSale' => false,
				'HeldBy' => NULL,
				'ISBN' => NULL,
				'Time' => NULL,
				'ExpectedReturn' => NULL,
			), $row ? $row : array());

		$this->copyId = $row['BookCopyId'];
		$this->isForSale = $row['IsForSale'];
		$this->heldBy = $row['HeldBy'];
		$this->isbn = $row['ISBN'];
		$this->rentalDate = strtotime($row['Time']);
		$this->returnDate = strtotime($row['ExpectedReturn']);
	}
}

?>
