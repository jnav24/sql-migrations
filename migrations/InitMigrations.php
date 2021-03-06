<?php

namespace Migration;

class InitMigrations extends SqlMigrations
{
	public function __construct($params)
	{
		$this->filepath = $params[0]; 
		$this->m_db = $params[2];
		parent::__construct($params);
	}

	public function up($option = '')
	{
		$table_created = $this->createMigrationTable();

		if ($table_created && $option === 'seed') {
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