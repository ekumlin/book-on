<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class Book {
	public $isbn;
	public $title;
	public $salePrice;
	public $pageCount;
	public $edition;
	public $language;
	public $publisher;
	public $copiesForRent;
	public $copiesForSale;
	public $copies;
	public $avgRating;
	public $ratings;
	public $authors;

	/**
	 * Creates a new book instance from a database row.
	 *
	 * @param string $row The database row containing book data. (If not provided, will populate empty data.)
	 */
	public function __construct($row = NULL) {
		$row = array_merge(array(
				'ISBN' => 0,
				'Title' => '',
				'SalePrice' => 0.0,
				'PageCount' => 0,
				'Edition' => 0,
				'Language' => config('language'),
				'PublisherName' => '',
				'CopiesForSale' => 0,
				'CopiesForRent' => 0,
			), $row ? $row : array());

		$this->isbn = $row['ISBN'];
		$this->title = $row['Title'];
		$this->salePrice = floatval($row['SalePrice']);
		$this->pageCount = intval($row['PageCount']);
		$this->edition = intval($row['Edition']);
		$this->language = $row['Language'];
		$this->publisher = $row['PublisherName'];
		$this->copiesForSale = intval($row['CopiesForSale']);
		$this->copiesForRent = intval($row['CopiesForRent']);

		$this->ratings = array(
				0 => $row['Rated5Count'] + $row['Rated4Count'] + $row['Rated3Count'] + $row['Rated2Count'] + $row['Rated1Count'],
				1 => $row['Rated1Count'],
				2 => $row['Rated2Count'],
				3 => $row['Rated3Count'],
				4 => $row['Rated4Count'],
				5 => $row['Rated5Count'],
			);

		$ratingTotal = 0;
		for ($i = 1; $i <= 5; $i++) {
			$ratingTotal += $this->ratings[$i] * $i;
		}
		$this->avgRating = ($this->ratings[0] > 0.0) ? floatval($ratingTotal) / floatval($this->ratings[0]) : 0.0;

		$this->copies = $this->copiesForRent + $this->copiesForSale;
		$this->authors = array();
	}

	public function addAuthor($author) {
		if (get_class($author) != 'Author') {
			throw new Exception('Provided author argument is ' . get_class($author) . ', expected Author');
		}

		$this->authors[] = $author;
	}
}

?>
