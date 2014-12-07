<?php

class Template {
	public static function toString($name, $args = array()) {
		if (!preg_match('@^[^/?*:;{}\\\\]+$@', $name)) {
			throw new Exception("Invalid template name '{$name}'");
		}

		if (!is_array($args)) {
			throw new Exception("Provided arguments were " . gettype($args) . "; expected array");
		}

		$filename = realpath(_ROOT . "/templates/{$name}.tpl");

		if (!file_exists($filename)) {
			throw new Exception("Could not find template '{$name}'");
		}

		ob_start();
		$viewBag = $args;
		require($filename);
		return ob_get_clean();
	}
}

?>
