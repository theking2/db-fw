<?php declare(strict_types=1);
namespace DB;

use Generator;

trait DBPersistTrait
{	/* #region helpers */

	/** _q - wrap fields in backticks */
	private static function _q(string $field): string
	{
		return '`' . $field . '`';
	}
	/** 
	 * wrapFieldArray - wrap field names in backticks and precede with table name
	 *
	 * @param  array $fields
	 * @return string
	 */
	static private function wrapFieldArray(array $fields): string
	{
		return static::getTableName() . '.`' . implode('`, ' . self::getTableName() . '.`', $fields) . '`';
	}
	/**
	 * getFieldNames - get field names from the parts, great for Iterators
	 *
	 * @param  mixed $withID - include the key
	 * @return array
	 */
	static private function getFieldNames(?bool $withID = true): array
	{
		if( $withID ) return array_keys(static::getFields());

		$result = [];
		foreach( array_keys(static::getFields() ) as $field) {
			if( $field != static::getPrimaryKey() ) {
				$result[] = $field;
			}
		}
		return $result;
	}
	/**
	 * getFieldPlaceholders - get placeholders for fields prefixed with :
	 * @param bool $withID - include ID field
	 * @return string - placeholders
	 */
	static private function getFieldPlaceHolders( ?bool $withID = true ):string
	{
		if( $withID ) return ":".implode( ',:', self::getFieldNames() );

		$result = [];
		foreach( self::getFieldNames($withID) as $fieldname ) {
			if( $fieldname !== static::getPrimaryKey() ) {
				$result[] = ':'.$fieldname;
			}
		}
		return implode(',', $result);
	}
	/**
	 * getFieldList
	 *
	 * @param  mixed $_dirty - if true, only dirty fields are returned
	 * @return string
	 */
	private function getUpdateFieldList( ?bool $ignore_dirty=false ): string
	{
		$result = [];
		foreach( static::getFields( ) as $field=> $description ) {
			// don't update primary key
			if( $field === static::getPrimaryKey() ) continue;
			if( $ignore_dirty or in_array( $field, $this-> _dirty ) ) {
				$result[] = "`$field` = :$field";
			}
		}
		return implode( ',', $result );
	}
	/**
	 * getFieldList return the list with or without PK column
	 * @param bool $withID - include ID field
	 */
	static protected function getSelectFields( ?bool $withID=false ):string
	{
		return static::wrapFieldArray( static::getFieldNames($withID) );
	}
	/**
	 * bindFieldList
	 *
	 * @param  mixed $stmt
	 * @param  mixed $_dirty - if true, only dirty fields are bound
	 * @return void
	 */
	private function bindFieldList( \PDOStatement $stmt, ?bool $ignore_dirty=false ): bool
	{
		$result = true;
		foreach( static::getFields( ) as $field=> $description ) {
			// don't update primary key
			if( $field === static::getPrimaryKey() ) continue;
			if( $ignore_dirty or in_array( $field, $this-> _dirty ) ) {
				$result = $result && $stmt->bindParam( ':'.$field, $this-> $field );
			}
		}
		return $result;
	}
	/**
	 * Create insert buffer as string[]
	 *
	 * @param  mixed $stmt
	 * @param  mixed $_dirty - if true, only dirty fields are bound
	 * @return void
	 */
	private function bindValueList( \PDOStatement $stmt, ?bool $ignore_dirty=false ): bool
	{
		$result = true;
		$this-> _insert_buffer = [];
		foreach( static::getFields( ) as $field=> $description ) {
			// don't update primary key
			if( $field === static::getPrimaryKey() ) continue;
			if( $ignore_dirty or in_array( $field, $this-> _dirty ) ) {
				$result = $result && $stmt->bindValue( ':'.$field, 	$this-> _insert_buffer[] = $this-> getFieldString($field) );
			}
		}
		return $result;
	}
	/* #endregion helpers */

	// #region getters and setters

	/**
	 * __setter for all fields
	 *
	 * @param  string $field
	 * @param  mixed $value
	 * @return void
	 */
	public function __set(string $field, $value): void
	{
		/** convert to DateTime type */
		$convert_date = function ($value, $format): ?\DateTime {
			if (is_null($value)) return null;
			if (gettype($value) === 'string') {
				if ($d = \DateTime::createFromFormat($format, $value)) {
					return $d;
				}
				throw new \InvalidArgumentException("Invalid date format: $value");
			}
			// we assume a DateTime here 
			return $value;
		};
		switch ($this->getFields()[$field][0]) {
			default:
				$this->$field = $value;
				break;
			case '\DateTime':
				$this->$field = $convert_date($value, 'Y-m-d H:i:s');
				break;
			case 'Date':
				$this->$field = $convert_date($value, 'Y-m-d');
				break;
			case 'int':
				$this->$field = (int)$value;
				break;
			case 'float':
				$this->$field = (float)$value;
				break;
			case 'bool':
				$this->$field = (bool)$value;
				break;
			case 'unsigned':
				$this->$field = (int)$value;
				break;
		}
		$this->_dirty[] = $field;
	}
	// #endregion

	/**
	 * Constructor calls the parent constructor to initialize the field values
	 * @param mixed $param The primary key value or an array of field values
	 * @param array $where An array of where clauses (see getWhere())
	 * @param array $order An array of order clauses (see getOrder())
	 */
	public function __construct(mixed $param = null, array $where = [], array $order = [])
	{
		$this->_where = $where;
		$this->_order = $order;
		parent::__construct($param);
	}

	/* #region CRUD */

	/**
	 * create – create a new record in the database or update an existing one
	 * @return bool
	 */
	public function freeze():bool
	{
		if( $this-> isRecord() ) {
			return $this->update();
		}
		return $this->insert();
	}

	/**
	 * thaw – fetch a record from the database by key
	 * this assumes keys are singel and ints!!
	 *
	 * @param  int $id
	 * @return object
	 */
	public function thaw($id): ?\Persist\IPersist
	{
    $query = sprintf
      ( 'select %s from %s where `%s` = :ID'
      , static::getSelectFields( false )
      , static::getTableName()
      , static::getPrimaryKey( )
		);
		$stmt = Database::getConnection()->prepare($query);
		if( !$stmt ) {
			throw DatabaseException::createStatementException( Database::getConnection(), "Could not prepare for {$this-> getTableName()}:%s)" );
		}
		$stmt->setFetchMode( \PDO::FETCH_INTO | \PDO::FETCH_PROPS_LATE, $this );

    if( !$stmt-> execute([':ID'=>$id]) ) {
			throw DatabaseException::createExecutionException( $stmt, "Could not execute for {$this-> getTableName()}:%s)" );
		}

		if ($stmt->fetch()) {
			switch( $this->getFields()[$this->getPrimaryKey()][0] ) {
				case 'int':
					$this-> {$this->getPrimaryKey()} = (int)$id;
					break;
				case 'string':
					$this-> {$this->getPrimaryKey()} = (string)$id;
					break;
				default:
					throw new \Exception("Unknown type for primary key");
			}
			$this-> _dirty = [];
			return $this;
		} else {
			$this-> {$this->getPrimaryKey()} = null;
			$this-> _dirty = [];
			return null;
		}
	}

	/**
	 * Insert a new record in the database
	 * NOTE: This is not thread save as between the execute and lastInsertId another
	 * sql statement could occur yielding the wrong ID to be set.
	 * @return bool
	 */
	protected function insert(): bool
	{
		try {
			if( $this->getInsertStatement()->execute( ) ) {
				$this->{$this->getPrimaryKey()} = (int)Database::getConnection( )->lastInsertId( );
				$this-> _dirty = [];
				return true;
			} else {
				throw DatabaseException::createExecutionException(
					$this->insert_statement, "Could not insert in {$this->getTableName()}:%s" );
			}

		} catch( \PDOException $e ) {
			throw DatabaseException::createExecutionException(
				$this->insert_statement, "Could not insert in {$this->getTableName()}:%s)"
			);
		}
	}
	/**
	 * Synchronize changes in Database
	 * @return bool
	 */
	protected function update(): bool
	{
		try {

			if( $this->getUpdateStatement() ->execute( ) ) {
				$this-> _dirty = [];
				return true;
			}

			throw DatabaseException::createExecutionException(
				$this-> update_statement, "Could not update {$this->getTableName()}:%s"
			);

		} catch( \PDOException $e ) {
			throw DatabaseException::createExecutionException(
				$this-> update_statement, "Could not update {$this->getTableName()}:%s"
			);
		}
	}
	/**
	 * Datensatz $this->ID aus der Tabelle entfernen
	 * If $constraint is set than use this to select the records to delete
	 * If $constraint is not set than delete thre record by ID
	 */
	public function delete()
	{
		try {

			if( $this->getDeleteStatement()-> execute( ) ) {
				$this-> _dirty = [];
				$this->{$this-> getPrimaryKey()} = 0;
				return true;
			}
			throw DatabaseException::createExecutionException(
				$this-> delete_statement, "Could not delete {$this->getTableName()}:%s"
			);

		} catch( \PDOException $e ) {
			throw DatabaseException::createExecutionException(
				$this-> delete_statement, "Could not delete from {self->getTableName()}:%s"
			);
		}
	}
	/* #endregion CRUD */

	/* #region Traversal */

	/** array $_where assoc array of fieldnames and operators */
	private array $_where = [];
	/** array $_order assoc array of fieldnames and sort direction */
	private array $_order = [];
	/**	array $_insert_buffer assoc array of fieldsnames and values */
	private array $_insert_buffer = [];

	/** @var \PDOStatement $current_statement contains the statement for traversal */
	private ?\PDOStatement $current_statement = null;

	/**
	 * findFirst – find the first record in the database
	 * @uses _where
	 * @uses _order
	 * @throws DatabaseException
	 * @return void
	 */
	function findFirst()
	{
		$query = sprintf(
			'select %s from %s',
			self::getSelectFields(true),
			$this->getTableName()
		);
		$query .= $this->getWhere();
		$query .= $this->getOrderBy();
		try {
			if( !$stmt = Database::getConnection()-> prepare($query) ) {
				throw DatabaseException::createStatementException(
					Database::getConnection(), "Could not prepare statement for {$this->getTableName()}:%s"
				);
			}
			if( !$this-> bindWhere($stmt) ) {
				throw DatabaseException::createExecutionException(
					$stmt, "Could not bind where in {$this->getTableName()}:%s"
				);
			}

			$stmt->setFetchMode( \PDO::FETCH_INTO | \PDO::FETCH_PROPS_LATE, $this );

			if( !$stmt-> execute() ) {
				throw DatabaseException::createExecutionException(
					$stmt, "Could not execute statement for {$this->getTableName()}:%s"
				);
			}
			if( $stmt-> fetch() ) {
				$this-> current_statement = $stmt;
				$this-> valid = true;
				$this-> _dirty = [];
			} else {
				$this-> valid = false;
			}
		} catch( \PDOException $e ) {
			$errorInfo = $stmt-> errorInfo();
			$message = sprintf(
				'Could not find %s, (%s)',
				$this->getTableName(),
				$errorInfo[2]
			);
			throw new DatabaseException( DatabaseException::ERR_STATEMENT, $e, $message );
		}
	}
	/**
	 * function findNext navigate to next record
	 * new record available
	 */
	public function findNext()
	{
		try {
			if( $this->current_statement->fetch() ) {
				$this->valid = true;
				$this->_dirty = [];
				return true;
			} else {
				$this-> valid = false;
				return false;
			}
		} catch( \PDOException $e ) {
			throw DatabaseException::createExecutionException(
				$this->current_statement, "Could not find next in {$this->getTableName()}:%s"
			);
		}
	}
	/**
	 * find – find records in the database
	 * @uses _where
	 * @uses _order
	 * @throws DatabaseException
	 * @return object
	 */
	public static function find(array $where = [], array $order = []): ?static
	{
		$obj = new static(where: $where, order: $order);
		$obj->findFirst();
		if ($obj->valid) {
			return $obj;
		}
		return null;
	}
	/* #endregion Traversal */

	/* #region Order */
	/**
	 * @var array $_order contains the order by clause
	 * @example ['id' => 'asc', 'name' => 'desc']
	 * @return object
	 */
	public function setOrder(array $order): object
	{
		$this->_order = $order;
		return $this;
	}
	/**
	 * Set Sort order
	 * @return string
	 */
	private function getOrderBy(): string
	{
		$order = [];
		foreach ($this->_order as $fieldname => $direction) {
			if ($this->_isField($fieldname)) {
				$order[] = "`$fieldname` $direction";
			} else throw new \InvalidArgumentException(sprintf('Field %s does not exist in %s', $fieldname, $this->getTableName()));
		}
		if (count($order) > 0) {
			return ' order by ' . implode(', ', $order);
		}
		return '';
	}

	/**
	 * 
	 * @return void 
	 * @throws DatabaseException 
	 */
	public static function findAll( ?array $where =[], ?array $order=[] ): Generator
	{
		$obj = (new static)-> setWhere($where)-> setOrder($order);
		for(
			$obj-> findFirst();
			$obj-> valid;
			$obj-> findNext()
		) {
			yield $obj-> {$obj->getPrimaryKey()} => $obj;
		}
	}
	/* #endregion Order */

	/* #region whereByExample */
	/**
	 * array of fields/values to select by
	 *
	 * @param  mixed $where
	 * @return void
	 */
	public function setWhere(array $where): object
	{
		$this->_where = $where;
		return $this;
	}
	/**
	 * Construct sql where clause by example, the operators are
	 * = equal
	 * ! not equal
	 * * like
	 * < smaller
	 * > greater
	 * & bitwise and
	 * | bitwise or
	 * ^ bitwise xor
	 * U IN values
	 * @return string where string clause
	 *
	 */
	private function getWhere(): string
	{
		$where = ['0=0']; // do nothing
		foreach( $this->_where as $fieldname => $filter ) {
			if( strstr('=!*<>&|^~', substr($filter, 0, 1)) ) {
				$operator = substr($filter, 0, 1);

				// Pop off the first character
				$this->__set( $fieldname, substr($filter, 1) );

				// Special case of the SQL 'IN' operator
				if( $operator === '~' ) {

					// We store the comma seperated operand list as array of values
					// which will be bound later
					$in_values = explode(',', $filter);

					// create a comma seperated list of numbered placeholders
					// "IN (:name_1,:name_2,....)"
					$in_section = [];
					for ($i = 0; $i < count($in_values); $i++) {
						$in_section[] = ":{$fieldname}_{$i}";
					}
					$in_section = implode(',', $in_section);

					$where[] = "`$fieldname` IN ($in_section)";
				} else {

					switch ($operator) {
						case '!':	$operator = '<>';	break;
						case '*':	$operator = 'like';	break; // the reason why the operands are swapped
						case '<': $operator = '>'; break; // the operands are swapped!
						case '>':	$operator = '<'; break; // the operands are swapped!
						default:	break; // we take the operator as it is for all other cases
					}

					$where[] = "`$fieldname` $operator :$fieldname";
				}
			} else {
				// no operator, we assume '='
				$this->__set($fieldname, $filter);
				$where[] = "`$fieldname` = :$fieldname";
			}
		}
		$where = implode(' and ', $where);
		return ' where ' . $where;
	}
	/**
	 * Bind the set values to the statement
	 * @param PDOStatement $stmt - 
	 */
	private function bindWhere( $stmt ): bool
	{
		$result = true;
		foreach( $this->_where as $fieldname => $filter ) {
			if( substr($filter, 0, 1) === '~' ) {
				$in_values = explode(',', substr($filter, 1));
				for( $i = 0; $i < count($in_values); $i++ ) {
					$result = $result && $stmt->bindValue(":{$fieldname}_{$i}", $in_values[$i]);
				}
			} else {
				$result = $result && $stmt->bindValue( ":$fieldname", $this->getFieldString($fieldname) );
			}
		}
		return $result;
	}

	/* #endregion */

	/* #region Cached Statements */
	/** @var \PDOStatement $insert_statement bind fields to values */
	private $insert_statement = null;
	/**
	 * Create Statement, bind to object members and save
	 * return cached select statement
	 * Result columns are bind to the fields
	 * @return \PDOStatement 
	 */
	protected function getInsertStatement(): \PDOStatement
	{
		if( is_null($this-> insert_statement) ) {
			$query = sprintf(
				'insert into %s(%s) values(%s)',
				static::getTableName(),
				static::getSelectFields( false ),
				static::getFieldPlaceholders( false )
			);

			$this-> insert_statement = Database::getConnection()-> prepare( $query );
			if( !$this->insert_statement ) {
				throw DatabaseException::createStatementException(
					Database::getConnection(), "Could not prepare insert statement for {$this->getTableName()}:%s" );
			}
			if( !$this-> bindValueList( $this-> insert_statement, true ) ) {
				throw DatabaseException::createStatementException(
					Database::getConnection(), "Could not bind insert statement for {$this->getTableName()}:%s" );
			}
		}
		return $this-> insert_statement;
	}
	/**
	 * Create Statement, bind to object members and save
	 * return cached select statement
	 * Result columns are bind to the fields
	 * @return \PDOStatement 
	 */
	private function getUpdateStatement(): \PDOStatement
	{
		$query = sprintf(
			'update %s set %s where %s = :ID',
			static::getTableName(),
			$this-> getUpdateFieldList( false ),
			static::getPrimaryKey()
		);

		$result = Database::getConnection( )-> prepare( $query );
		if( !$result ) {
			throw DatabaseException::createStatementException(
				Database::getConnection(), "Could not prepare update statement for {$this->getTableName()}:%s"
			);
		}
		if( !$result-> bindParam( ':ID', $this-> {$this-> getPrimaryKey()} ) ) {
			throw DatabaseException::createStatementException(
				Database::getConnection(), "Could not bind ID to update statement for {$this->getTableName()}:%s" );
		}
		if( !$this-> bindValueList( $result, false ) ) {
			throw DatabaseException::createStatementException(
				Database::getConnection(), "Could not bind update statement for {$this->getTableName()}:%s"
			);
		}

		return $result;
	}
	/** @var \PDOStatement $delete_statement bind ID param to PK */
	private ?\PDOStatement $delete_statement = null;
	/**
	 * Create Statement, bind to object members and save
	 * return cached select statement
	 * Result columns are bind to the fields
	 * @return \PDOStatement 
	 */
	protected function getDeleteStatement()
	{
		if( is_null($this-> delete_statement) ) {
			$query = sprintf( 'delete from %s where `%s` = :ID',
				static::getTableName(),
				static::getPrimaryKey()
			);
			$this-> delete_statement = Database::getConnection()-> prepare( $query );
			if( !$this-> delete_statement ) {
				throw DatabaseException::createStatementException(
					Database::getConnection(), "Could not prepare delete statement {$this->getTableName()}:%s"
				);
			}
			if( !$this-> delete_statement->bindParam( ':ID', $this->{$this-> getPrimaryKey()}) ) {
				throw DatabaseException::createStatementException(
					Database::getConnection(), "Could not bind ID to delete statement {$this->getTableName()}:%s"
				);
			}
		}
		return $this->delete_statement;
	}
	/* #endregion */


}