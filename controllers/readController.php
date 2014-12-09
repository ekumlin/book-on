<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class ReadController {
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
