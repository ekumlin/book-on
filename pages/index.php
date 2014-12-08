<?php

$books = json_decode(apiCall(array(
		'mode' => 'read',
		'data' => 'allBooks',
	)));

if ($books->success) {
	$bookIndex = '';
	
	foreach ($books->data as $obj) {
		$bookIndex .= Template::toString("bookCard", array(
			'book' => $obj,
		));
	}

	print Template::toString("page", array(
			'title' => 'Book-On',
			'styles' => array('base'),
			'scripts' => array(''),
			'body' => $bookIndex,
		));
} else {
	die("Error");
}

?>
