<?php 

require 'bootstrap.php';

$path = env()->getEnv('MIGRATION_PATH');
$params = [$path, $db, $m_db];
$command = new Migration\CommandMigrations($params);



if (isset($argv[1])) {
	$args = $command->buildMigrateArray($argv);
	$options = $command->options();

	foreach ($args as $arg => $value) {
		if (!empty($options[$arg]) && !empty($options[$arg]['exec']['obj'])) {
			$class = "Migration\\".$options[$arg]['exec']['obj'];

			if (!empty($options[$arg]['exec']['params'])) {
				$obj = new $class($options[$arg]['exec']['params']);
			}
			else {
				$obj = new $class();
			}

			$obj->$options[$arg]['exec']['method']($value);
		}
	}
}
else {
	$migrate = new Migration\SqlMigrations($path, $db, $m_db);
	$migrate->up();
}