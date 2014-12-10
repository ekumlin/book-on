<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class InventoryController {
	/**
	 * Makes an API call to add a book to the database.
	 * 
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function addNewBook($request, &$jsonResult) {
		global $DB;

		$book = $request['book'];

		$query = "
			INSERT INTO Book
				(`ISBN`,
					`Title`,
					`SalePrice`,
					`PageCount`,
					`Edition`,
					`Language`,
					`Publisher`)
			VALUES
				(:isbn,
					:title,
					:salePrice,
					:pageCount,
					:edition,
					:language,
					:publisher);
		";
		$insertion = $DB->query($query, array(
				'isbn' => $book['isbn'],
				'title' => $book['title'],
				'salePrice' => $book['salePrice'],
				'pageCount' => $book['pageCount'],
				'edition' => $book['edition'],
				'language' => $book['language'],
				'publisher' => $book['publisher'],
			));

		$jsonResult['success'] = true;
	}
}

?>
