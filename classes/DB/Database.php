<?php
namespace DB;

final class Database
{
	private static $db;      // Instance of Database
	private $connection;     // PDO Connection

  /**
	 * constructor
	 * the class is a singleton, and the constructor should not be called from
	 * the outside, hence private
	 */
	private function __construct()
	{
		$dsn = 'mysql:dbname=project;host=localhost';
		$db_user = 'project';
		$db_pass = 'pm2021';
		$db_options = array
		    ( \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
		    , \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_BOUND
		    , \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
		  );

		$this-> connection =  new \PDO( $dsn, $db_user, $db_pass, $db_options );
	}
	/**
	 * Destructor
	 * There is no desctructor as the connection is closed automatically e.g set
	 * to null when the script ends
	 */



	/**
	 * Retrieve the connection object
	 * construct the first time
	 * @return PDOConnection 
	 */
	public static function getConnection()
	{
		if( static::$db == null ) {
			static::$db = new Database();
		}
		return static::$db->connection;
	}

  /**
	 * create a customized Exception
	 */
	public function getException()
	{
		return new \db\Exception( 0x2100, null, $this->connection->errorInfo()[2] );
	}
}
