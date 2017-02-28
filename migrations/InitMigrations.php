<?php

namespace Migration;

class InitMigrations extends SqlMigrations
{
	private $filepath;
	private $m_db;

	public function __construct()
	{
		$args = func_get_args();
		$args = $args[0];

		$this->filepath = $args[0]; 
		$this->m_db = $args[2];
		parent::__construct($this->filepath, $args[1], $this->m_db);
	}

	public function up($seed = '')
	{
		$table_created = $this->createMigrationTable();

		if ($table_created && $seed === 'seed') {
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
}