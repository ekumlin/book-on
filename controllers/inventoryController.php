<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class InventoryController {
	/**
	 * Checks if Author exists in Author Table. If so, gathers AuthorId and creates new entry in
	 * BookAuthor. If not, adds new entry to Author to Author table, stores Author ID, and creates entry in BookAuthor with ID.
	 *
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function addAuthorToBook($request, &$jsonResult) {
		global $DB;

		if (!Controller::verifyAccess(User::USER_STAFF, $jsonResult)) {
			return;
		}

		if (isset($request['author-id'])) {
			$idVal = $request['author-id'];
		} else {
			$authorId = json_decode(apiCall(array(
					'controller' => 'read',
					'action' => 'viewAuthorId',
					'firstName' => $request['author-firstName'],
					'lastName' => $request['author-lastName'],
				)));

			//test for $authorId present or not. if not, insert
			if ($authorId->data[0] == -1) { //Should really use a constant error value here
				$authorId = json_decode(apiCall(array(
						'controller' => 'inventory',
						'action' => 'addNewAuthor',
						'author-firstName' => $request['author-firstName'],
						'author-lastName' => $request['author-lastName'],
						'author-birthDate' => $request['author-birthDate'],
						'author-homeCountry' => $request['author-homeCountry'],
					)));
			}

			if (!$authorId->success) {
				return;
			}

			$idVal = $authorId->data[0];
		}

		$query = "
			INSERT IGNORE INTO BookAuthor
				(`ISBN`,
					`AuthorId`)
			VALUES
				(:isbn,
					:authorId);
		";
		$DB->query($query, array(
				'isbn' => $request['author-isbn'],
				'authorId' => $idVal,
			));

		$jsonResult['success'] = true;
	}

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
	 * Adds new author to table. Caller is expected to call read/viewAuthorID first to make sure
	 * the entry does not already exist. IGNORE flag is not used, as it might mask other errors.
	 *
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function addNewAuthor($request, &$jsonResult) {
		global $DB;

		if (!Controller::verifyAccess(User::USER_STAFF, $jsonResult)) {
			return;
		}

		//omit BookTransactionID since it's auto-increment
		$query = "
			INSERT INTO Author
				(`FirstName`,
					`LastName`,
					`BirthDate`,
					`HomeCountry`)
			VALUES
				(:firstName,
					:lastName,
					:birthDate,
					:homeCountry);
		";
		$DB->query($query, array(
				'firstName' => $request['author-firstName'],
				'lastName' => $request['author-lastName'],
				'birthDate' => $request['author-birthDate'],
				'homeCountry' => $request['author-homeCountry'],
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

		$authorIds = array_unique(explode(',', $book['authorIds']));
		foreach ($authorIds as $id) {
			$tempResult = array();
			$this->addAuthorToBook(array(
					'author-isbn' => $book['isbn'],
					'author-id' => $id,
				), $tempResult);
		}

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
				ISBN = :isbn
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

		$query = "
			DELETE FROM
				BookAuthor
			WHERE
				ISBN = :isbn
		";
		$DB->query($query, array(
				'isbn' => $book['isbn'],
			));

		$authorIds = array_unique(explode(',', $book['authorIds']));
		foreach ($authorIds as $id) {
			$tempResult = array();
			$this->addAuthorToBook(array(
					'author-isbn' => $book['isbn'],
					'author-id' => $id,
				), $tempResult);
		}

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
