<?php declare(strict_types=1);
namespace DB;

/**
 * Database – Singelton class for database access
 */
final class Database
{
	/** @param \DB\Database $db */
	private static $db;      // Instance of Database
	/** @param \PDO $connection */
	private \PDO $connection;     // PDO Connection

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
		$db_options = 
			[ \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
			, \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_BOUND
			, \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
			, \PDO::ATTR_PERSISTENT => true
			, \PDO::ATTR_EMULATE_PREPARES => false
			];

		$this-> connection =  new \PDO( $dsn, $db_user, $db_pass, $db_options );
	}

	/**
	 * Retrieve the connection object
	 * construct the first time
	 * @return \PDO
	 */
	public static function getConnection(): \PDO
	{
		if( static::$db == null ) {
			static::$db = new Database();
		}
		return static::$db->connection;
	}

  /**
	 * create a customized Exception
	 * @return DatabaseException
	 */
	public function getException(): \DB\DatabaseException
	{
		return new DatabaseException( 0x2100, null, $this->connection->errorInfo()[2] );
	}
}
