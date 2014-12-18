<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class Controller {
	/**
	 * Checks whether a user has access to the current controller, and throws an error string to the JSON result if not.
	 *
	 * @param integer $required The access level required for the controller.
	 * @param array $jsonResult A bundle that holds the JSON result of the regular controller call. Requires success element to be true or false.
	 */
	public static function verifyAccess($required, &$jsonResult) {
		if (!Http::canAccess($required)) {
			$jsonResult['errno'] = 0;
			$jsonResult['errstr'] = 'Invalid access.';
			return false;
		}

		return true;
	}
}

?>
