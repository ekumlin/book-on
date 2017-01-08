<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class StringUtil {
	const FILE_TITLE_REGEX = '@^[^/?*:;{}\\\\]+$@';

	/**
	 * Takes in a number and returns a string representing the ordinal ("nth"). Based on solution found here: http://stackoverflow.com/a/3110033/1438733
	 *
	 * @param string $number The number to get the orginal of. Can be a string or integer.
	 */
	public static function ordinal($number) {
		$value = intval($number) % 100;

		if ($value >= 11 && $value <= 13) {
			return strval($number) . 'th';
		}

		$ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
		return strval($number) . $ends[$value % 10];
	}

	/**
	 * Formats either an ISBN-13 or ISBN-10 for human reading.
	 *
	 * @param string $number The ISBN number. Can be an integer or string.
	 */
	public static function formatIsbn($isbn) {
		$isbn = strval($isbn);

		if (strlen($isbn) == 13) {
			return substr($isbn, 0, 3) . '-' . substr($isbn, 3);
		}

		return $isbn;
	}
}

?>
