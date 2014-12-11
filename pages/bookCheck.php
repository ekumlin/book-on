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

print View::toString('page', array(
		'title' => $title,
		'styles' => array('forms'),
		'scripts' => array('forms', 'bookCheck'),
		'body' => $content,
	));

?>
