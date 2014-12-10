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
	public $authors;

	/**
	 * Creates a new book instance from a database row.
	 *
	 * @param string $row The database row containing book data. (If not provided, will populate empty data.)
	 */
	public function __construct($row = NULL) {
		if ($row) {
			$this->isbn = $row['ISBN'];
			$this->title = $row['Title'];
			$this->salePrice = floatval($row['SalePrice']);
			$this->pageCount = intval($row['PageCount']);
			$this->edition = intval($row['Edition']);
			$this->language = $row['Language'];
			$this->publisher = $row['PublisherName'];
			$this->copiesForSale = intval($row['CopiesForSale']);
			$this->copiesForRent = intval($row['CopiesForRent']);
		} else {
			$this->isbn = NULL;
			$this->title = '';
			$this->salePrice = 0.0;
			$this->pageCount = 0;
			$this->edition = 0;
			$this->language = NULL;
			$this->publisher = NULL;
			$this->copiesForSale = 0;
			$this->copiesForRent = 0;
		}

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
