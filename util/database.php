<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class Connection {
	private $db;
	private $queryAffectedRows;

	/**
	 * Creates a new database connection instance.
	 *
	 * @param string $host The host string for the database.
	 * @param string $user The username to connect with.
	 * @param string $password The password for the given username.
	 * @param string $db The name of the database to connect to.
	 */
	public function __construct($host, $user, $password, $db) {
		try {
			$this->db = new PDO("mysql:host={$host};dbname={$db}", $user, $password, array(
				PDO::ATTR_PERSISTENT => true,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
			));
		} catch (PDOException $pdoe) {
			$code = $pdoe->getCode();
			$msg = $pdoe->getMessage();
			$line = $pdoe->getFile();
			$file = $pdoe->getLine();

			Log::writeLine("PDO Exception {$code} on line {$line} of {$file}: {$msg}");
		}
	}

	/**
	 * Get the number of affected rows in the previous query.
	 *
	 * @return integer The number of affected rows.
	 */
	public function affectedRows() {
		return $this->queryAffectedRows;
	}

	/**
	 * Get the ID of the last inserted row.
	 *
	 * @return integer The ID of the last inserted row.
	 */
	public function lastInsertedId() {
		return $this->db->lastInsertId();
	}

	/**
	 * Performs a query on the PDO object.
	 *
	 * @param string $queryString The executable query as a PDO-ready string.
	 * @param array $args The set of arguments for the query. Should have numeric indices if using '?' parameters, or keys prefixed by ':' if using named parameters.
	 * @return array The returned values from the query, or an empty array otherwise.
	 */
	public function query($queryString, $args = array()) {
		$stmt = $this->db->prepare($queryString);
		$result = array();

		foreach ($args as $k => $v) {
			$args[$k] = $this->parameterize($v);
		}

		if ($stmt->execute($args)) {
			if ($stmt->columnCount() > 0) {
				$result = $stmt->fetchAll();
			}
		}

		$this->queryAffectedRows = $stmt->rowCount();

		return $result;
	}

	/**
	 * Prepares an object for insertion into the database.
	 *
	 * @param object $obj Any object, scalar, or array that needs to be prepared for use as a parameter.
	 * @return array The returned values from the query, or an empty array otherwise.
	 */
	public function parameterize($obj) {
		if (is_object($obj)) {
			$class = get_class($obj);
			if ($class == 'DateTime') {
				return date_format($obj, 'Y/m/d H:i:s');
			}
		}

		return $obj;
	}
}

?>
