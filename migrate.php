<?php 

require 'bootstrap.php';

$path = env()->getEnv('MIGRATION_PATH');
$command = new Migration\CommandMigrations();

if (isset($argv[1])) {
	$args = $command->buildMigrateArray($argv);

	if (array_key_exists('-h', $args)) {
		$command->listAllCommands();
	}
	else if (array_key_exists('-i', $args)) {
		$migrate = new Migration\InitMigrations($path, $db, $m_db, array_key_exists('-m', $args));
		$migrate->up();
	}
}
else {
	$migrate = new Migration\SqlMigrations($path, $db, $m_db);
	$migrate->up();
}