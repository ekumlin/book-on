<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class Rating {
	public $isbn;
	public $rating;
	public $review;
	public $date;
	public $user;

	/**
	 * Creates a new held book instance from a database row.
	 *
	 * @param string $row The database row containing held book data. (If not provided, will populate empty data.)
	 */
	public function __construct($row = NULL) {
		$row = array_merge(array(
				'ISBN' => NULL,
				'Rating' => 0.0,
				'Review' => NULL,
				'Date' => NULL,
			), $row ? $row : array());

		$this->isbn = $row['ISBN'];
		$this->rating = $row['Rating'];
		$this->review = $row['Review'];
		$this->date = $row['Date'];
	}
}

?>
