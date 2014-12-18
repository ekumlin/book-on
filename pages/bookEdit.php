<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

$content = '';
$editingIsbn = $_GET['isbn'];

if (!isset($_SESSION['User']) || $_SESSION['User']->employeeLevel < User::USER_STAFF) {
	header('Location: ' . _HOST . ($editingIsbn ? 'books/' . $editingIsbn : ''));
	exit;
}

$emptyBook = new Book();
if (isset($_POST['bookTitle'])) {
	$errors = array();

	$isbn = $title = $language = NULL;
	$edition = $salePrice = $pageCount = $publisher = 0;

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

	if (ctype_digit($_POST['publisher'])) {
		$publisher = intval($_POST['publisher']);
	}

	if (!$isbn) {
		$errors[] = 'Invalid ISBN.';
	}
	if (!$title || !strlen($title)) {
		$errors[] = 'Invalid book title.';
	}

	if (count($errors) == 0) {
		if (count($existingBook->data) > 0) {
			$errors[] = 'A <a href=' . _HOST . 'books/' . $isbn . '>book</a> with this ISBN already exists.';
		}

		if ($editingIsbn == 0) {
			$newBook = json_decode(apiCall(array(
					'controller' => 'inventory',
					'action' => 'addNewBook',
					'book' => array(
							'isbn' => $isbn,
							'title' => $title,
							'salePrice' => $salePrice,
							'pageCount' => $pageCount,
							'edition' => $edition,
							'language' => $language,
							'publisher' => $publisher,
						),
				)));

			header('Location: ' . _HOST . 'books/' . $isbn);
			exit;
		} else {
			$newBook = json_decode(apiCall(array(
					'controller' => 'inventory',
					'action' => 'updateBook',
					'book' => array(
							'isbn' => $editingIsbn,
							'title' => $title,
							'salePrice' => $salePrice,
							'pageCount' => $pageCount,
							'edition' => $edition,
							'language' => $language,
							'publisher' => $publisher,
						),
				)));

			header('Location: ' . _HOST . 'books/' . $isbn);
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
		'target' => 'add',
	));
} else {
	$books = json_decode(apiCall(array(
			'controller' => 'read',
			'action' => 'viewBook',
			'isbn' => $editingIsbn,
		)));

	$content .= View::toString("bookEdit", array(
		'book' => $books->data[0],
		'target' => "edit/{$editingIsbn}",
	));
}

print View::toString('page', array(
		'title' => 'Editing book',
		'styles' => array('bookView', 'forms'),
		'scripts' => array('bookEdit', 'forms'),
		'searchTarget' => 'books/search',
		'body' => $content,
	));

?>
