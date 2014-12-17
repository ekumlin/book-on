<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

$content = '';
$fields = '';
$title = 'Book-On';
$tasks = array();
$errors = array();
$mode = (isset($_GET['mode']) && strtolower($_GET['mode']) == 'in') ? 'in' : 'out';

$userLevel = isset($_SESSION['User']) ? $_SESSION['User']->employeeLevel : User::USER_BASIC;

if (isset($_POST['cardNumber'])) {
	//REQUIRED DATA: bookCopy.ISBN, copyID, rental date, return date, bookCopy.HeldBy
	//               cardNumber,

	//Book checkout requires two fields: card number and bookcopyID
	//one card number is entered, and multiple books.
	//bookid is stored in copyId1 to copyIdn based on number of copies.
	//TODO: permissions check goes here? i.e are they allowed to checkout a book? or do we expect the caller
	//to check prereqs before calling this function?

	$nowDate = new DateTime();
	$returnDate = (new DateTime())->add(new DateInterval("P7D"));

	$copies = array();
	$maxCInd = intval($_POST['maxCopyIndex']);

	// Check for errors first
	for ($cInd = 1; $cInd <= $maxCInd; $cInd++) {
		if (!isset($_POST["copyId{$cInd}"])) {
			continue;
		}

		$copyID = $_POST["copyId{$cInd}"];

		if (!strlen($copyID)) {
			continue;
		}

		$fields .= '<div class="input-group">' . View::toString('bookCopyEntry', array('name' => "copyId{$cInd}", 'value' => $copyID)) . '</div>'; // This is probably not good practice

		if (!ctype_digit($copyID)) {
			$errors[] = "Scanned book #{$cInd} has invalid ID {$copyID}";
			continue;
		}

		$copy = json_decode(apiCall(array(
				'controller' => 'read',
				'action' => 'viewBookCopy',
				'copyId' => $copyID,
			)));

		if (!$copy->success) {
			$errors[] = "Scanned book #{$cInd} with ID {$copyID} was not found in the system";
			continue;
		}

		$copyBook = $copy->data[0];
		$copies[$copyID] = $copyBook;

		if ($mode == 'in') {
			if ($copyBook->heldBy == NULL) {
				$errors[] = "'{$copyBook->book->title}' with ID {$copyID} is not currently checked out";
			}
		} else {
			if ($copyBook->heldBy != NULL) {
				$ownerName = $userLevel >= User::USER_STAFF ? $copyBook->heldBy : 'someone else';
				$errors[] = "'{$copyBook->book->title}' with ID {$copyID} is currently checked out to {$ownerName}";
			}
		}
	}

	if (count($errors) == 0) {
		// No errors, assume all data is okay
		for ($cInd = 1; $cInd <= $maxCInd; $cInd++) {
			if (!isset($_POST["copyId{$cInd}"])) {
				continue;
			}

			$copyID = $_POST["copyId{$cInd}"];

			if (!strlen($copyID)) {
				continue;
			}

			$copyBook = $copies[$copyID];

			if ($mode == 'in') {
				//find matching transaction
				$transactionID = json_decode(apiCall(array(
					'controller' => 'read',
					'action' => 'viewMostRecentRentalForUser',
					'transaction' => array(
							'cardNumber' => $copyBook->heldBy,
							'bookCopyId' => $copyID,
						),
				)));

				if (count($transactionID->data[0]) > 0) {
					//update actualDate to return date
					$updatedBookCopy = json_decode(apiCall(array(
						'controller' => 'inventory',
						'action' => 'updateReturnTransaction',
						'returnTrans' => array(
								'returnDate' => date_format(new DateTime(),"Y/m/d H:i:s"),
								'bookTransactionId' => $transactionID->data[0][0]->BookTransactionId,
							),
					)));
				}

				$updatedBookCopy = json_decode(apiCall(array(
					'controller' => 'inventory',
					'action' => 'updateBookCopy',
					'bookCopy' => array(
							'isForSale' => $copyBook->isForSale,
							'heldBy' => NULL,
							'isbn' => $copyBook->isbn,
							'bookCopyId' => $copyID,
						),
				)));

				$tasks[] = "Book copy {$copyID} has been checked in from {$copyBook->heldBy}";
			} else { //check out
				$copyBook->rentalDate = $nowDate;
				$copyBook->returnDate = $returnDate;
				$existingBook = json_decode(apiCall(array(
					'controller' => 'read',
					'action' => 'viewBook',
					'isbn' => $copyBook->isbn,
				)));
				$copyBook->book = $existingBook; //required by heldBook, but not by bookCopy

				//set heldBy to requester
				$updatedBookCopy = json_decode(apiCall(array(
					'controller' => 'inventory',
					'action' => 'updateBookCopy',
					'bookCopy' => array(
							'isForSale' => $copyBook->isForSale,
							'heldBy' => $_POST['cardNumber'],
							'isbn' => $copyBook->isbn,
							'bookCopyId' => $copyID,
						),
				)));

				$transactionID = json_decode(apiCall(array(
					'controller' => 'inventory',
					'action' => 'addBookTransaction',
					'bookTrans' => array(
							'bookCopyId' =>  $copyID,
							'transDate' => date_format($copyBook->rentalDate, "Y/m/d H:i:s"),
							'expectDate' => date_format($copyBook->returnDate, "Y/m/d H:i:s"),
							'actualDate' => NULL,
							'cardNumber' => $_POST['cardNumber'],
						),
				)));

				$copyBook = $transactionID->data[0];
				$tasks[] = "'{$existingBook->data[0]->title}' has been checked out to {$_POST['cardNumber']}";
			}
		}
	}
}

if (count($errors) > 0) {
	$content .= View::toString('error', array(
		'error' => '<ul><li>' . join('</li><li>', $errors) . '</li></ul>',
	));
} else {
	$fields = '';
}

if (count($tasks) > 0) {
	$content .= View::toString('notice', array(
		'class' => 'success',
		'title' => 'Success!',
		'message' => '<ul><li>' . join('</li><li>', $tasks) . '</li></ul>',
	));
}

$content .= View::toString('bookCheck', array(
	'mode' => $mode,
	'fields' => $fields,
));

print View::toString('page', array(
		'title' => $title,
		'styles' => array('forms'),
		'scripts' => array('forms', 'bookCheck'),
		'searchTarget' => 'books/search',
		'body' => $content,
	));

?>
