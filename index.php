<?php

require('init.php');

$bookIndex = '';

$query = <<<EOD
SELECT
	b.*, a.*
FROM
	Book AS b
	LEFT JOIN BookAuthor AS ba ON ba.ISBN = b.ISBN
	LEFT JOIN Author AS a ON ba.AuthorId = a.AuthorId
	LEFT JOIN Publisher AS p ON p.PublisherId = b.Publisher
EOD;
$books = $DB->query($query);

foreach ($books as $row) {
	$bookIndex .= Template::toString("bookCard", array(
		'book' => new Book($row, true),
	));
}

print Template::toString("page", array(
	'title' => 'Book-On',
	'styles' => array('base'),
	'scripts' => array(''),
	'body' => $bookIndex,
));

?>
