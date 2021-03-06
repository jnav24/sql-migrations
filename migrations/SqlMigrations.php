<?php

namespace Migration;

class SqlMigrations
{
	private $db;
	protected $filepath;
	private $m_db;
	private $ignore_errors = false;

	public function __construct($params)
	{
		if (!$params[1] instanceof \Database\DB || !$params[2] instanceof \Database\DB) {
			throw new \Exception('Must be from Database DB instance.');
		}

		$this->db = $params[1];
		$this->filepath = $params[0];
		$this->m_db = $params[2];
	}

	public function up()
	{
		$new_migrations = $this->getMigrations();

		if (count($new_migrations)) {
			$this->runMigrations($new_migrations);
			return;
		}

		echo "There are no new migrations.\n";
	}

	public function checkForNewMigration($option = '')
	{
		$new_migrations = $this->getMigrations();

		if (count($new_migrations)) {
			$plural = (count($new_migrations) > 1 ? 's' : '');
			$grammar = (count($new_migrations) > 1 ? 'are' : 'is');
			echo "There {$grammar} " . count($new_migrations) ." new migration{$plural}:\n";
			foreach ($new_migrations as $migration) {
				echo "\t{$migration}\n";
			}

			if (!empty($option)) {
				$method = 'run' . ucfirst($option);
				if (method_exists($this, $method)) {
					$this->{$method}($new_migrations);
				}
			}

			return;
		}

		echo "There are no new migrations.\n";
	}

	private function runIgnore($migrations)
	{
		$this->ignore_errors = true;
		$this->runMigrations($migrations);
	}

	private function getMigrations()
	{
		$migrationData = $this->getMigrationData();
		$migrationFiles = $this->getMigrationFiles();
		return $this->getNewMigrationsInFiles($migrationData, $migrationFiles);
	}

	private function getMigrationData()
	{
		$sql = "SELECT filename FROM " . env()->getEnv('MY_DB_TABLE');
		$this->m_db->query($sql);
		return $this->m_db->getAll();
	}

	protected function getMigrationFiles()
	{
		$files = scandir($this->filepath);
		$migrations = [];

		foreach ($files as $file) {
			if (substr($file, -4) === '.sql') {
				$migrations[] = $file;
			}
		}

		return $migrations;
	}

	protected function insertMigration($migration)
	{
		$sql = "INSERT INTO " . env()->getEnv('MY_DB_TABLE') . " ";
		$sql .= "(filename, migrated_at) VALUES ";
		$sql .= "(:filename, :timestamp)";
		$this->m_db->query($sql);
		$this->m_db->bind(':filename', $migration);
		$this->m_db->bind(':timestamp', date('y-m-d H:i:s', strtotime('now')));
		$this->m_db->execute();
		echo "Migration " . $migration . " has been set.\n";
	}

	private function getNewMigrationsInFiles($all_data, $files)
	{
		$new_migrations = [];

		foreach ($files as $file) {
			if (array_search($file, array_column($all_data, 'filename')) === false) {
				$new_migrations[] = $file;
			}
		}

		return $new_migrations;
	}

	private function runMigrations($migrations)
	{
		$exitCode = 0;
		$output = '';
		$path = rtrim($this->filepath, '/') . '/';
		foreach ($migrations as $migration) {
			$exec = "mysql -u" . env()->getEnv('DB_USER') . " "; 
			$exec .= "-p" . env()->getEnv('DB_PASS') . " ";
			$exec .= env()->getEnv('DB_NAME') . " < " . $path . $migration;
			$exec .= " 2>&1";
			exec($exec, $output, $exitCode);

			if ($exitCode == 0) {
				$this->insertMigration($migration);
			}
			else {
				if ($this->ignore_errors) {
					$this->insertMigration($migration);
				}
				else {
					$this->returnMigrationError($migration, $output);	
				}
			}
		}
	}

	private function returnMigrationError($file, array $error)
	{
		$message = '';
		for ($i = 0; $i < count($error); $i++) {
			if (strpos($error[$i], 'Warning:') === false) {
				$message .= "\t" . $error[$i] . "\n";
			}
		}
		echo "There is a mysql error in {$file}.\n" . $message . "\n\n";
	}
}