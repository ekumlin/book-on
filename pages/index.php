<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

$content = '';

$books = json_decode(apiCall(array(
		'controller' => 'read',
		'action' => 'allBooks',
	)));

if ($books->success) {
	foreach ($books->data as $obj) {
		$content .= View::toString("bookCard", array(
			'book' => $obj,
		));
	}
} else {
	$content .= View::toString("error", array(
		'error' => "Unknown error.",
	));
}

print View::toString('page', array(
		'styles' => array(),
		'scripts' => array(),
		'searchTarget' => 'books/search',
		'body' => $content,
	));

?>
