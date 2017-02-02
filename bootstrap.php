<?php

require_once __DIR__ . '/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Global functions
|--------------------------------------------------------------------------
|
| Global functions that returns a provider class
|
*/

$providers = [
    'env' => Provider\Env::getInstance(),
];

foreach ($providers as $funcName => $class)
{
    $GLOBALS[$funcName] = $class;
    eval("function $funcName() { global $$funcName; return $$funcName; }");
}

/*
|--------------------------------------------------------------------------
| Environment Setup
|--------------------------------------------------------------------------
|
| Sets up the environment based on the .env file outside the App directory.
|
*/

env()->setPath(__DIR__.'/');
env()->load();

/*
|--------------------------------------------------------------------------
| Schema Setup and Init
|--------------------------------------------------------------------------
|
| The schema is the migration of custom tables located in the App\Migrations
|
|
*/

$db = new Database\DB(
	env()->getEnv('DB_HOST'), 
	env()->getEnv('DB_NAME'), 
	env()->getEnv('DB_USER'), 
	env()->getEnv('DB_PASS'));
$m_db = new Database\DB(
	env()->getEnv('M_HOST', env()->getEnv('DB_HOST')), 
	env()->getEnv('M_NAME', env()->getEnv('DB_NAME')), 
	env()->getEnv('M_USER', env()->getEnv('DB_USER')), 
	env()->getEnv('M_PASS', env()->getEnv('DB_PASS')));