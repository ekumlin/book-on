<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class CollectionController {
	/**
	 * Makes an API call to get all of a user's collections.
	 * 
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function viewCollection($request, &$jsonResult) {
		global $DB;

		if (isset($_SESSION['User'])) {
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
					SUM(DISTINCT CASE WHEN br.Rating = 5 THEN 1 ELSE 0 END) AS Rated5Count,
					SUM(DISTINCT CASE WHEN br.Rating = 4 THEN 1 ELSE 0 END) AS Rated4Count,
					SUM(DISTINCT CASE WHEN br.Rating = 3 THEN 1 ELSE 0 END) AS Rated3Count,
					SUM(DISTINCT CASE WHEN br.Rating = 2 THEN 1 ELSE 0 END) AS Rated2Count,
					SUM(DISTINCT CASE WHEN br.Rating = 1 THEN 1 ELSE 0 END) AS Rated1Count
				FROM
					Collection AS c
					LEFT JOIN BookCollected AS bcol ON bcol.CollectionId = c.CollectionId
					LEFT JOIN Book AS b ON b.ISBN = bcol.ISBN
					LEFT JOIN Publisher AS p ON p.PublisherId = b.Publisher
					LEFT JOIN BookCopy AS bc ON bc.ISBN = b.ISBN
					LEFT JOIN BookAuthor AS ba ON ba.ISBN = b.ISBN
					LEFT JOIN Author AS a ON a.AuthorId = ba.AuthorId
					LEFT JOIN BookRated AS br ON br.ISBN = b.ISBN
				WHERE
					{$conditionString}
					c.CardNumber = :cardNumber
				GROUP BY b.ISBN, p.Name
				ORDER BY b.Title
			";
			$collections = $DB->query($query, $params);

			$collectionMap = array();

			foreach ($collections as $c) {
				$jsonResult['success'] |= true;

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
}

?>
