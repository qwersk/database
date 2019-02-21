<?php

namespace Qwersk;

use \PDO;

class Database
{
	private $host;
	private $port;
	private $dbname;
	private $username;
	private $password;
	private $options;
	private $pdo;

    public function __construct($host, $port, $dbname, $username, $password, $options = array())
    {
        $this->host = $host;
        $this->port = $port;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;
        $this->options = $options;
        $this->connect();
    }

    private function connect()
    {
		$dsn = 'mysql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->dbname . ';charset=utf8;';

		$default_options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_FOUND_ROWS => true
        ];
        $options = array_merge($default_options, $this->options);

        $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
    }

    public function run($sql, $args = NULL)
    {
        if (!$args)
        {
            return $this->pdo->query($sql);
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }

	public function buildParams($allowed, $source)
	{
		$set = '';

        foreach ($allowed as $field) {
            if (!array_key_exists($field, $source)) {
                continue;
            }
            $set .= "`" . $field . "`" . " = :$field, ";
        }
        return substr($set, 0, -2); 
	}

    public function close()
    {
    	$this->pdo = null;
    }

    public function __destruct()
    {
    	$this->close();
    }
}
