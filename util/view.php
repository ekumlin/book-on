<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class View {
	/**
	 * Loads a view, subtitutes in arguments, and outputs it.
	 *
	 * @param string $name The name of the view file (not including the extension).
	 * @param array $args The arguments to be subtituted into the $viewBag variable in the view.
	 */
	public static function render($name, $args = array()) {
		if (!preg_match(String::FILE_TITLE_REGEX, $name)) {
			throw new Exception("Invalid view name '{$name}'");
		}

		if (!is_array($args)) {
			throw new Exception("Provided arguments were " . gettype($args) . "; expected array");
		}

		$filename = realpath(_ROOT . "views/{$name}.tpl");

		if (!file_exists($filename)) {
			throw new Exception("Could not find view '{$name}'");
		}

		$viewBag = $args;
		require($filename);
	}

	/**
	 * Loads a view, subtitutes in arguments, and returns it as a string.
	 *
	 * @param string $name The name of the view file (not including the extension).
	 * @param array $args The arguments to be subtituted into the $viewBag variable in the view.
	 */
	public static function toString($name, $args = array()) {
		ob_start();
		self::render($name, $args);
		return ob_get_clean();
	}
}

?>
