<?php

namespace Migration;

class CommandMigrations
{
	public function __construct()
	{
		$this->commands = $this->options();
	}

	public function options()
	{
		return [
			'-h' => 'display help commands',
			'-i' => 'creates database and tables but does not import data',
			'-e' => 'idk what this does',
			'-m' => 'insert seeds in with the init migration'
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
			echo "\t{$option}\t{$description}\n";
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