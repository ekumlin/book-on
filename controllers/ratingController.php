<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class RatingController {
	/**
	 * Makes an API call to update a book's information in the database.
	 *
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function updateRating($request, &$jsonResult) {
		global $DB;

		if (isset($_SESSION['User'])) {
			$isbn = $request['isbn'];

			$newRating = intval($request['rating']);
			if ($newRating < 1) {
				$newRating = 1;
			} else if ($newRating > 5) {
				$newRating = 5;
			}

			$query = "
				SELECT *
				FROM BookRated
				WHERE
					ISBN = :isbn
					AND CardNumber = :cardNumber
			";
			$existingRatings = $DB->query($query, array(
					'isbn' => $isbn,
					'cardNumber' => $_SESSION['User']->cardNumber,
				));

			if (count($existingRatings) > 0) {
			$query = "
					UPDATE BookRated
					SET
						Rating = :rating,
						Date = :date
					WHERE
						ISBN = :isbn
						AND CardNumber = :cardNumber
				";
				$DB->query($query, array(
						'isbn' => $isbn,
						'rating' => $newRating,
						'date' => new DateTime(),
						'cardNumber' => $_SESSION['User']->cardNumber,
					));
			} else {
				// The user hasn't rated this book yet, so insert a row
				$query = "
					INSERT INTO BookRated
						(`CardNumber`,
							`ISBN`,
							`Rating`,
							`Review`,
							`Date`)
					VALUES
						(:cardNumber,
							:isbn,
							:rating,
							'',
							:date)
				";
				$DB->query($query, array(
						'isbn' => $isbn,
						'rating' => $newRating,
						'date' => new DateTime(),
						'cardNumber' => $_SESSION['User']->cardNumber,
					));
			}

			$jsonResult['success'] = true;
		} else {
			$jsonResult['errno'] = 0;
			$jsonResult['errstr'] = 'You cannot rate books if you are not logged in.';
		}
	}
}

?>
