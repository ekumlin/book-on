<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class String {
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
}

?>
