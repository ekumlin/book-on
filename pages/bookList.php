<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

$content = '';

$headerFormats = array(
		'cards' => NULL,
		'list' => 'bookListItemHeader',
	);

$listFormats = array(
		'cards' => 'bookCard',
		'list' => 'bookListItem',
	);

$books = json_decode(apiCall(array(
		'controller' => 'read',
		'action' => 'allBooks',
	)));

if ($books->success) {
	$format = isset($_GET['format']) ? $_GET['format'] : NULL;
	if (array_key_exists($format, $listFormats)) {
		$fmtTemplate = $listFormats[$format];
	} else {
		$fmtTemplate = $listFormats['cards'];
	}

	if (array_key_exists($format, $headerFormats)) {
		$content .= View::toString($headerFormats[$format]);
	}
	foreach ($books->data as $obj) {
		$content .= View::toString($fmtTemplate, array(
			'book' => $obj,
		));
	}
} else {
	$content .= View::toString('error', array(
		'error' => 'Unknown error.',
	));
}

print View::toString('page', array(
		'styles' => array('listings'),
		'scripts' => array(),
		'searchTarget' => 'books/search',
		'body' => $content,
	));

?>
