<?php

namespace Migration;

class CommandMigrations
{
	private $init_set = false;

	public function __construct($migrate_obj)
	{
		$this->migrate_obj = $migrate_obj;
	}

	public function options()
	{
		return [
			'-h' => 'help',
			'-i' => 'init',
			'-e' => 'execute'
		];
	}

	public function initMigration()
	{
		if ($this->init_set) {
			echo "Run '-i' before any other option\n";
			return false;
		}

		$this->migrate_obj->up();
		$this->init_set = true;
	}

	public function helpMigration()
	{
		echo "Here are some options \n";
		print_r(options());
	}

	public function executeMigration()
	{
		$this->migrate_obj->executeBaseMigration();
		$this->init_set = false;
	}
}