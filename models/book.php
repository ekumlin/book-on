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
	 * @param string $row The database row containing book data.
	 */
	public function __construct($row) {
		$this->isbn = $row['ISBN'];
		$this->title = $row['Title'];
		$this->salePrice = $row['SalePrice'];
		$this->pageCount = $row['PageCount'];
		$this->edition = $row['Edition'];
		$this->language = $row['Language'];
		$this->publisher = $row['PublisherName'];
		$this->copiesForRent = $row['CopiesForRent'];
		$this->copiesForSale = $row['CopiesForSale'];

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
