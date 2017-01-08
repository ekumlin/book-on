<?php

if (!defined('VALID_REQUEST')) {
	define('VALID_REQUEST', true);
}

require_once('init.php');

function apiReject($errno, $errstr, $errfile, $errline, $errcontext) {
	if (!is_dir('logs')) {
		mkdir(_ROOT . 'logs', 0777);
	}

	Log::writeLine("Error {$errno} in {$errfile} on line {$errline}: $errstr");

	die(json_encode(array(
			'success' => false,
			'errno' => $errno,
			'errstr' => $errstr,
		)));

	return true;
}

function apiCall($request) {
	set_error_handler("apiReject");

	global $DB;
	$jsonResult = array(
			'success' => false,
			'data' => array(),
		);

	$controllerFile = $request['controller'] . "Controller";
	if (preg_match(StringUtil::FILE_TITLE_REGEX, $controllerFile)) {
		require_once(_ROOT . "controllers/{$controllerFile}.php");

		$controllerName = ucfirst($controllerFile);
		$controller = new $controllerName;
		$method = $request['action'];

		if (method_exists($controller, $method)) {
			call_user_func_array(array($controller, $method), array($request, &$jsonResult));
		}
	}

	restore_error_handler();
	return json_encode($jsonResult);
}

if (!defined('IS_PAGEVIEW')) {
	parse_str($_SERVER['QUERY_STRING'], $request);
	die(apiCall($request));
}

?>
