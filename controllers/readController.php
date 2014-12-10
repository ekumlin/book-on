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
				SUM(CASE WHEN bc.IsForSale = 0 THEN 1 ELSE 0 END) AS CopiesForRent,
				SUM(CASE WHEN bc.IsForSale = 1 THEN 1 ELSE 0 END) AS CopiesForSale
			FROM
				Book AS b
				LEFT JOIN Publisher AS p ON p.PublisherId = b.Publisher
				LEFT JOIN BookCopy AS bc ON bc.ISBN = b.ISBN
				LEFT JOIN BookAuthor AS ba ON ba.ISBN = b.ISBN
				LEFT JOIN Author AS a ON a.AuthorId = ba.AuthorId
		";
		if ($isbns) {
			$query .= "WHERE b.ISBN IN ({$isbns})";
		}
		$query .= " GROUP BY b.ISBN, a.AuthorId, p.Name";

		$books = $DB->query($query);
		$bookMap = $this->constructBookMap($books);

		$jsonResult['success'] = true;
		foreach ($bookMap as $isbn => $book) {
			$jsonResult['data'][] = $book;
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
	 * Makes an API call to get all of a user's collections
	 * 
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function viewCollection($request, &$jsonResult) {
		global $DB;

		if (isset($_SESSION['User'])) {
			$user = $_SESSION['User'];

			$conditionString = '';
			$params = array(
					'cardNumber' => $user->cardNumber,
				);

			if ($request['collectionId'] > 0) {
				$params['collectionId'] = $request['collectionId'];
				$conditionString = 'c.CollectionId = :collectionId AND';
			}

			$query = "
				SELECT
					c.*,
					b.*,
					a.*,
					p.Name AS PublisherName,
					SUM(CASE WHEN bc.IsForSale = 0 THEN 1 ELSE 0 END) AS CopiesForRent,
					SUM(CASE WHEN bc.IsForSale = 1 THEN 1 ELSE 0 END) AS CopiesForSale
				FROM
					Collection AS c
					LEFT JOIN BookCollected AS bcol ON bcol.CollectionId = c.CollectionId
					LEFT JOIN Book AS b ON b.ISBN = bcol.ISBN
					LEFT JOIN Publisher AS p ON p.PublisherId = b.Publisher
					LEFT JOIN BookCopy AS bc ON bc.ISBN = b.ISBN
					LEFT JOIN BookAuthor AS ba ON ba.ISBN = b.ISBN
					LEFT JOIN Author AS a ON a.AuthorId = ba.AuthorId
				WHERE
					{$conditionString} c.CardNumber = :cardNumber
				GROUP BY b.ISBN, p.Name
				ORDER BY b.ISBN
			";
			$collections = $DB->query($query, $params);

			$collectionMap = array();

			foreach ($collections as $c) {
				$jsonResult['success'] |= true;

				$cId = intval($c['CollectionId']);

				if (!isset($collectionMap[$cId])) {
					$collectionMap[$cId] = new Collection($c);
					$jsonResult['data'][] = $collectionMap[$cId];
				}

				if ($c['ISBN'] != NULL) {
					$collectionMap[$cId]->addItem(new Book($c));
				}
			}
		}
	}
}

?>
