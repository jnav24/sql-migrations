<?php

namespace Migration;

class CommandMigrations
{
	private $commands;
	private $db;
	private $m_db;
	private $path;

	public function __construct()
	{
		$this->commands = $this->options();
		$args = func_get_args();

		if (!empty($args)) {
			$args = $args[0];
			$this->path = $args[0]; 
			$this->db = $args[1]; 
			$this->m_db = $args[2];
		}
	}

	public function options()
	{
		return [
			'-c' => [
				'description' => 'Runs a check for new migrations.',
				'exec' => [
					'obj' => 'SqlMigrations',
					'method' => 'checkForNewMigration',
					'params' => [$this->path, $this->db, $this->m_db]
				]
			],
			'-h' => [
				'description' => 'Display help commands',
				'exec' => [
					'obj' => 'CommandMigrations',
					'method' => 'listAllCommands',
				]
			],
			'-i' => [
				'description' => 'Creates migration table. By default, this command does not import all migration files into the migration table.' . "\n\n\t\t" . '`-i seed`' . "\n\t\t" . 'Import the migration files to migration table.' . "\n",
				'exec' => [
					'obj' => 'InitMigrations',
					'method' => 'up',
					'params' => [$this->path, $this->db, $this->m_db]
				]
			],
			'-e' => [
				'description' => 'Export/Import all your table structures from the database specified in the env file. By default, this command only exports all your tables but not the data.' . "\n\n\t\t" . '`-e <table_name>`' . "\n\t\t" . 'Export specific table.' . "\n\n\t\t" . '`-e seed`' . "\n\t\t" .'Export all tables with data.'  . "\n\n\t\t" . '`-e seed--<table_name>`' . "\n\t\t" .'Export specific table with data.' . "\n\n\t\t" .'`-e import`' . "\n\t\t" .'Import all exported data.' . "\n",
				'exec' => [
					'obj' => '',
					'method' => '',
					'params' => [$this->path, $this->db]
				]
			]
		];
	}

	public function listAllCommands()
	{
		echo "SQL Migrations\n";
		echo "Author: Justin Navarro\n\n";
		echo "Usage: migrate [options] [ -i ] [arguments]\n";
		echo "\t migrate -i\n\n";
		echo "Options:\n";

		foreach ($this->commands as $option => $description) {
			echo "\t{$option}\t{$description['description']}\n";
		}

		echo "\nDocumentation can be found at http://github.com/jnav24\n";
	}

	public function buildMigrateArray($args)
	{
		$delimiter = '-';
		$run = [];
		unset($args[0]);

		for ($i = 1; $i <= count($args); $i++) {
			if (strpos($args[$i], $delimiter) > -1) {
				$value = '';

				if (isset($args[($i+1)]) && strpos($args[($i+1)], $delimiter) === false) {
					$value = $args[($i+1)];
				}

				if (!array_key_exists($args[$i], $this->commands)) {
					echo "The value `" . $args[$i] . "` does not exist. \n";
					echo "\tRun `migrate -h`, if you want to know the list of commands.\n";
					die();
				}

				$run[$args[$i]] = $value;
			}
		}

		return $run;
	}
}