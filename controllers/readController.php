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

		$query =
<<<EOD
SELECT
	b.*, a.*, COUNT(bc.ISBN) AS Copies
FROM
	Book AS b
	LEFT JOIN BookAuthor AS ba ON ba.ISBN = b.ISBN
	LEFT JOIN Author AS a ON ba.AuthorId = a.AuthorId
	LEFT JOIN Publisher AS p ON p.PublisherId = b.Publisher
    LEFT JOIN BookCopy AS bc ON bc.ISBN = b.ISBN
GROUP BY b.ISBN
EOD;
		$books = $DB->query($query);

		$jsonResult['success'] = true;
		foreach ($books as $book) {
			$jsonResult['data'][] = new Book($book);
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

		$query =
<<<EOD
SELECT
	b.*, a.*, COUNT(bc.ISBN) AS Copies
FROM
	Book AS b
	LEFT JOIN BookAuthor AS ba ON ba.ISBN = b.ISBN
	LEFT JOIN Author AS a ON ba.AuthorId = a.AuthorId
	LEFT JOIN Publisher AS p ON p.PublisherId = b.Publisher
    LEFT JOIN BookCopy AS bc ON bc.ISBN = b.ISBN
WHERE
	b.ISBN = :isbn
GROUP BY b.ISBN
EOD;
		$books = $DB->query($query, array(
				'isbn' => $request['isbn'],
			));

		$jsonResult['success'] = true;
		foreach ($books as $book) {
			$jsonResult['data'][] = new Book($book);
		}
	}
}

?>
