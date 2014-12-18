<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class ReadController {
	/**
	 * Makes an API call to list all books.
	 *
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function allBooks($request, &$jsonResult) {
		$this->viewBooks($request, $jsonResult);
	}

	/**
	 * Makes an API call to list all publishers.
	 *
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function allPublishers($request, &$jsonResult) {
		global $DB;

		$query = "
			SELECT
				p.*
			FROM
				Publisher AS p
			ORDER BY p.Name
		";

		$results = $DB->query($query);

		$jsonResult['success'] = true;
		foreach ($results as $result) {
			$jsonResult['data'][] = new Publisher($result);
		}
	}

	/**
	 * Makes an API call to create a new publisher.
	 *
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function createPublisher($request, &$jsonResult) {
		global $DB;

		if (!Controller::verifyAccess(User::USER_STAFF, $jsonResult)) {
			return;
		}

		$phone = NULL;
		if ($request['phone']) {
			if (!ctype_digit($request['phone'])) {
				$jsonResult['success'] = false;
				$jsonResult['errno'] = 0;
				$jsonResult['errstr'] = "Provided phone number \"{$request['phone']}\" is not valid";
			} else {
				$phone = $request['phone'];
			}
		}

		if (!$request['name']) {
			$jsonResult['success'] = false;
			$jsonResult['errno'] = 0;
			$jsonResult['errstr'] = 'Publisher name not provided';
		}

		$query = "
			INSERT INTO Publisher
				(`Name`,
					`Address`,
					`Phone`)
			VALUES
				(:name,
					:address,
					:phone)
		";

		$DB->query($query, array(
				'name' => $request['name'],
				'address' => $request['address'],
				'phone' => $phone,
			));

		$jsonResult['success'] = $DB->affectedRows() > 0;
		if ($jsonResult['success']) {
			$jsonResult['data'] = array('id' => $DB->lastInsertedId(), 'name' => $request['name']);
		}
	}

	/**
	 * Makes an API call to get all data for a specific book.
	 *
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function viewBook($request, &$jsonResult) {
		$this->viewBooks($request, $jsonResult);
	}

	/**
	 * Makes an API call to get all data for a specific set of books. Returned as an array of database rows.
	 *
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function viewBooks($request, &$jsonResult) {
		global $DB;

		$owner = NULL;
		if (Http::canAccess(User::USER_BASIC)) {
			if (isset($request['heldBy']) && ctype_digit($request['heldBy'])) {
				$owner = $request['heldBy'];
			}
		}

		if ($owner) {
			$this->viewHeldBooks($request, $jsonResult);
			return;
		}

		$isbns = NULL;
		if (isset($request['isbns']) && preg_match('/^[0-9]([0-9,]*[0-9])?$/', $request['isbns'])) {
			$isbns = $request['isbns'];
		} else if (isset($request['isbn']) && ctype_digit($request['isbn'])) {
			$isbns = $request['isbn'];
		}

		$query = "
			SELECT
				b.*,
				a.*,
				p.Name AS PublisherName,
				SUM(CASE WHEN bc.IsForSale = 0 AND bc.HeldBy IS NULL THEN 1 ELSE 0 END) AS CopiesForRent,
				SUM(CASE WHEN bc.IsForSale = 1 AND bc.HeldBy IS NULL THEN 1 ELSE 0 END) AS CopiesForSale,
				br.Rated5Count,
				br.Rated4Count,
				br.Rated3Count,
				br.Rated2Count,
				br.Rated1Count
			FROM
				Book AS b
				LEFT JOIN Publisher AS p ON p.PublisherId = b.Publisher
				LEFT JOIN BookCopy AS bc ON bc.ISBN = b.ISBN
				LEFT JOIN BookAuthor AS ba ON ba.ISBN = b.ISBN
				LEFT JOIN Author AS a ON a.AuthorId = ba.AuthorId
				LEFT JOIN (
					SELECT
						ISBN,
						SUM(CASE WHEN Rating = 5 THEN 1 ELSE 0 END) AS Rated5Count,
						SUM(CASE WHEN Rating = 4 THEN 1 ELSE 0 END) AS Rated4Count,
						SUM(CASE WHEN Rating = 3 THEN 1 ELSE 0 END) AS Rated3Count,
						SUM(CASE WHEN Rating = 2 THEN 1 ELSE 0 END) AS Rated2Count,
						SUM(CASE WHEN Rating = 1 THEN 1 ELSE 0 END) AS Rated1Count
					FROM BookRated
					GROUP BY ISBN
				) AS br ON br.ISBN = b.ISBN
		";
		if ($isbns) {
			$query .= "WHERE b.ISBN IN ({$isbns})";
		}
		$query .= "
			GROUP BY b.ISBN, a.AuthorId, p.Name
			ORDER BY b.Title
		";

		$books = $DB->query($query);
		$bookMap = $this->constructBookMap($books);

		$jsonResult['success'] = true;
		foreach ($bookMap as $isbn => $book) {
			$jsonResult['data'][] = $book;
		}
	}

	/**
	 * Makes an API call to get all data for a specific held book.
	 *
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function viewBookCopy($request, &$jsonResult) {
		global $DB;

		$query = "
			SELECT
				bc.*,
				b.*
			FROM
				BookCopy AS bc
				JOIN Book AS b ON b.ISBN = bc.ISBN
			WHERE
				bc.BookCopyId = :copyId
			LIMIT
				1
		";

		$bookCopy = $DB->query($query, array(
				'copyId' => $request['copyId'],
			));

		foreach ($bookCopy as $copy) {
			$jsonResult['success'] = true;

			$held = new HeldBook($copy);
			$held->book = new Book($copy);
			$jsonResult['data'][] = $held;
		}
	}

	/**
	 * Makes an API call to get all held book data for a specific user.
	 *
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function viewHeldBooks($request, &$jsonResult) {
		global $DB;

		if (!Controller::verifyAccess(User::USER_BASIC, $jsonResult)) {
			return;
		}

		$query = "
			SELECT
				b.*,
				bc.*,
				bt.Time,
				bt.ExpectedReturn,
				bt.ActualReturn,
				p.Name AS PublisherName
			FROM
				BookCopy AS bc
				LEFT JOIN Book AS b ON bc.ISBN = b.ISBN
				LEFT JOIN BookTransaction AS bt ON bt.BookCopyId = bc.BookCopyId AND bt.CardNumber = bc.HeldBy
				LEFT JOIN Publisher AS p ON p.PublisherId = b.Publisher
			WHERE
				bc.HeldBy = :card
				AND bt.ActualReturn IS NULL
			ORDER BY b.Title
		";

		$books = $DB->query($query, array(
				'card' => $request['heldBy'],
			));

		$jsonResult['success'] = true;
		foreach ($books as $book) {
			$copy = new HeldBook($book);
			$copy->book = new Book($book);
			$jsonResult['data'][] = $copy;
		}
	}

	/**
	 * Makes an API call to get all of a book's ratings and reviews.
	 *
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function viewMostRecentRentalForUser($request, &$jsonResult) {
		global $DB;

		if (!Controller::verifyAccess(User::USER_BASIC, $jsonResult)) {
			return;
		}

		$transaction = $request['transaction'];

		$query = "
			SELECT
				bt.BookTransactionId
			FROM
				BookTransaction AS bt
			LEFT JOIN
				BookCopy AS bc
			ON
				bt.CardNumber = bc.HeldBy AND
				bt.BookCopyId = bc.BookCopyId
			WHERE
				bt.CardNumber = :cardNumber AND
				bt.BookCopyId = :bookCopyId AND
				bt.ActualReturn IS NULL AND
				bc.IsForSale = 0
			ORDER BY bt.Time DESC
			LIMIT 1;
		";
		$transactionID = $DB->query($query, array(
				'cardNumber' => $transaction['cardNumber'],
				'bookCopyId' => $transaction['bookCopyId'],
			));


		$jsonResult['success'] = true;
		$jsonResult['data'][] = $transactionID;
	}

	/**
	 * Constructs an ISBN->book mapping of all books from a set of rows.
	 *
	 * @param array $books An iterable list of database rows containing book data.
	 */
	private function constructBookMap($books) {
		$bookMap = array();

		foreach ($books as $book) {
			$isbn = $book['ISBN'];
			if (!isset($bookMap[$isbn])) {
				$bookMap[$isbn] = new Book($book);
			}

			$bookMap[$isbn]->addAuthor(new Author($book));
		}

		return $bookMap;
	}

     /**
	 * Makes an API call to view the AuthorID of an author for use with BookAuthor pairing.
     * Assumption: Two Authors do not have the same first+last name pairing.
	 *
     * @param array $request A bundle of request data. Usually comes from URL parameter string.
     * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
     */
	public function viewAuthorId($request, &$jsonResult) {
		global $DB;

        if (!Controller::verifyAccess(User::USER_BASIC, $jsonResult)) {
            return;
        }

		$query = "
    			SELECT
                a.AuthorId
            FROM
                Author as a
            WHERE
                a.FirstName = :firstName AND
                a.LastName = :lastName
            LIMIT 1;
		";
		$authorId = $DB->query($query, array(
				'firstName' => $request['firstName'],
				'lastName' => $request['lastName'],
			));
        $resultId = (sizeof($authorId) == 0) ? -1 : $authorId[0]['AuthorId'];


		$jsonResult['success'] = true;
		$jsonResult['data'][] = $resultId;
	}
}

?>
