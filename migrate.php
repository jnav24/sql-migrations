<?php 

require 'bootstrap.php';

$path = env()->getEnv('MIGRATION_PATH');

if (isset($argv[1])) {
	if ($argv[1] == 'init') {
		$migrate = new Migration\InitMigrations($path, $db, $m_db);
		$migrate->up();

		if (isset($argv[2])) {
			if ($argv[2] == '') {
				runSqlMigrations($path, $db, $m_db);
			}
			else {
				endMigration($argv[2]);
			}
		}
		

		exit;
	}
	else if ($argv[1] == '-e') {
		$export = new Migration\ExportMigrations($path, $db);
		$export->up();
		die();
	}
	else {
		endMigration($argv[1]);
	}
}

function runSqlMigrations($path, $db, $m_db) 
{
	$migrate = new Migration\SqlMigrations($path, $db, $m_db);
	$migrate->up();
}

function endMigration($arg)
{
	echo "What do you mean? {$arg}?\n";
	die();
} 

runSqlMigrations($path, $db, $m_db);