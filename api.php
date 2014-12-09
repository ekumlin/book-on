<?php

if (!defined('VALID_REQUEST')) {
	define('VALID_REQUEST', true);
}

require_once('init.php');

function apiReject($errno, $errstr, $errfile, $errline, $errcontext) {
	die(json_encode(array(
			'success' => false,
			'errno' => $errno,
			'errstr' => $errstr,
		)));

	return true;
}

function apiCall($request) {
	set_error_handler("apiReject");

	global $DB;
	$jsonResult = array(
			'success' => false,
			'data' => array(),
		);

	if ($request['mode'] == "read") {
		if ($request['data'] == "allBooks") {
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
		} else if ($request['data'] == "viewBook") {
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

	set_error_handler(NULL);
	return json_encode($jsonResult);
}

if (!defined('IS_PAGEVIEW')) {
	parse_str($_SERVER['QUERY_STRING'], $request);
	die(apiCall($request));
}

?>
