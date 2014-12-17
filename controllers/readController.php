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
	 * Makes an API call to list all users.
	 * 
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function allUsers($request, &$jsonResult) {
		global $DB;

		$query = "
			SELECT
				u.*
			FROM
				User AS u
			ORDER BY u.CardNumber
		";

		$users = $DB->query($query);

		$jsonResult['success'] = true;
		foreach ($users as $user) {
			$jsonResult['data'][] = new User($user);
		}
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
		if (isset($request['heldBy']) && ctype_digit($request['heldBy'])) {
			$owner = $request['heldBy'];
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
				SUM(DISTINCT CASE WHEN br.Rating = 5 THEN 1 ELSE 0 END) AS Rated5Count,
				SUM(DISTINCT CASE WHEN br.Rating = 4 THEN 1 ELSE 0 END) AS Rated4Count,
				SUM(DISTINCT CASE WHEN br.Rating = 3 THEN 1 ELSE 0 END) AS Rated3Count,
				SUM(DISTINCT CASE WHEN br.Rating = 2 THEN 1 ELSE 0 END) AS Rated2Count,
				SUM(DISTINCT CASE WHEN br.Rating = 1 THEN 1 ELSE 0 END) AS Rated1Count
			FROM
				Book AS b
				LEFT JOIN Publisher AS p ON p.PublisherId = b.Publisher
				LEFT JOIN BookCopy AS bc ON bc.ISBN = b.ISBN
				LEFT JOIN BookAuthor AS ba ON ba.ISBN = b.ISBN
				LEFT JOIN Author AS a ON a.AuthorId = ba.AuthorId
				LEFT JOIN BookRated AS br ON br.ISBN = b.ISBN
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
				bc.*
			FROM
				BookCopy AS bc
			WHERE 
				bc.BookCopyId = :copyId
            LIMIT
                1
		";

		$bookCopy = $DB->query($query, array(
				'copyId' => $request['copyId'],
			));
        
        
		$jsonResult['success'] = true;
	    $copy = new HeldBook($bookCopy);
		$copy->copyId = $request['copyId'];
        $copy->isbn = $bookCopy[0]['ISBN'];
        $copy->isForSale = $bookCopy[0]['IsForSale'];
        $copy->heldBy = $bookCopy[0]['HeldBy'];
        
		$jsonResult['data'][] = $copy;
	}

	/**
	 * Makes an API call to get all held book data for a specific user.
	 * 
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function viewHeldBooks($request, &$jsonResult) {
		global $DB;

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
				AND bt.BookTransactionId IS NULL
					OR (bt.ExpectedReturn > NOW() AND bt.ActualReturn IS NULL)
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
	public function viewRatings($request, &$jsonResult) {
		global $DB;

		$query = "
			SELECT
				br.*,
				u.Name,
				u.Email
			FROM
				BookRated AS br
				JOIN User AS u ON u.CardNumber = br.CardNumber
			WHERE
				br.ISBN = :isbn
		";
		$ratings = $DB->query($query, array(
				'isbn' => $request['isbn'],
			));

		$jsonResult['success'] = true;
		foreach ($ratings as $r) {
			$rating = new Rating($r);
			$rating->user = new User($r);

			$jsonResult['data'][] = $rating;
		}
	}
}

?>
