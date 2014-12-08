<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class Template {
	/**
	 * Loads a template, subtitutes in arguments, and outputs it.
	 *
	 * @param string $name The name of the template file (not including the extension).
	 * @param array $args The arguments to be subtituted into the $viewBag variable in the template.
	 */
	public static function render($name, $args = array()) {
		if (!preg_match('@^[^/?*:;{}\\\\]+$@', $name)) {
			throw new Exception("Invalid template name '{$name}'");
		}

		if (!is_array($args)) {
			throw new Exception("Provided arguments were " . gettype($args) . "; expected array");
		}

		$filename = realpath(_ROOT . "templates/{$name}.tpl");

		if (!file_exists($filename)) {
			throw new Exception("Could not find template '{$name}'");
		}

		$viewBag = $args;
		require($filename);
	}

	/**
	 * Loads a template, subtitutes in arguments, and returns it as a string.
	 *
	 * @param string $name The name of the template file (not including the extension).
	 * @param array $args The arguments to be subtituted into the $viewBag variable in the template.
	 */
	public static function toString($name, $args = array()) {
		ob_start();
		self::render($name, $args);
		return ob_get_clean();
	}
}

?>
