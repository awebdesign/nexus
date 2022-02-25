<?php

namespace Aweb\Nexus\Database;

use PDO;

final class PdoAdapter {

	private $connection = null;
	private $statement = null;
    private $charset = 'utf8'; //original was: utf8
    private $collation = 'utf8_general_ci';//original was: utf8_general_ci
    private $options = [
        //PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //Triggers Errors

        //PDO::ATTR_CASE => PDO::CASE_NATURAL,
        //PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        //PDO::ATTR_STRINGIFY_FETCHES => false,
        //PDO::ATTR_EMULATE_PREPARES => false,
        //PDO::ATTR_PERSISTENT => false,
        //PDO::MYSQL_ATTR_SSL_CA => false,
        //PDO::ATTR_EMULATE_PREPARES,
    ];

    /**
     * Opencart PDO database driver
     *
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param string $database
     * @param string $port
     */
	public function __construct($hostname, $username, $password, $database, $port = '3306') {
		try {
			$this->connection = new \PDO("mysql:host=" . $hostname . ";port=" . $port . ";dbname=" . $database, $username, $password, $this->options);
		} catch(\PDOException $e) {
			throw new \Exception('Failed to connect to database. Reason: \'' . $e->getMessage() . '\'');
		}

        $this->charset = defined('DB_CHARSET') ? DB_CHARSET : $this->charset;
        $this->collation = defined('DB_COLLATION') ? DB_COLLATION : $this->collation;

		$this->connection->prepare("set names '{$this->charset}' collate '{$this->collation}'")->execute();

        //$this->connection->exec("SET NAMES '{$this->charset}'");
		//$this->connection->exec("SET CHARACTER SET {$this->charset}");
		//$this->connection->exec("SET CHARACTER_SET_CONNECTION={$this->charset}");
		$this->connection->exec("SET SQL_MODE = ''");
        //$this->connection->prepare("set session sql_mode='NO_ENGINE_SUBSTITUTION'")->execute();
        if (defined('DB_TIMEZONE')) {
            $this->connection->prepare('set time_zone="' . DB_TIMEZONE . '"')->execute();
        }
	}

	public function prepare($sql) {
		$this->statement = $this->connection->prepare($sql);
	}

	public function bindParam($parameter, $variable, $data_type = \PDO::PARAM_STR, $length = 0) {
		if ($length) {
			$this->statement->bindParam($parameter, $variable, $data_type, $length);
		} else {
			$this->statement->bindParam($parameter, $variable, $data_type);
		}
	}

	// public function execute() {
	// 	try {
	// 		if ($this->statement && $this->statement->execute()) {
	// 			$data = array();

	// 			while ($row = $this->statement->fetch(\PDO::FETCH_ASSOC)) {
	// 				$data[] = $row;
	// 			}

	// 			$result = new \stdClass();
	// 			$result->row = (isset($data[0])) ? $data[0] : array();
	// 			$result->rows = $data;
	// 			$result->num_rows = $this->statement->rowCount();
	// 		}
	// 	} catch(\PDOException $e) {
	// 		throw new \Exception('Error: ' . $e->getMessage() . ' Error Code : ' . $e->getCode());
	// 	}
	// }

	public function query($sql, $params = array()) {
		$this->statement = $this->connection->prepare($sql);

		$result = false;

		try {
			if ($this->statement && $this->statement->execute($params)) {
				$data = array();

                /**
                 * Get FETCH_ASSOC if the query is a SELECT or a SHOW command
                 */
                $queryData = explode(' ', strtolower(trim($this->statement->queryString)));
                $queryType = isset($queryData[0]) ? trim($queryData[0]) : null;
                if(in_array($queryType, ['select', 'show'])) {
                    while ($row = $this->statement->fetch(\PDO::FETCH_ASSOC)) {
					    $data[] = $row;
				    }
                }

				$result = new \stdClass();
				$result->row = (isset($data[0]) ? $data[0] : array());
				$result->rows = $data;
				$result->num_rows = count($data) ? $this->statement->rowCount() : 0;
			}
		} catch (\PDOException $e) {
			throw new \Exception('Error: ' . $e->getMessage() . ' Error Code : ' . $e->getCode() . ' <br />' . $sql);
		}

		if ($result) {
			return $result;
		} else {
			$result = new \stdClass();
			$result->row = array();
			$result->rows = array();
			$result->num_rows = 0;
			return $result;
		}
	}

    /**
     * Opencart default escape method
     *
     * @param mixed $value
     * @return void
     */
	public function escape($value) {
		return str_replace(array("\\", "\0", "\n", "\r", "\x1a", "'", '"'), array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"'), $value);
	}

    /**
     * Affected rows
     *
     * @return int
     */
	public function countAffected() {
		if ($this->statement) {
			return $this->statement->rowCount();
		} else {
			return 0;
		}
	}

    /**
     * Last inserted ID
     *
     * @return string|false
     */
	public function getLastId() {
		return $this->connection->lastInsertId();
	}

    /**
     * Connection checking method
     *
     * @return boolean
     */
	public function isConnected() {
		if ($this->connection) {
			return true;
		} else {
			return false;
		}
	}

	public function __destruct() {
		$this->connection = null;
	}

    /**
     * Connection link shared
     *
     * @return void
     */
    public function shareConnection() {
		return $this->connection;
	}
}
