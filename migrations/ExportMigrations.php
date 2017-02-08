<?php

namespace Migration;

class ExportMigrations
{
	private $db;
	private $export_dir;
	private $path;

	public function __construct($path, $db)
	{
		$this->db = $db;
		$this->export_dir = env()->getEnv('EXPORT_DIR_NAME');
		$this->path = $path;
	}

	public function up()
	{
		// $this->setUp();
		// $this->exportTablesNoData();
		$this->exportTableContents();
	}

	private function setUp()
	{
		exec('cd ' . $this->path . ' && mkdir ' . $this->export_dir);
	}

	private function exportTablesNoData()
	{
		$user = env()->getEnv('DB_USER');
		$pass = env()->getEnv('DB_PASS');
		$db_name = env()->getEnv('DB_NAME');
		$exec = "mysqldump -u{$user} -p{$pass} --no-data --routines {$db_name} > ";
		$exec .= rtrim($this->path,'/') . "/{$export_dir}/all_table_structure.sql"
		$exec .= " 2>&1";
		exec($exec);
	}

	private function exportTableContents($table_name = '')
	{
		$tables = $this->getTables($table_name);
		$user = env()->getEnv('DB_USER');
		$pass = env()->getEnv('DB_PASS');
		$db_name = env()->getEnv('DB_NAME');

		foreach ($tables as $table) {
			exec("mysqldump -u{$user} -p{$pass} {$db_name} {$table} > ". rtrim($this->path,'/') . "/{$export_dir}/{$table}.sql";
		}
	}

	private function getTables($table_name)
	{
		$sql = "show tables";

		if (!empty($table_name)) {
			$sql .= " like '". $table_name ."'";
		}

		$this->db->query($sql);
		$results = $this->db->getColumn();
		return (!$results ? [] : $results);
	}
}