<?php

namespace Migration;

class InitMigrations extends SqlMigrations
{
	private $filepath;
	private $m_db;

	public function __construct($filepath, \Database\DB $db, \Database\DB $m_db)
	{
		$this->filepath = $filepath;
		$this->m_db = $m_db;
		parent::__construct($filepath, $db, $m_db);
	}

	public function up()
	{
		if ($this->createMigrationTable()) {
			$migrations = $this->getMigrationFiles();

			foreach ($migrations as $migration) {
				$this->insertMigration($migration);
			}
		}		
	}

	private function createMigrationTable()
	{
		$sql = "CREATE TABLE IF NOT EXISTS " . env()->getEnv('MY_DB_TABLE') . " (";
		$sql .= "`id` int(11) NOT NULL AUTO_INCREMENT,";
  		$sql .= "`filename` varchar(255) NOT NULL,";
  		$sql .= "`migrated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,";
  		$sql .= "PRIMARY KEY (`id`)";
  		$sql .= ")";
  		$this->m_db->query($sql);
  		return $this->m_db->execute();
	}

	public function executeBaseMigration()
	{
		die('executing...');
		$this->runMigrations($this->getMigrationFiles());
	}
}