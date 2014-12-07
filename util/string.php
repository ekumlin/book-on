<?php

class String {
	/**
	 * Takes in a number and returns a string representing the ordinal ("nth"). Based on solution found here: http://stackoverflow.com/a/3110033/1438733
	 *
	 * @param string $name The name of the template file (not including the extension).
	 * @param array $args The arguments to be subtituted into the $viewBag variable in the template.
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
