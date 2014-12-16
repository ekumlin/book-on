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
				AVG(br.Rating) AS AverageRating
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
	 * Makes an API call to get all of a user's collections.
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
					SUM(CASE WHEN bc.IsForSale = 0 AND bc.HeldBy IS NULL THEN 1 ELSE 0 END) AS CopiesForRent,
					SUM(CASE WHEN bc.IsForSale = 1 AND bc.HeldBy IS NULL THEN 1 ELSE 0 END) AS CopiesForSale
				FROM
					Collection AS c
					LEFT JOIN BookCollected AS bcol ON bcol.CollectionId = c.CollectionId
					LEFT JOIN Book AS b ON b.ISBN = bcol.ISBN
					LEFT JOIN Publisher AS p ON p.PublisherId = b.Publisher
					LEFT JOIN BookCopy AS bc ON bc.ISBN = b.ISBN
					LEFT JOIN BookAuthor AS ba ON ba.ISBN = b.ISBN
					LEFT JOIN Author AS a ON a.AuthorId = ba.AuthorId
				WHERE
					{$conditionString}
					c.CardNumber = :cardNumber
				GROUP BY b.ISBN, p.Name
				ORDER BY b.Title
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
				bt.*,
					p.Name AS PublisherName
			FROM
				BookCopy AS bc
				LEFT JOIN Book AS b ON bc.ISBN = b.ISBN
				LEFT JOIN BookTransaction AS bt ON bt.BookCopyId = bc.BookCopyId
				LEFT JOIN Publisher AS p ON p.PublisherId = b.Publisher
			WHERE bt.ExpectedReturn > NOW() AND bt.ActualReturn IS NULL AND bt.CardNumber = :card
			GROUP BY b.ISBN, p.Name
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
    
    public function viewBookCopy($request, &$jsonResult) {
        global $DB;

        $query = "
			SELECT
				bc.*,
			FROM
				BookCopy AS bc
			WHERE 
                BookCopyID=:copyID
		";
        
        $bookCopy = $DB->query($query, array(
                'copyID' => $request['copyId'],
            ));
        
        
        $existingBook = json_decode(apiCall(array(
                'controller' => 'read',
                'action' => 'viewBook',
                'isbn' => $bookCopy->$row['ISBN'],
            )));
        
        $jsonResult['success'] = true;
        $copy = new HeldBook($bookCopy);
        $copy->copyId = $request['copyId'];
        $copy->book = $existingBook;
        $copy->heldBy = $bookCopy->$row['HeldBy'];
        $jsonResult['data'][] = $copy;
    }
}



?>
