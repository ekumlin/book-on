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
		// TODO Exclude the current URL.

		$returnUrl = _HOST;
		if (isset($_SERVER['HTTP_REFERER']) && (!$exclude || !preg_match($exclude, $_SERVER['HTTP_REFERER']))) {
			$returnUrl = $_SERVER['HTTP_REFERER'];
		}

		header("Location: {$returnUrl}");
		exit;
	}
}

?>
