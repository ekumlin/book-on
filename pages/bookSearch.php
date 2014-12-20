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

	$searchString = trim($_GET['q']);
	if ($searchString) {
		// Remove books from the results if they don't match our criteria
		foreach ($books->data as $key => $book) {
			if (ctype_digit($searchString)) {
				// Check digit-only searches against ISBNs ONLY
				if (!strstr($book->isbn, $searchString)) {
					$books->data[$key] = NULL;
					continue;
				}
			} else {
				$searchables = array(
						$book->title,
						$book->publisher,
						Locale::getLanguageName($book->language),
					);

				foreach ($book->authors as $a) {
					$searchables[] = $a->firstName;
					$searchables[] = $a->lastName;
					$searchables[] = "{$a->firstName} {$a->lastName}";
					$searchables[] = "{$a->lastName}, {$a->firstName}";
				}

				$matches = 0;
				foreach ($searchables as $s) {
					if (strstr(strtolower($s), $searchString)) {
						$matches++;
					}
				}

				if ($matches == 0) {
					$books->data[$key] = NULL;
					continue;
				}
			}
		}

		$content .= "<h1>Results for '{$searchString}'</h1>";
	}

	if (array_key_exists($format, $headerFormats)) {
		$content .= View::toString($headerFormats[$format]);
	}

	foreach ($books->data as $book) {
		if (!$book) {
			continue;
		}

		$content .= View::toString($fmtTemplate, array(
			'book' => $book,
		));
	}
} else {
	$content .= View::toString('error', array(
		'error' => 'Unknown error.',
	));
}

print View::toString('page', array(
		'styles' => array('listings'),
		'scripts' => array('collection'),
		'searchTarget' => 'books/search',
		'body' => $content,
	));

?>
