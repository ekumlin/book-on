<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

$content = '';
$title = 'Book-On';
$mode = (isset($_GET['mode']) && strtolower($_GET['mode']) == 'in') ? 'in' : 'out';


$content .= View::toString('bookCheck', array(
	'mode' => $mode,
));

if (isset($_POST['cardNumber'])) {
    //REQUIRED DATA: bookCopy.ISBN, copyID, rental date, return date, bookCopy.HeldBy
	//               cardNumber,

	//Book checkout requires two fields: card number and bookcopyID
	//one card number is entered, and multiple books. 
	//bookid is stored in copyId1 to copyIdn based on number of copies.
	//TODO: permissions check goes here? i.e are they allowed to checkout a book? or do we expect the caller
	//to check prereqs before calling this function?

    $holdResult = ($mode == 'in') ? NULL : $_POST['cardNumber'];
    
	$cInd = 1; //TODO: use as index in loop for multiple copies at once
	$copyID = isset($_POST["copyId{$cInd}"]) ? $_POST["copyId{$cInd}"] : NULL;
    
    
	$copy = json_decode(apiCall(array(
			'controller' => 'read',
			'action' => 'viewBookCopy',
			'copyId' => $copyID,
		)));

	if ($copy->success) {
        	//need to set return date to some value
			$nowDate = new DateTime();

        if ($mode == 'in') {
            if ($copy->data[0]->heldBy == NULL && $mode == 'in') {
                $content .= View::toString('error', array(
                    'error' => 'This book has already been checked in.',
                )); 
            } else {
                $content .= "Checking in\n";
                                //set heldBy to requester
                $updatedBookCopy = json_decode(apiCall(array(
                    'controller' => 'inventory',
                    'action' => 'updateBookCopy',
                    'bookCopy' => array(
                            'isForSale' => $copy->data[0]->isForSale,
                            'heldBy' => NULL,
                            'isbn' => $copy->data[0]->isbn,
                            'bookCopyId' => $copyID,
                        ),
                )));
                
            }
        } else { //check out
            if ($copy->data[0]->heldBy != NULL) {
                $content .= View::toString('error', array(
                    'error' => 'This book has already been checked out by someone else.',
                )); 
            
            } else {
                $content .= "Checking out\n";
                
             	$copy->data[0]->rentalDate = $nowDate;
			    $copy->data[0]->returnDate = $nowDate->add(new DateInterval("P7D"));   
                $existingBook = json_decode(apiCall(array(
			        'controller' => 'read',
			        'action' => 'viewBook',
			        'isbn' => $copy->data[0]->isbn,
		        )));
		        $copy->data[0]->book = $existingBook; //required by heldBook, but not by bookCopy
		        //$content .= var_dump($copy);
		        $content .= "Held by: {$copy->data[0]->heldBy}\n";
            
                //set heldBy to requester
                $updatedBookCopy = json_decode(apiCall(array(
                    'controller' => 'inventory',
                    'action' => 'updateBookCopy',
                    'bookCopy' => array(
                            'isForSale' => $copy->data[0]->isForSale,
                            'heldBy' => $_POST['cardNumber'],
                            'isbn' => $copy->data[0]->isbn,
                            'bookCopyId' => $copyID,
                        ),
                )));
                
                $transactionID = json_decode(apiCall(array(
                    'controller' => 'inventory',
                    'action' => 'addBookTransaction',
                    'bookTrans' => array(
                            'bookCopyId' =>  $copyID,
                            'transDate' => $copy->data[0]->rentalDate,
                            'expectDate' => $copy->data[0]->returnDate,
                            'actualDate' => NULL,
                            'cardNumber' => $_POST['cardNumber'],
                        ),
                )));
            }
        }

		} else { //viewBookCopy failed
		    $content .= View::toString('error', array(
			    'error' => 'Unknown error.',
		    ));
	    }
} else { //$_POST['cardNumber'] unset
	$content .= View::toString('error', array(
		'error' => 'Did you forget to enter your card number?',
	));
}

print View::toString('page', array(
		'title' => $title,
		'styles' => array('forms'),
		'scripts' => array('forms', 'bookCheck'),
		'searchTarget' => 'books/search',
		'body' => $content,
	));

?>
