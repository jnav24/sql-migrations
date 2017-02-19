<?php

namespace Migration;

class InitMigrations extends SqlMigrations
{
	private $filepath;
	private $m_db;
	private $run_migration_files = false;

	public function __construct($filepath, \Database\DB $db, \Database\DB $m_db, $run_migration_files)
	{
		$this->filepath = $filepath;
		$this->m_db = $m_db;
		$this->run_migration_files = $run_migration_files;
		parent::__construct($filepath, $db, $m_db);
	}

	public function up()
	{
		$table_created = $this->createMigrationTable();

		if ($table_created && $this->run_migration_files) {
			$migrations = $this->getMigrationFiles();

			foreach ($migrations as $migration) {
				$this->insertMigration($migration);
			}
		}		
	}

	private function createMigrationTable()
	{
		$sql = "CREATE TABLE IF NOT EXISTS " . env()->getEnv('M_TABLE') . " (";
		$sql .= "`id` int(11) NOT NULL AUTO_INCREMENT,";
  		$sql .= "`filename` varchar(255) NOT NULL,";
  		$sql .= "`migrated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,";
  		$sql .= "PRIMARY KEY (`id`)";
  		$sql .= ")";
  		$this->m_db->query($sql);
  		return $this->m_db->execute();
	}
}