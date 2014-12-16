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

//REQUIRED DATA: bookCopy.ISBN, copyID, rental date, return date, bookCopy.HeldBy
//               cardNumber,

//Book checkout requires two fields: card number and bookcopyID
//one card number is entered, and multiple books. 
//card number is stored as....
//bookid is stored in copyId1 to copyIdn based on number of copies.
$givenCardNo = isset($_POST['cardNumber']) ? $_POST['cardNumber'] : '1111111111111'; //dummy value
    //for now since I can't figure out how to grab it from form (no id or name)
    //might be $_SESSION['User']->cardNumber ??? but doesn't currently require log in

if ($givenCardNo == NULL) {
    $content .= View::toString('error', array(
        'error' => 'Error: Blank card number.',
    ));
}
//TODO: permissions check goes here? i.e are they allowed to checkout a book? or do we expect the caller
//to check prereqs before calling this function?

$cInd = 1; //TODO: use as index in loop for multiple copies at once
$copyID = isset($_POST["copyId{$cInd}"]) ? $_POST["copyId{$cInd}"] : NULL;

$copy = json_decode(apiCall(array(
		'controller' => 'read',
		'action' => 'viewBookCopy',
		'copyId' => $copyID,
	)));

if ($copy->success) {
    if ($copy->$heldBy != NULL) {
        //already checked out, return error   
        $content .= View::toString('error', array(
		    'error' => 'Error: Book already checked out by someone.',
	    )); 
    } else {
        //need to set return date to some value, 
        $nowDate = new DateTime();
        $copy->$rentalDate = $nowDate;
        $copy->$returnDate = $nowDate->add(new DateInterval("P7D"));
        
        $content .= var_dump($copy);
    }
} else {
	$content .= View::toString('error', array(
		'error' => 'Unknown error.',
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
