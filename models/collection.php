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

	public function addItem($item) {
		if (get_class($item) != 'Book') {
			throw new Exception('Provided item argument is ' . get_class($item) . ', expected Book');
		}

		$this->items[] = $item;
	}
    
    public function removeItem($isbn) {
        if (get_class($item) != 'Book') {
			throw new Exception('Provided item argument is ' . get_class($item) . ', expected Book');
		}
        
        for ($i = 0; $i < sizeof($this->items); $i++)
        {
        	if ($this->$items[$i]->isbn == $isbn) {
                unset($this->$items[$i]);
                return;
            }
        }
        
    }
}

?>
