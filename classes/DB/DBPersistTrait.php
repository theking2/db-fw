<?php declare(strict_types=1);
namespace DB;

trait DBPersistTrait
{
	private array $_where = [];
	private array $_insert_buffer = [];


	/* #region helpers */

  /** _q - wrap fields in backticks */
  private static function _q(string $field):string { return '`'.$field.'`'; }
  /** 
   * wrapFieldArray - wrap field names in backticks and precede with table name
   *
   * @param  array $fields
   * @return string
   */
  static private function wrapFieldArray(array $fields):string {
      return static::getTableName().'.`'.implode('`, '.self::getTableName().'.`', $fields).'`';
  }	
	/**
	 * getFieldNames - get field names from the parts, great for Iterators
	 *
	 * @param  mixed $withID - include the key
	 * @return array
	 */
	static private function getFieldNames(?bool $withID = true):array {
		if( $withID ) return array_keys(static::getFields());
		
		$result = [];
		foreach(array_keys(static::getFields()) as $field) {
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
	
  /* #region CRUD */
	
	/**
	 * create – create a new record in the database or update an existing one
	 * @return bool
	 */
	public function freeze( ):bool
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
  public function thaw(int $id): ?object
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
    $stmt-> setFetchMode( \PDO::FETCH_INTO, $this );

    if( !$stmt-> execute([':ID'=>$id]) ) {
			throw DatabaseException::createExecutionException( $stmt, "Could not execute for {$this-> getTableName()}:%s)" );
		}

		if( $stmt-> fetch( \PDO::FETCH_INTO ) ) {
			$this-> {$this->getPrimaryKey()} = $id;
			$this-> _dirty = [];
			return $this;
		} else {
			$this-> {$this->getPrimaryKey()} = 0;
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
	protected function insert( ): bool
	{
		try {
			if( $this->getInsertStatement()->execute( ) ) {
				$this->{$this->getPrimaryKey()} = (int)Database::getConnection( )->lastInsertId( );
				$this-> _dirty = [];
				return true;
			}
			else {
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
	protected function update( ):bool
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
	public function delete( )
	{
		try {

			if( $this->getDeleteStatement()-> execute( ) ) {
				$this-> _dirty = [];
				$this->{$this-> getPrimaryKey()} = 0;
				return true;
			}
			throw DatabaseException::createExecutionException(
				$this->getDeleteStatement(), "Could not delete from {$this->getTableName()}"
			);

		} catch( \PDOException $e ) {
			throw DatabaseException::createExecutionException(
				$this-> delete_statement, "Could not delete from {self->getTableName()}:%s"
			);
		}
	}
	/* #endregion CRUD */

	/* #region Traversal */

	/** @var \PDOStatemen $current_statement contains the statement for traversal */
  private ?\PDOStatement $current_statement = null;

  /**
   * findFirst
   *
   * @param  string $order
   * @return void
   */
  function findFirst (?string $order=null ) {
    $query = sprintf(
			'select %s from %s',
			self::getSelectFields(true),
			$this->getTableName()
		);
    $query .= ' where ' . $this-> getWhere();
    if( !is_null($order) && is_array($order) ) {
      $query .= sprintf(' order by ');
      $query .= static::wrapFieldArray($order);
    }
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

			$stmt-> setFetchMode( \PDO::FETCH_INTO, $this );

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
	public function findNext( )
	{
		try {
			if($this->current_statement->fetch()) {
				$this-> valid = true; 
				$this-> _dirty = [];
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
  /* #endregion Traversal */

  /* #region whereByExample */
	public function setWhere(array $where):void {
		$this-> _where = $where;
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
	private function getWhere():string
	{
		$where = ['0=0']; // do nothing
		foreach( $this-> _where as $fieldname=> $filter ) {
			if( strstr('=!*<>&|^U', substr( $filter,0, 1 ) ) ) {
				$operator = substr( $filter, 0, 1);

				// Pop off the first character
				$this-> __set($fieldname, substr($filter,1) );

				// Special case of the SQL 'IN' operator
				if( $operator === 'U' ) {

					// We store the comma seperated operand list as array of values
					// which will be bound later
					$in_values = explode(',', $filter);

					// create a comma seperated list of numbered placeholders
					// "IN (:name_1,:name_2,....)"
					$in_section = [];
					for( $i=0; $i < count($in_values); $i++ ) {
						$in_section[] = ":{$fieldname}_{$i}";
					}
					$in_section = implode( ',', $in_section );

					$where[] = "`$fieldname` IN ($in_section)";

				} else {

					switch( $operator ) {
						case '!':	$operator = '<>';	break;
						case '*':	$operator = 'like';	break; // the reason why the operands are swapped
						case '<': $operator = '>'; break; // the operands are swapped!
						case '>':	$operator = '<'; break; // the operands are swapped!
						default:break; // we take the operator as it is for all other cases
					}

					$where[] = ":$fieldname $operator `$fieldname`";
				}
			}
		}
		$where = implode(' and ', $where );
		return $where;
	}
 	/**
	* Bind the set values to the statement
	* @param PDOStatement $stmt - 
	*/
	private function bindWhere( $stmt ): bool
	{
		$result = true;
		foreach( $this-> _where as $fieldname => $filter ) {
			if(substr($filter,0,1)==='U' ) {
				$in_values = explode( ',', substr($filter,1) );
				for( $i=0; $i < count($in_values); $i++ ) {
					$result = $result && $stmt->bindValue( ":{$fieldname}_{$i}", $in_values[$i] );
				}
			} else {
				$result = $result && $stmt->bindValue( ":$fieldname", substr($filter,1) );
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
		if( is_null($this->insert_statement) ) {
			$query = sprintf( 'insert into %s(%s) values(%s)'
				, static::getTableName()
				, static::getSelectFields( false )
				, static::getFieldPlaceholders( false )
				);

			$this-> insert_statement = Database::getConnection( )->prepare( $query );
			if( !$this-> insert_statement ) {
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
	/** @var \PDOStatement $update_statement bind ID param to PK; bind fields to */
	private ?\PDOStatement $update_statement = null;
	/**
	 * Create Statement, bind to object members and save
	 * return cached select statement
	 * Result columns are bind to the fields
	 * @return \PDOStatement 
	 */
	private function getUpdateStatement(): \PDOStatement
	{	
		$query = sprintf( 'update %s set %s where %s = :ID'
			, static::getTableName()
			, $this-> getUpdateFieldList( false )
			, static::getPrimaryKey( ) );

		$result = Database::getConnection( )-> prepare( $query );
		if( !$result ) {
			throw DatabaseException::createStatementException(
				Database::getConnection(), "Could not prepare update statement for {$this->getTableName()}:%s" );
		}
		if( !$result-> bindParam( ':ID', $this-> {$this-> getPrimaryKey( )} ) ) {
			throw DatabaseException::createStatementException(
				Database::getConnection(), "Could not bind ID to update statement for {$this->getTableName()}:%s" );
		}
		if( !$this-> bindValueList( $result, false ) ) {
			throw DatabaseException::createStatementException(
				Database::getConnection(), "Could not bind update statement for {$this->getTableName()}:%s" );
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
		if( is_null( $this->delete_statement) ) {
			$query = sprintf( 'delete from %s where `%s` = :ID'
				, static::getTableName()
				, static::getPrimaryKey( )
			);
			$this->delete_statement = Database::getConnection( )->prepare( $query );
			if( !$this->delete_statement ) {
				throw DatabaseException::createStatementException(
					Database::getConnection(), "Could not prepare delete statement {$this->getTableName()}:%s" );
			}
			if( !$this->delete_statement->bindParam( ':ID', $this->{$this-> getPrimaryKey()} ) ) {
				throw DatabaseException::createStatementException(
					Database::getConnection(), "Could not bind ID to delete statement {$this->getTableName()}:%s" );
			}
		}
		return $this->delete_statement;
	}
  /* #endregion */


}