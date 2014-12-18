<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

$content = '';
$tasks = array();

if (!Http::canAccess(User::USER_STAFF)) {
	header('Location: ' . _HOST . ($editingIsbn ? 'books/' . $editingIsbn : ''));
	exit;
}

$title = "Add books to inventory";
$isWarning = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$maxCInd = intval($_POST['maxCopyIndex']);

	for ($cInd = 1; $cInd <= $maxCInd; $cInd++) {
		if (!isset($_POST["copyId{$cInd}"])) {
			continue;
		}

		$copyID = $_POST["copyId{$cInd}"];
		$isbn = $_POST["isbn{$cInd}"];

		if (!strlen($copyID) || !strlen($isbn)) {
			continue;
		}

		$existingCopy = json_decode(apiCall(array(
			'controller' => 'read',
			'action' => 'viewBookCopy',
			'copyId' => $copyID,
		)));

		if ($existingCopy->success && count($existingCopy->data) > 0) {
			$isWarning = true;
			$tasks[] = "Book copy {$copyID} is already in inventory";
			continue;
		}

		$insertion = json_decode(apiCall(array(
				'controller' => 'inventory',
				'action' => 'insertBookCopy',
				'bookCopy' => array(
						'isbn' => $isbn,
						'copyId' => $copyID,
						'isForSale' => (isset($_POST['areSoldBooks']) && $_POST['areSoldBooks'] == 'on') ? 1 : 0,
					),
			)));

		if ($insertion->success) {
			$tasks[] = "Book copy {$copyID} has been added to inventory";
		} else {
			$isWarning = true;
			$tasks[] = "Book copy {$copyID} was not added to inventory: {$insertion->errstr}";
		}
	}
}

if (count($tasks) > 0) {
	$content .= View::toString('notice', array(
			'class' => $isWarning ? 'warning' : 'success',
			'title' => 'Operation complete',
			'message' => '<ul><li>' . join('</li><li>', $tasks) . '</li></ul>',
		));
}

$content .= View::toString('bookEnlist');

print View::toString('page', array(
		'title' => $title,
		'styles' => array('forms'),
		'scripts' => array('forms', 'bookCheck'),
		'searchTarget' => 'books/search',
		'body' => $content,
	));

?>
