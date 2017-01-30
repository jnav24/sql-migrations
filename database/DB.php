<?php

namespace Database;

class DB
{
	private $dbh;
		private $error;
		private $stmt;

		public function __construct($host, $db, $user, $pass) {
			$dsn = 'mysql:host=' . $host . ';dbname=' . $db;
			$user = $user;
			$pass = $pass;

			$options = array(
			    \PDO::ATTR_PERSISTENT => true, 
			    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
			);

			try {
			    $this->dbh = new \PDO($dsn, $user, $pass, $options);
			}
			catch (PDOException $e) {
			    $this->error = $e->getMessage();
			    // die($this->error);
			}

		}

		// This closes the connection and keeps the server from 
		// slowing down.
		public function __destruct() {
			try {
			    $this->dbh = null;
			}
			catch (PDOException $e) {
				$this->error = $e->getMessage();
				die($this->error);
			}
		}

		public function query($query){
		    $this->stmt = $this->dbh->prepare($query);
		}

		public function bind($param,$value,$type = null) {
			if (is_null($type)) {
				switch (true) {
					case is_int($value):
						$type = \PDO::PARAM_INT;
						break;
					case is_bool($value):
						$type = \PDO::PARAM_BOOL;
						break;
					case is_null($value):
						$type = \PDO::PARAM_NULL;
						break;
					default:
						$type = \PDO::PARAM_STR;
				}
			}

			$this->stmt->bindValue($param, $value, $type);
		}

		public function execute() {
			return $this->stmt->execute();
		}

		public function getAll() {
			$this->execute();
			return $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
		}

		public function getSingle() {
			$this->execute();
			return $this->stmt->fetch(\PDO::FETCH_ASSOC);
		}

		public function rowCount() {
		    return $this->stmt->rowCount();
		}

		public function lastInsertId() {
		    return $this->dbh->lastInsertId();
		}

		public function beginTransaction() {
			return $this->stmt->beginTransaction();
		}

		public function endTransaction() {
			return $this->stmt->commit();
		}

		public function cancelTransaction() {
		    return $this->dbh->rollBack();
		}

		public function debugDumpParams() {
		    return $this->stmt->debugDumpParams();
		}
}