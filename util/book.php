<?php

class Book {
	private $isbn;
	private $title;
	private $salePrice;
	private $pageCount;
	private $edition;
	private $language;
	private $publisher;
	private $author;

	/**
	 * Creates a new book instance from a database row.
	 *
	 * @param string $row The database row containing book data.
	 * @param boolean $hasAuthor true if the row also contains author data, false otherwise.
	 */
	public function __construct($row, $hasAuthor = false) {
		$this->isbn = $row['ISBN'];
		$this->title = $row['Title'];
		$this->salePrice = $row['SalePrice'];
		$this->pageCount = $row['PageCount'];
		$this->edition = $row['Edition'];
		$this->language = $row['Language'];
		$this->publisher = $row['Publisher'];

		$this->author = new Author($row);
	}
}

?>
