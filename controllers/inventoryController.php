<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class InventoryController {
	/**
	 * Makes an API call to add a book transaction to the bookTransaction table as part of record keeping.
	 *
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function addBookTransaction($request, &$jsonResult) {
		global $DB;

		if (!Controller::verifyAccess(User::USER_STAFF, $jsonResult)) {
			return;
		}

		$bookTrans = $request['bookTrans'];

		//omit BookTransactionID since it's auto-increment
		$query = "
			INSERT INTO BookTransaction
				(`BookCopyId`,
					`Time`,
					`ExpectedReturn`,
					`ActualReturn`,
					`CardNumber`)
			VALUES
				(:bookCopyId,
					:transDate,
					:expectDate,
					:actualDate,
					:cardNumber);
		";
		$DB->query($query, array(
				'bookCopyId' => $bookTrans['bookCopyId'],
				'transDate' => $bookTrans['transDate'],
				'expectDate' => $bookTrans['expectDate'],
				'actualDate' => $bookTrans['actualDate'],
				'cardNumber' => $bookTrans['cardNumber'],
			));

		$jsonResult['success'] = true;
		$jsonResult['data'][] = $DB->lastInsertedId();

	}

	/**
	 * Makes an API call to add a book to the database.
	 *
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function addNewBook($request, &$jsonResult) {
		global $DB;

		if (!Controller::verifyAccess(User::USER_STAFF, $jsonResult)) {
			return;
		}

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
		$DB->query($query, array(
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

	/**
	 * Makes an API call to update a book copy as part of checking in/out a book by the user.
	 *
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function insertBookCopy($request, &$jsonResult) {
		global $DB;

		if (!Controller::verifyAccess(User::USER_STAFF, $jsonResult)) {
			return;
		}

		$bookCopy = $request['bookCopy'];

		$DB->query('SELECT * FROM Book AS b WHERE b.ISBN = :isbn', array(
				'isbn' => $bookCopy['isbn'],
			));

		if ($DB->affectedRows() > 0) {
			$query = "
				INSERT INTO BookCopy
					(`BookCopyId`,
						`IsForSale`,
						`HeldBy`,
						`ISBN`)
				VALUES
					(:copyId,
						:isForSale,
						NULL,
						:isbn)
			";
			$DB->query($query, array(
					'isbn' => $bookCopy['isbn'],
					'copyId' => $bookCopy['copyId'],
					'isForSale' => $bookCopy['isForSale'],
				));

			$jsonResult['success'] = $DB->affectedRows() > 0;
		} else {
			$jsonResult['errno'] = 0;
			$jsonResult['errstr'] = "A book with the ISBN '{$bookCopy['isbn']}' was not found in the database.";
		}
	}

	/**
	 * Makes an API call to update a book's information in the database.
	 *
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function updateBook($request, &$jsonResult) {
		global $DB;

		if (!Controller::verifyAccess(User::USER_STAFF, $jsonResult)) {
			return;
		}

		$book = $request['book'];

		$query = "
			UPDATE Book
			SET
				Title = :title,
				SalePrice = :salePrice,
				PageCount = :pageCount,
				Edition = :edition,
				Language = :language,
				Publisher = :publisher
			WHERE
				ISBN = :isbn;
		";
		$DB->query($query, array(
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

	/**
	 * Makes an API call to update a book copy as part of checking in/out a book by the user.
	 *
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function updateBookCopy($request, &$jsonResult) {
		global $DB;

		if (!Controller::verifyAccess(User::USER_BASIC, $jsonResult)) {
			return;
		}

		$bookCopy = $request['bookCopy'];

		if ($bookCopy['heldBy']) {
			if (!Http::canAccess(User::USER_STAFF) && strval($bookCopy['heldBy']) != strval($_SESSION['User']->cardNumber)) {
				$jsonResult['errno'] = 0;
				$jsonResult['errstr'] = 'You cannot check out a book in someone else\'s name.';
				return;
			}
		}

		$query = "
			UPDATE BookCopy
			SET
				IsForSale = :isForSale,
				HeldBy = :heldBy,
				ISBN = :isbn
			WHERE
				BookCopyId = :bookCopyId;
		";
		$DB->query($query, array(
				'isForSale' => $bookCopy['isForSale'],
				'heldBy' => $bookCopy['heldBy'],
				'isbn' => $bookCopy['isbn'],
				'bookCopyId' => $bookCopy['bookCopyId'],
			));

		$jsonResult['success'] = true;
	}

	/**
	 * Makes an API call to update a book transaction in the bookTransaction table as part of record keeping.
	 *
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function updateReturnTransaction($request, &$jsonResult) {
		global $DB;

		if (!Controller::verifyAccess(User::USER_STAFF, $jsonResult)) {
			return;
		}

		$bookReturn = $request['returnTrans'];

		//omit BookTransactionID since it's auto-increment
		$query = "
			UPDATE BookTransaction
			SET
				ActualReturn = :returnDate
			WHERE
				ActualReturn IS NULL
				AND BookCopyId = :bookCopyId
		";
		$DB->query($query, array(
				'returnDate' => $bookReturn['returnDate'],
				'bookCopyId' => $bookReturn['bookCopyId'],
			));

		$jsonResult['success'] = true;
	}
}

?>
