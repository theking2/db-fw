<?php declare(strict_types=1);
namespace DB;

trait PersistTrait
{
  private ?\PDOStatement $current_statement = null;
	private array $_dirty = [];
	private array $_where = [];


	/* #region helpers */
	/** isRecord - true if record exists in database */
	public function isRecord():bool { return isset($this-> {static::getPrimaryKey()}) and $this-> {static::getPrimaryKey()} > 0; } 
	
	/** getKeyValue - retrieve current key */
	public function getKeyValue(): ?int { return $this-> isRecord()? $this-> {static::getPrimaryKey()}: null; }

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
	private function getUpdateFieldList( ?bool $_dirty=false ): string
	{
		$result = [];
		foreach( static::getFields( ) as $field=> $description ) {
			// don't update primary key
			if( $field === static::getPrimaryKey() ) continue;
			if( $_dirty or in_array( $field, $this-> _dirty ) ) {
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
	private function bindFieldList( \PDOStatement $stmt, ?bool $ignore_dirty=false ): void
	{
		foreach( static::getFields( ) as $field=> $description ) {
			// don't update primary key
			if( $field === static::getPrimaryKey() ) continue;
			if( $ignore_dirty or in_array( $field, $this-> _dirty ) ) {
				$stmt->bindParam( ':'.$field, $this-> $field );
			}
		}
	}
	/* #endregion helpers */
	
  /* #region CRUD */
	
	/**
	 * create – create a new record in the database or update an existing one
	 * @return bool
	 */
	public function freeze( ):bool
	{
		if( $this-> {$this->getPrimaryKey()} ) {
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
    $stmt-> setFetchMode( \PDO::FETCH_INTO, $this );

    if( !$stmt-> execute([':ID'=>$id]) ) throw new \Exception($stmt->errorInfo()[2]);
		if( $stmt-> fetch( \PDO::FETCH_INTO ) ) {
			$this-> {$this->getPrimaryKey()} = $id;
			$this-> _dirty = [];
			return $this;
		} else {
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
				$errorInfo = $this->insert_statement->errorInfo( );
				$message = sprintf( 'Could not save "%s". (%s)', static::getTableName(), $errorInfo[2] );
				throw new DatabaseException( DatabaseException::ERR_STATEMENT, NULL, $message );
				return false;
			}
		} catch( \Exception $e ) {
			error_log( $e-> getMessage() );
			return false;
		}
	}
	/**
	 * Synchronize changes in Database
	 * @return bool
	 */
	protected function update( ):bool
	{
		try {
			if( $this->getUpdateStatement()->execute( ) ) {
				$this-> _dirty = [];
				return true;
			}
			$errorInfo = $this->getUpdateStatement()->errorInfo( );
			$message = sprintf( 'Could not save %s. (%s):', static::getTableName(), $errorInfo[2], $this-> update_statement-> debugDumpParams() );
			throw new DatabaseException( DatabaseException::ERR_STATEMENT, NULL, $message );

		} catch( \Exception $e ) {
			error_log( $e-> getMessage() );
			return false;
		}
	}
		/**
	 * Datensatz $this->ID aus der Tabelle entfernen
	 * If $constraint is set than use this to select the records to delete
	 * If $constraint is not set than delete thre record by ID
	 */
	public function delete( )
	{
		if( $this->getDeleteStatement()-> execute( ) ) {
			$this-> _dirty = [];
			$this->{$this-> getPrimaryKey()} = 0;
		}
		else {
			$errorInfo = $this->getDeleteStatement()-> errorInfo( );
			$message = sprintf( 'Could not delete %s, (%s)', $this->getTableName(), $errorInfo[2] );
			throw new \Exception( $message );
		}
	}
	/* #endregion CRUD */

	/* #region Traversal */
  /**
   * findFirst
   *
   * @param  mixed $order
   * @return void
   */
  function findFirst(?string $order=null) {
    $query = sprintf( 'select %s from %s', self::getSelectFields(true), $this->getTableName() );
    $query .= ' where ' . $this-> getWhere();
    if( !is_null($order) && is_array($order) ) {
      $query .= sprintf(' order by ');
      $query .= static::wrapFieldArray($order);
    }
    $stmt = Database::getConnection()-> prepare($query);
    $this-> bindWhere($stmt);
    $stmt-> setFetchMode( \PDO::FETCH_INTO, $this );

    if( !$stmt-> execute() ) throw new \Exception($stmt->errorInfo()[2]);
    if( $stmt-> fetch() ) {
      $this-> current_statement = $stmt;
      $this-> valid = true;
			$this-> _dirty = [];
    } else {
      $this-> valid = false;
    }
  }
  /**
	 * function findNext navigate to next record
	 * new record available
	 */
	public function findNext( )
	{
    if($this->current_statement->fetch()) {
		  $this-> valid = true; 
			$this-> _dirty = [];
      return true;
    } else {
      $this-> valid = false;
      return false;
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
	private function bindWhere( $stmt )
	{
		foreach( $this-> _where as $fieldname => $filter ) {
			if(substr($filter,0,1)==='U' ) {
				$in_values = explode( ',', substr($filter,1) );
				for( $i=0; $i < count($in_values); $i++ ) {
					$stmt->bindValue( ":{$fieldname}_{$i}", $in_values[$i] );
				}
			} else {
				$stmt->bindValue( ":$fieldname", substr($filter,1) );
			}
		}
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

			// echo $query . '<br>';
			$this-> insert_statement = Database::getConnection( )->prepare( $query );
			$this-> bindFieldList( $this-> insert_statement, true );
		}
		return $this-> insert_statement;
	}
	/** @var \PDOStatement $update_statement bind ID param to PK; bind fields to */
	private $update_statement = null;
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
			, $this-> getUpdateFieldList( true )
			, static::getPrimaryKey( ) );

		$result = Database::getConnection( )-> prepare( $query );
		$result-> bindParam( ':ID', $this-> {$this-> getPrimaryKey( )} );		
		$this-> bindFieldList( $result, false );

		return $result;
	}
	/** @var \PDOStatement $delete_statement bind ID param to PK */
	private $delete_statement = null;	
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
			$this->delete_statement->bindParam( ':ID', $this->{$this-> getPrimaryKey()} );
		}
		return $this->delete_statement;
	}
  /* #endregion */

  /* #region getters/setters */  

  /**
   * __get
   *
   * @param  mixed $field
   * @return mixed
   */
  public function __get(string $field) { return $this->{$field};}
  /**
   * __setter for all fields
   *
   * @param  string $field
   * @param  mixed $value
   * @return void
   */
  public function __set(string $field, $value):void {
    switch($this-> getFields()[$field][0]) {
      default : $this-> $field = $value; break;
      case '\DateTime' : $this-> $field = \DateTime::createFromFormat('Y-m-d', $value); break;
      case 'int' : $this-> $field = (int)$value; break;
      case 'float' : $this-> $field = (float)$value; break;
      case 'bool' : $this-> $field = (bool)$value; break;
      case 'unsigned' : $this-> $field = (int)$value; break;
    }
		$this-> _dirty[] = $field;
  }
  /* #endregion */

	/* #region generic
	/**
   * getArrayCopy - Returns an array copy of the object
   *
   * @return array
   */
  public function getArrayCopy(): array
  {
    $array = [];
    foreach( array_keys( $this->getFields() ) as $field ) {
      $array[$field] = (string)$this-> $field;
    }
    return $array;
  }  
	/* #endregion */

	
	public function __construct( $param = null )
	{
		if( !is_null( $param ) ) {
			if( !\is_array( $param ) ) {
				$this-> thaw( $param );
			} else {
				$this-> setFromArray($param);
			}
		} else {
			$this-> _dirty = [];
			$this-> {$this-> getPrimaryKey()} = 0;
		}
	}
  /**
   * createFromArray - Create or set from array
   *
   * @param  mixed $array
   * @return Persist
   */
  static function createFromArray(array $array): \Persist\PersistInterface {
		$obj = new self();
		$obj-> setFromArray( $array );
    return $obj;
  }	
	/**
	 * setFromArray
	 *
	 * @param  mixed $array
	 * @return Persist\PersistInterface
	 */
	function setFromArray(array $array): \Persist\PersistInterface {
		foreach($array as $field=>$value) {
			$this->__set( $field, $value );
		}
		return $this;
	}
}