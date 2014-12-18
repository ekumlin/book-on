<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

$content = '';
$pgTitle = '';
$desiredIsbn = $_GET['isbn'];

$books = json_decode(apiCall(array(
		'controller' => 'read',
		'action' => 'viewBook',
		'isbn' => $desiredIsbn,
	)));

if ($books->success) {
	if (count($books->data) >= 1) {
		$reviews = json_decode(apiCall(array(
				'controller' => 'rating',
				'action' => 'viewRatings',
				'isbn' => $desiredIsbn,
			)));

		$content .= View::toString("bookView", array(
			'book' => $books->data[0],
			'reviews' => $reviews->data,
		));

		$pgTitle = $books->data[0]->title;
	} else {
		$content .= View::toString("error", array(
			'error' => "No such book found.",
		));
	}
} else {
	$content .= View::toString("error", array(
		'error' => "Unknown error.",
	));
}

print View::toString('page', array(
		'title' => $pgTitle,
		'styles' => array('bookView'),
		'scripts' => array('collection'),
		'searchTarget' => 'books/search',
		'body' => $content,
	));

?>
