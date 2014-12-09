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
		global $DB;

		$query = "
			SELECT
				ba.ISBN, a.*
			FROM
				BookAuthor AS ba
				JOIN Author AS a ON ba.AuthorId = a.AuthorId
		";
		$authors = $DB->query($query);

		$authorTable = array();
		foreach ($authors as $a) {
			if (!isset($authorTable[$a['ISBN']])) {
				$authorTable[$a['ISBN']] = array();
			}

			$authorTable[$a['ISBN']][] = $a;
		}

		$query = "
			SELECT
				b.*,
				p.Name AS PublisherName,
				SUM(CASE WHEN bc.IsForSale = 0 THEN 1 ELSE 0 END) AS CopiesForRent,
				SUM(CASE WHEN bc.IsForSale = 1 THEN 1 ELSE 0 END) AS CopiesForSale
			FROM
				Book AS b
				LEFT JOIN Publisher AS p ON p.PublisherId = b.Publisher
				LEFT JOIN BookCopy AS bc ON bc.ISBN = b.ISBN
			GROUP BY b.ISBN, p.Name
		";
		$books = $DB->query($query);

		$jsonResult['success'] = true;
		foreach ($books as $book) {
			$b = new Book($book);
			foreach ($authorTable[$b->isbn] as $a) {
				$b->addAuthor(new Author($a));
			}

			$jsonResult['data'][] = $b;
		}
	}

	/**
	 * Makes an API call to get all data for a specific book.
	 * 
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function viewBook($request, &$jsonResult) {
		global $DB;

		$query = "
			SELECT
				ba.ISBN, a.*
			FROM
				BookAuthor AS ba
				JOIN Author AS a ON ba.AuthorId = a.AuthorId
		";
		$authors = $DB->query($query);

		$authorTable = array();
		foreach ($authors as $a) {
			if (!isset($authorTable[$a['ISBN']])) {
				$authorTable[$a['ISBN']] = array();
			}

			$authorTable[$a['ISBN']][] = $a;
		}

		$query = "
			SELECT
				b.*,
				p.Name AS PublisherName,
				SUM(CASE WHEN bc.IsForSale = 0 THEN 1 ELSE 0 END) AS CopiesForRent,
				SUM(CASE WHEN bc.IsForSale = 1 THEN 1 ELSE 0 END) AS CopiesForSale
			FROM
				Book AS b
				LEFT JOIN Publisher AS p ON p.PublisherId = b.Publisher
				LEFT JOIN BookCopy AS bc ON bc.ISBN = b.ISBN
			WHERE
				b.ISBN = :isbn
			GROUP BY b.ISBN, p.Name
		";
		$books = $DB->query($query, array(
				'isbn' => $request['isbn'],
			));

		$jsonResult['success'] = true;
		foreach ($books as $book) {
			$b = new Book($book);
			foreach ($authorTable[$b->isbn] as $a) {
				$b->addAuthor(new Author($a));
			}

			$jsonResult['data'][] = $b;
		}
	}
}

?>
