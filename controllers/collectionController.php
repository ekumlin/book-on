<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class CollectionController {
	/**
	 * Makes an API call to add a book to a user's collection.
	 *
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function addCollectedBook($request, &$jsonResult) {
		global $DB;

		if (!Controller::verifyAccess(User::USER_BASIC, $jsonResult)) {
			return;
		}

		$user = $_SESSION['User'];

		$query = "
			SELECT *
			FROM
				Collection AS c
				LEFT JOIN BookCollected AS bc ON bc.CollectionId = c.CollectionId
			WHERE
				c.CollectionId = :collectionId
				AND c.CardNumber = :cardNumber
		";
		$collectedBooks = $DB->query($query, array(
				'cardNumber' => $user->cardNumber,
				'collectionId' => $request['collectionId'],
			));

		foreach ($collectedBooks as $b) {
			if ($b['ISBN'] == $request['isbn']) {
				$jsonResult['success'] = true;
			}
		}

		if (!$jsonResult['success'] && count($collectedBooks) > 0) {
			$query = "
				INSERT INTO BookCollected
					(CollectionId,
					 ISBN)
				VALUES
					(:collectionId,
					 :isbn)
			";
			$DB->query($query, array(
					'collectionId' => $request['collectionId'],
					'isbn' => $request['isbn'],
				));

			$jsonResult['success'] = true;
		}
	}

	/**
	 * Makes an API call to remove a book from a user's collection.
	 *
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function removeCollectedBook($request, &$jsonResult) {
		global $DB;

		if (!Controller::verifyAccess(User::USER_BASIC, $jsonResult)) {
			return;
		}

		$user = $_SESSION['User'];

		$query = "
			SELECT *
			FROM
				Collection AS c
				LEFT JOIN BookCollected AS bc ON bc.CollectionId = c.CollectionId
			WHERE
				c.CollectionId = :collectionId
				AND c.CardNumber = :cardNumber
		";
		$collectedBooks = $DB->query($query, array(
				'cardNumber' => $user->cardNumber,
				'collectionId' => $request['collectionId'],
			));

		foreach ($collectedBooks as $b) {
			if ($b['ISBN'] == $request['isbn']) {
				$jsonResult['success'] = true;
			}
		}

		if ($jsonResult['success']) {
			$query = "
				DELETE FROM BookCollected
				WHERE
					CollectionId = :collectionId
					AND ISBN = :isbn
			";
			$DB->query($query, array(
					'collectionId' => $request['collectionId'],
					'isbn' => $request['isbn'],
				));
		}
	}

	/**
	 * Makes an API call to get all of a user's collections.
	 *
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function viewCollection($request, &$jsonResult) {
		global $DB;

		if (!Controller::verifyAccess(User::USER_BASIC, $jsonResult)) {
			return;
		}

		$user = $_SESSION['User'];

		$conditionString = '';
		$params = array(
				'cardNumber' => $user->cardNumber,
			);

		if ($request['collectionId'] > 0) {
			$params['collectionId'] = $request['collectionId'];
			$conditionString = 'c.CollectionId = :collectionId AND';
		}

		$query = "
			SELECT
				c.*,
				b.*,
				a.*,
				p.Name AS PublisherName,
				SUM(CASE WHEN bc.IsForSale = 0 AND bc.HeldBy IS NULL THEN 1 ELSE 0 END) AS CopiesForRent,
				SUM(CASE WHEN bc.IsForSale = 1 AND bc.HeldBy IS NULL THEN 1 ELSE 0 END) AS CopiesForSale,
				br.Rated5Count,
				br.Rated4Count,
				br.Rated3Count,
				br.Rated2Count,
				br.Rated1Count
			FROM
				Collection AS c
				LEFT JOIN BookCollected AS bcol ON bcol.CollectionId = c.CollectionId
				LEFT JOIN Book AS b ON b.ISBN = bcol.ISBN
				LEFT JOIN Publisher AS p ON p.PublisherId = b.Publisher
				LEFT JOIN BookCopy AS bc ON bc.ISBN = b.ISBN
				LEFT JOIN BookAuthor AS ba ON ba.ISBN = b.ISBN
				LEFT JOIN Author AS a ON a.AuthorId = ba.AuthorId
				LEFT JOIN (
					SELECT
						ISBN,
						SUM(CASE WHEN Rating = 5 THEN 1 ELSE 0 END) AS Rated5Count,
						SUM(CASE WHEN Rating = 4 THEN 1 ELSE 0 END) AS Rated4Count,
						SUM(CASE WHEN Rating = 3 THEN 1 ELSE 0 END) AS Rated3Count,
						SUM(CASE WHEN Rating = 2 THEN 1 ELSE 0 END) AS Rated2Count,
						SUM(CASE WHEN Rating = 1 THEN 1 ELSE 0 END) AS Rated1Count
					FROM BookRated
					GROUP BY ISBN
				) AS br ON br.ISBN = b.ISBN
			WHERE
				{$conditionString}
				c.CardNumber = :cardNumber
			GROUP BY c.CollectionId, b.ISBN, p.Name
			ORDER BY c.Name
		";
		$collections = $DB->query($query, $params);

		$collectionMap = array();

		$jsonResult['success'] = true;
		foreach ($collections as $c) {
			$cId = intval($c['CollectionId']);

			if (!isset($collectionMap[$cId])) {
				$collectionMap[$cId] = new Collection($c);
				$jsonResult['data'][] = $collectionMap[$cId];
			}

			if ($c['ISBN'] != NULL) {
				$collectionMap[$cId]->addItem(new Book($c));
			}
		}
	}
}

?>
