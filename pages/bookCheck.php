<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

if (!Http::canAccess(User::USER_BASIC)) {
	header('Location: ' . _HOST . ($editingIsbn ? 'books/' . $editingIsbn : ''));
	exit;
}

$content = '';
$fields = '';
$tasks = array();
$errors = array();
$copies = array();
$isConfirmation = false;

$mode = isset($_GET['mode']) ? strtolower($_GET['mode']) : 'in';
if ($mode == 'sell') {
	$actionLabel = "Sell";
} else {
	if ($mode != 'in') {
		$mode = 'out';
	}
	$actionLabel = "Check {$mode}";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	//REQUIRED DATA: bookCopy.ISBN, copyID, rental date, return date, bookCopy.HeldBy
	//               cardNumber,

	//Book checkout requires two fields: card number and bookcopyID
	//one card number is entered, and multiple books.
	//bookid is stored in copyId1 to copyIdn based on number of copies.
	//TODO: permissions check goes here? i.e are they allowed to checkout a book? or do we expect the caller
	//to check prereqs before calling this function?

	$nowDate = new DateTime();
	$returnDate = (new DateTime())->add(new DateInterval("P7D"));

	$cardNo = (Http::canAccess(User::USER_STAFF) && isset($_POST['cardNumber'])) ? $_POST['cardNumber'] : $_SESSION['User']->cardNumber;
	$maxCInd = intval($_POST['maxCopyIndex']);

	if ($mode != 'in') {
		if (!ctype_digit($cardNo)) {
			$errors[] = "Invalid card number '{$cardNo}' provided";
		} else {
			$users = json_decode(apiCall(array(
					'controller' => 'user',
					'action' => 'getUserByCard',
					'cardNumber' => $cardNo,
				)));

			if (!$users->success) {
				$errors[] = "No account exists with card number '{$cardNo}'";
			}
		}
	}

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

		if ($copyBook->isForSale && $mode != 'sell') {
			$errors[] = "'{$copyBook->book->title}' cannot be rented, it is for purchase only";
			continue;
		}

		if ($mode == 'sell') {
			if (!$copyBook->isForSale) {
				$errors[] = "'{$copyBook->book->title}' cannot be purchased, it is for rental only";
				continue;
			}
			if ($copyBook->heldBy != NULL) {
				if (Http::canAccess(User::USER_STAFF)) {
					$ownerName = $copyBook->heldBy;
				} else {
					$ownerName = 'someone else';
				}

				$errors[] = "'{$copyBook->book->title}' with ID {$copyID} is currently held by {$ownerName}";
			}
		} else if ($mode == 'in') {
			if ($copyBook->heldBy == NULL) {
				$errors[] = "'{$copyBook->book->title}' with ID {$copyID} is not currently checked out";
			}
		} else {
			if ($copyBook->heldBy != NULL) {
				if (Http::canAccess(User::USER_STAFF)) {
					$ownerName = $copyBook->heldBy;
				} else {
					$ownerName = 'someone else';
				}

				$errors[] = "'{$copyBook->book->title}' with ID {$copyID} is currently held by {$ownerName}";
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
				apiCall(array(
					'controller' => 'inventory',
					'action' => 'updateReturnTransaction',
					'returnTrans' => array(
							'returnDate' => new DateTime(),
							'bookCopyId' => $copyID,
						),
				));

				apiCall(array(
					'controller' => 'inventory',
					'action' => 'updateBookCopy',
					'bookCopy' => array(
							'isForSale' => $copyBook->isForSale,
							'heldBy' => NULL,
							'isbn' => $copyBook->isbn,
							'bookCopyId' => $copyID,
						),
				));

				$tasks[] = "Book copy {$copyID} has been checked in from {$copyBook->heldBy}";
			} else if ($mode == 'sell' && !isset($_POST['final'])) {
				$isConfirmation = true;
			} else  { //check out
				$copyBook->rentalDate = $nowDate;
				$copyBook->returnDate = $returnDate;
				$copyBook->book = json_decode(apiCall(array(
					'controller' => 'read',
					'action' => 'viewBook',
					'isbn' => $copyBook->isbn,
				))); //required by heldBook, but not by bookCopy

				//set heldBy to requester
				apiCall(array(
					'controller' => 'inventory',
					'action' => 'updateBookCopy',
					'bookCopy' => array(
							'isForSale' => $copyBook->isForSale,
							'heldBy' => $cardNo,
							'isbn' => $copyBook->isbn,
							'bookCopyId' => $copyID,
						),
				));

				apiCall(array(
					'controller' => 'inventory',
					'action' => 'addBookTransaction',
					'bookTrans' => array(
							'bookCopyId' =>  $copyID,
							'transDate' => $copyBook->rentalDate,
							'expectDate' => $mode == 'sell' ? NULL : $copyBook->returnDate,
							'actualDate' => NULL,
							'cardNumber' => $cardNo,
						),
				));

				// You would check here to see if `$mode == 'sell'`, then charge the person.

				$tasks[] = "'{$copyBook->book->data[0]->title}' has been checked out to {$cardNo}";
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

if ($isConfirmation) {
	$content .= View::toString('bookTransaction', array(
			'books' => $copies,
		));
	//TAX_RATE
} else {
	$content .= View::toString('bookCheck', array(
			'title' => "{$actionLabel} books",
			'mode' => $mode,
			'fields' => $fields,
		));
}

print View::toString('page', array(
		'title' => "{$actionLabel} books",
		'styles' => array('forms'),
		'scripts' => array('forms', 'bookCheck'),
		'searchTarget' => 'books/search',
		'body' => $content,
	));

?>
