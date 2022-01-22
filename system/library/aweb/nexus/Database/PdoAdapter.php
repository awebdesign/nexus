<?php

namespace Aweb\Nexus\Database;

use PDO;

final class PdoAdapter {

	private $connection = null;
	private $statement = null;
    private $charset = 'utf8'; //original was: utf8
    private $collation = 'utf8_unicode_ci';//original was: utf8_general_ci
    private $options = [
        //PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //Triggers Errors
    ];

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

	public function execute() {
		try {
			if ($this->statement && $this->statement->execute()) {
				$data = array();

				while ($row = $this->statement->fetch(\PDO::FETCH_ASSOC)) {
					$data[] = $row;
				}

				$result = new \stdClass();
				$result->row = (isset($data[0])) ? $data[0] : array();
				$result->rows = $data;
				$result->num_rows = $this->statement->rowCount();
			}
		} catch(\PDOException $e) {
			throw new \Exception('Error: ' . $e->getMessage() . ' Error Code : ' . $e->getCode());
		}
	}

	public function query($sql, $params = array()) {
		$this->statement = $this->connection->prepare($sql);

		$result = false;

		try {
			if ($this->statement && $this->statement->execute($params)) {
				$data = array();

                if(substr($this->statement->queryString, 0, 6) == 'SELECT') {
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

	public function escape($value) {
		return str_replace(array("\\", "\0", "\n", "\r", "\x1a", "'", '"'), array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"'), $value);
	}

	public function countAffected() {
		if ($this->statement) {
			return $this->statement->rowCount();
		} else {
			return 0;
		}
	}

	public function getLastId() {
		return $this->connection->lastInsertId();
	}

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

    public function shareConnection() {
		return $this->connection;
	}
}
