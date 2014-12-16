<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

if (!isset($_SESSION['User'])) {
	http_response_code(404);
	exit;
}

$content = '';
$title = 'Book-On';
$desiredCId = isset($_GET['collectionId']) ? $_GET['collectionId'] : 0;

$collections = json_decode(apiCall(array(
		'controller' => 'collection',
		'action' => 'viewCollection',
		'collectionId' => $desiredCId,
	)));

if ($collections->success) {
	if ($desiredCId == 0) {
		$content .= View::toString('collectionListItemHeader');
		foreach ($collections->data as $obj) {
			$content .= View::toString('collectionListItem', array(
				'collection' => $obj,
			));
		}
	} else if (count($collections->data) > 0) {
		if (count($collections->data[0]->items) == 0) {
			$content .= View::toString('collectionListEmpty');
		} else {
			$items = View::toString('bookListItemHeader');
			foreach ($collections->data[0]->items as $item) {
				$items .= View::toString('bookListItem', array(
						'book' => $item,
					));
			}

			$content .= View::toString('collectionView', array(
					'collection' => $collections->data[0],
					'items' => $items,
				));
		}
	}
} else {
	Http::back('/collection/');
}

print View::toString('page', array(
		'title' => $title,
		'styles' => array('listings'),
		'scripts' => array(),
		'searchTarget' => 'books/search',
		'body' => $content,
	));

?>
