<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class Log {
	/**
	 * Writes a single-line message to a log file.
	 *
	 * @param string $message The line of text to write
	 */
	public static function writeLine($message) {
		file_put_contents(_ROOT . 'logs/' . date('Y_d_m') . '.txt', date('[H:i:s]: ') . $message . PHP_EOL, FILE_APPEND);
	}
}

?>
