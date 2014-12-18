<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class Collection {
	public $collectionId;
	public $name;
	public $cardNumber;
	public $items;

	/**
	 * Creates a new collection instance from a database row. Does not retain password.
	 *
	 * @param string $row The database row containing collection data.
	 */
	public function __construct($row) {
		$this->collectionId = $row['CollectionId'];
		$this->name = $row['Name'];
		$this->cardNumber = $row['CardNumber'];

		$this->items = array();
	}

	/**
	 * Adds a new book item to the list of items this collection holds.
	 *
	 * @param Book $row The Book item to add.
	 */
	public function addItem($item) {
		if (get_class($item) != 'Book') {
			throw new Exception('Provided item argument is ' . get_class($item) . ', expected Book');
		}

		$this->items[] = $item;
	}

	/**
	 * Removes a book item from the list of items this collection holds.
	 *
	 * @param integer $row The ISBN of the book item to remove.
	 */
	public function removeItem($isbn) {
		for ($i = 0; $i < sizeof($this->items); $i++) {
			if ($this->$items[$i]->isbn == $isbn) {
				unset($this->$items[$i]);
				$this->$items = array_values($this->$items); //preserve contiguous numerical index
				return;
			}
		}
	}
}

?>
