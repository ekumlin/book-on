<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

$content = '';
$tasks = array();

$userLevel = isset($_SESSION['User']) ? $_SESSION['User']->employeeLevel : User::USER_BASIC;

$title = "Add books to inventory";

if (isset($_POST['cardNumber'])) {

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
			$tasks[] = "Book copy {$copyID} is already in inventory";
			continue;
		}

		apiCall(array(
			'controller' => 'inventory',
			'action' => 'insertBookCopy',
			'bookCopy' => array(
					'isbn' => $isbn,
					'copyId' => $copyID,
				),
		));

		$tasks[] = "Book copy {$copyID} has been added to the inventory";
	}
}

if (count($tasks) > 0) {
	$content .= View::toString('notice', array(
			'class' => 'success',
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
