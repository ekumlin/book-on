<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

if (!isset($_SESSION['User']) || $_SESSION['User']->employeeLevel < User::USER_STAFF) {
	http_response_code(404);
	exit;
}

$content = '';

$books = json_decode(apiCall(array(
		'controller' => 'read',
		'action' => 'allUsers',
	)));

if ($books->success) {
	$content .= View::toString('userListItemHeader');
	foreach ($books->data as $obj) {
		$content .= View::toString('userListItem', array(
			'user' => new User($obj),
		));
	}
} else {
	$content .= View::toString('error', array(
		'error' => 'Unknown error.',
	));
}

print View::toString('page', array(
		'title' => 'Book-On',
		'styles' => array('listings'),
		'scripts' => array(),
		'searchTarget' => 'books/search',
		'body' => $content,
	));

?>
