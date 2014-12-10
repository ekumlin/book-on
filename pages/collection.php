<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

$content = '';
$title = 'Book-On';
$desiredCId = isset($_GET['collectionId']) ? $_GET['collectionId'] : 0;

$collections = json_decode(apiCall(array(
		'controller' => 'read',
		'action' => 'viewCollection',
		'collectionId' => $desiredCId,
	)));

if ($collections->success) {
	if ($desiredCId == 0) {
		$content .= View::toString("collectionList", array(
				'collections' => $collections->data,
			));
	} else if (count($collections->data) > 0) {
		$items = '';
		foreach ($collections->data[0]->items as $item) {
			$items .= View::toString("bookCard", array(
					'book' => $item,
				));
		}

		$content .= View::toString("collectionView", array(
				'collection' => $collections->data[0],
				'items' => $items,
			));
	}
} else {
	Http::back('/collection/');
}

print View::toString("page", array(
		'title' => $title,
		'styles' => array(),
		'scripts' => array(),
		'body' => $content,
	));

?>