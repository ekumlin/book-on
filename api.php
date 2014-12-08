<?php

require_once('init.php');

function apiCall($request) {
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
	b.*, a.*
FROM
	Book AS b
	LEFT JOIN BookAuthor AS ba ON ba.ISBN = b.ISBN
	LEFT JOIN Author AS a ON ba.AuthorId = a.AuthorId
	LEFT JOIN Publisher AS p ON p.PublisherId = b.Publisher
EOD;
			$books = $DB->query($query);

			$jsonResult['success'] = true;
			foreach ($books as $book) {
				$jsonResult['data'][] = new Book($book);
			}
		}
	}

	return json_encode($jsonResult);
}

if (!defined('IS_PAGEVIEW')) {
	parse_str($_SERVER['QUERY_STRING'], $request);
	die(apiCall($request));
}

?>
