<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class Http {
	/**
	 * Returns the user to the previous URL using header(). Returns to the homepage if no return URL is available.
	 *
	 * @param string $exclude The regex of any return URL to exclude. (Optional)
	 */
	public static function back($exclude = NULL) {
		// Default to homepage
		$returnUrl = _HOST;

		// Try to find the referring URL (not always reliable)
		if (isset($_SERVER['HTTP_REFERER']) && (!$exclude || !preg_match($exclude, $_SERVER['HTTP_REFERER']))) {
			$returnUrl = $_SERVER['HTTP_REFERER'];
		}

		// Exclude the current URL
		if (strstr($returnUrl, $_SERVER['REQUEST_URI'])) {
			$returnUrl = _HOST;
		}

		// Pass as a header and end the script execution
		header("Location: {$returnUrl}");
		exit;
	}

	/**
	 * Checks if the currently logged-in user has access to a clearance specified.
	 *
	 * @param integer $required The access level required to compare to. Should be a constant from the User class.
	 */
	public static function canAccess($required) {
		$userLevel = isset($_SESSION['User']) ? $_SESSION['User']->employeeLevel : User::USER_NONE;
		return $userLevel >= $required;
	}
}

?>
