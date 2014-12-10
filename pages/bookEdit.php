<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

$content = '';
$title = 'Book-On';
$editingIsbn = $_GET['isbn'];

$emptyBook = new Book();
if (isset($_POST['bookTitle'])) {
	$errors = array();

	if ($editingIsbn == 0) {
		$isbn = $title = $language = NULL;
		$edition = $salePrice = $pageCount = 0;

		$isbn = str_replace('-', '', strval($_POST['isbn']));
		if (!ctype_digit($isbn) || (strlen($isbn) != 13 && strlen($isbn) != 10)) {
			$isbn = NULL;
		}

		$existingBook = json_decode(apiCall(array(
				'controller' => 'read',
				'action' => 'viewBook',
				'isbn' => $isbn,
			)));

		$title = $_POST['bookTitle'];

		if (array_key_exists($_POST['language'], Locale::getLanguageList())) {
			$language = $_POST['language'];
		}

		if (ctype_digit($_POST['edition'])) {
			$edition = intval($_POST['edition']);
		}

		if (is_numeric($_POST['salePrice'])) {
			$salePrice = floatval($_POST['salePrice']);
		}

		if (ctype_digit($_POST['pageCount'])) {
			$pageCount = intval($_POST['pageCount']);
		}

		if (count($existingBook->data) > 0) {
			$errors[] = 'A <a href=' . _HOST . 'book/' . $isbn . '>book</a> with this ISBN already exists.';
		}
		if (!$isbn) {
			$errors[] = 'Invalid ISBN.';
		}
		if (!$title || !strlen($title)) {
			$errors[] = 'Invalid book title.';
		}

		if (count($errors) == 0) {
			$addedBook = json_decode(apiCall(array(
					'controller' => 'inventory',
					'action' => 'addNewBook',
					'book' => array(
							'isbn' => $isbn,
							'title' => $title,
							'salePrice' => $salePrice,
							'pageCount' => $pageCount,
							'edition' => $edition,
							'language' => $language,
							'publisher' => NULL,
						),
				)));

			var_dump($addedBook);
			//header('Location: ' . _HOST . 'book/' . $isbn);
			exit;
		}
	}

	if (count($errors) > 0) {
		$content .= View::toString("error", array(
			'error' => '<ul><li>' . join('</li><li>', $errors) . '</li></ul>',
		));

		$antiXss = ENT_COMPAT | ENT_HTML401 | ENT_QUOTES;
		$emptyBook->isbn = htmlentities($_POST['isbn'], $antiXss);
		$emptyBook->title = htmlentities($_POST['bookTitle'], $antiXss);
		$emptyBook->language = htmlentities($_POST['language'], $antiXss);
		$emptyBook->edition = htmlentities($_POST['edition'], $antiXss);
		$emptyBook->salePrice = htmlentities($_POST['salePrice'], $antiXss);
		$emptyBook->pageCount = htmlentities($_POST['pageCount'], $antiXss);
	}
}

if ($editingIsbn == 0) {
	$content .= View::toString("bookEdit", array(
		'book' => $emptyBook,
	));
} else {
	$books = json_decode(apiCall(array(
			'controller' => 'read',
			'action' => 'viewBook',
			'isbn' => $editingIsbn,
		)));

	$content .= View::toString("bookEdit", array(
		'book' => $books->data[0],
	));
}

print View::toString("page", array(
		'title' => $title,
		'styles' => array('bookView', 'forms'),
		'scripts' => array('forms'),
		'body' => $content,
	));

?>
