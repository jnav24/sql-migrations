<?php

namespace Migration;

class ExportMigrations
{
	private $db;
	private $export_dir;
	private $path;
	private $table_name = '';
	private $method_name;

	public function __construct($params)
	{
		$this->export_dir = env()->getEnv('EXPORT_DIR_NAME');
		$this->path = rtrim($params[0], '/') . '/'; 
		$this->db = $params[1];
	}

	public function up($option = '')
	{
		if ($this->validateParams($option)) {
		    $method = "run" . ucfirst($this->method_name);
		    $this->{$method}();
		}
	}

    private function runExport()
    {
        $this->setUp();
        $this->exportTablesNoData();
    }

    private function runImport()
    {
        die('import');
    }

    private function runSeed()
    {
        $this->setUp();
        $this->exportTableContents();
    }

	private function validateParams($option)
	{
		if (empty($option)) {
		    $this->method_name = 'export';
			return true;
		} 

		$options = ['import', 'seed'];
		$user_options = explode('_', $option);

		if (in_array($user_options[0], $options)) {
		    $this->method_name = $user_options[0];

		    if (!empty($user_options[1])) {
		        $this->table_name = $user_options[1];
            }

			return true;
		}

		if ($this->tableExists($option)) {
            $this->method_name = 'export';
            $this->table_name = $option;
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

	private function tableExists($table)
    {
        $sql = "SHOW TABLES LIKE :table";
        $this->db->query($sql);
        $this->db->bind(':table', $table);
        $this->db->execute();
        return $this->db->rowCount();
    }

	private function exportTableContents()
	{
	    $tables = array();
	    $tables[] = $this->table_name;

	    if (empty($this->table_name)) {
            $tables = $this->getTables();
        }

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