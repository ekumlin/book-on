<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

$content = '';

$books = json_decode(apiCall(array(
		'mode' => 'read',
		'data' => 'allBooks',
	)));

if ($books->success) {

	foreach ($books->data as $obj) {
		$content .= Template::toString("bookCard", array(
			'book' => $obj,
		));
	}
} else {
	$content .= Template::toString("error", array(
		'error' => "Unknown error.",
	));
}

print Template::toString("page", array(
		'title' => 'Book-On',
		'styles' => array(),
		'scripts' => array(),
		'body' => $content,
	));

?>
