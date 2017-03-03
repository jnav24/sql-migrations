<?php

namespace Migration;

class ExportMigrations
{
	private $db;
	private $export_dir;
	private $path;

	public function __construct($params)
	{
		$this->export_dir = env()->getEnv('EXPORT_DIR_NAME');
		$this->path = rtrim($params[0], '/') . '/'; 
		$this->db = $params[1];
	}

	public function up($option = '')
	{
		if ($this->validateParams($option)) {
			$this->setUp();
			$this->exportTablesNoData();
			$this->exportTableContents();
		}
	}

	private function validateParams($option)
	{
		if (empty($option)) {
			return true;
		} 

		$options = ['import', 'seed'];
		$user_options = explode('--', $option);
		
		/*
		* @TODO:
		* between this condition below and the up()
		* figure out a way to call a method based on the user input
		* or pass it to a property
		*/
		if (in_array($user_options[0], $options)) {
			return true;
		}

		return false;
	}

	private function setUp()
	{
		if (!file_exists($this->path . $this->export_dir)) {
			exec('cd ' . $this->path . ' && mkdir ' . $this->export_dir);
		}
	}

	private function exportTablesNoData()
	{
		$user = env()->getEnv('DB_USER');
		$pass = env()->getEnv('DB_PASS');
		$db_name = env()->getEnv('DB_NAME');
		$file_name = $this->table_name;
		$routines = "";

		if (empty($this->table_name)) {
			$file_name = "all_table_structure";
			$routines = "--routines";
		}

		$exec = "mysqldump -u{$user} -p{$pass} --no-data {$routines} {$db_name} {$this->table_name} > ";
		$exec .= $this->path . "{$this->export_dir}/{$file_name}_migration_" . strtotime('now') . ".sql";
		$exec .= " 2>&1";

		$this->runShellCommand($exec);
	}

	private function exportTableContents()
	{
		$tables = $this->getTables();
		$user = env()->getEnv('DB_USER');
		$pass = env()->getEnv('DB_PASS');
		$db_name = env()->getEnv('DB_NAME');

		foreach ($tables as $table) {
			$exec = "mysqldump -u{$user} -p{$pass} --no-create-info {$db_name} {$table} > ";
			$exec .= $this->path . "{$this->export_dir}/{$table}_seed_" . strtotime('now') . ".sql";
			$exec .= " 2>&1";
			$this->runShellCommand($exec);
		}
	}

	private function getTables()
	{
		$sql = "show tables";

		if (!empty($this->table_name)) {
			$sql .= " like '". $this->table_name ."'";
		}

		$this->db->query($sql);
		$results = $this->db->getColumn();
		return (!$results ? [] : $results);
	}

	private function runShellCommand($command)
	{
		$exitCode = 0;
		$output = '';
		exec($command, $output, $exitCode);

		if ($exitCode) {
			echo "There was an error: \n";
			echo $output;
		}
	}
}