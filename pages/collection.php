<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

if (!isset($_SESSION['User'])) {
	header('Location: ' . _HOST . 'login');
	exit;
}

$content = '';
$pgTitle = '';
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

		$pgTitle = 'Collections';
	} else if (count($collections->data) > 0) {
		if (count($collections->data[0]->items) == 0) {
			$content .= View::toString('notice', array(
					'class' => 'warning',
					'title' => 'Uh-oh!',
					'message' => 'Looks like there aren\'t any books in this collection. You can search for books above or look in the <a href="' . _HOST . '">book index</a>.',
				));
		} else {
			$items = View::toString('bookCollectedListItemHeader');
			foreach ($collections->data[0]->items as $item) {
				$items .= View::toString('bookCollectedListItem', array(
						'book' => $item,
					));
			}

			$content .= View::toString('collectionView', array(
					'collection' => $collections->data[0],
					'items' => $items,
				));
		}

		$pgTitle = $collections->data[0]->name;
	}
} else {
	Http::back('/collection/');
}

print View::toString('page', array(
		'title' => $pgTitle,
		'styles' => array('listings'),
		'scripts' => array('collection'),
		'searchTarget' => 'books/search',
		'body' => $content,
	));

?>
