<?php declare(strict_types=1);
namespace DB;

trait Persist
{
  private ?\PDOStatement $current_statement = null;

  /* #region CRUD */
    
  /**
   * thaw – fetch a record from the database
   *
   * @param  mixed $id
   * @return object
   */
  public function thaw(int $id): object
  {
    $query = sprintf
      ( 'select %s from %s where `%s` = :ID'
      , static::getFieldList( false )
      , static::getTableName()
      , static::getPrimaryKey( )
      );
    $stmt = Database::getConnection()->prepare($query);
    $stmt-> setFetchMode( \PDO::FETCH_INTO, $this );

    if( !$stmt-> execute([':ID'=>$id]) ) throw new \Exception($stmt->errorInfo()[2]);
		if( $obj = $stmt-> fetch( \PDO::FETCH_INTO ) ) {
			$this-> {$this->getPrimaryKey()} = $id;
			return $obj;
		} else {
			return null;
		}
  }

	/**
	 * create – create a new record in the database or update an existing one
	 */
	public function freeze( )
	{
		if()
		if( $this->isRecord( ) )
			$this->update( );
		else
			$this->insert( );
	}

	/**
	 * Neuer Datensatz hinzufügen
	 * NOTE: This is not thread save as between the execute and lastInsertId another
	 * sql statement could occur yielding the wrong ID to be set.
	 */
	protected function insert( )
	{
		try {
			if( $this->getInsertStatement()->execute( ) ) {
				$this->ID = Database::getConnection( )->lastInsertId( );
				$this->dirty = array();
			}
			else {
				$errorInfo = $this->insert_statement->errorInfo( );
				$message = sprintf( 'Could not save "%s". (%s)', static::getTableName(), $errorInfo[2] );
				throw new DatabaseException( DatabaseException::ERR_STATEMENT, NULL, $message );
			}
		} catch( \Exception $e ) {
			error_log( $e-> getMessage() );
		}
	}


  /**
   * findFirst
   *
   * @param  mixed $order
   * @return void
   */
  function findFirst(?string $order=null) {
    $query = sprintf( 'select %s from %s', $this->getFieldList(true), $this->getTableName() );
    $query .= ' where ' . $this-> getWhereByExample();
    if( !is_null($order) && is_array($order) ) {
      $query .= sprintf(' order by ');
      $query .= static::wrapFieldArray($order);
    }
    $stmt = Database::getConnection()-> prepare($query);
    $this-> bindValueByExample($stmt);
    $stmt-> setFetchMode( \PDO::FETCH_INTO, $this );

    if( !$stmt-> execute() ) throw new \Exception($stmt->errorInfo()[2]);
    if( $stmt-> fetch() ) {
      $this-> current_statement = $stmt;
      $this-> valid = true;
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
      return true;
    } else {
      $this-> valid = false;
      return false;
    }
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
      case 'DateTime' : $this-> $field = \DateTime::createFromFormat('Y-m-d', $value); break;
      case 'integer' : $this-> $field = (int)$value; break;
      case 'float' : $this-> $field = (float)$value;
      case 'unsigned' : $this-> $field = (int)$value; break;
    }
  }
  /* #endregion */

  /* #region whereByExample */
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
	private function getWhereByExample():string
	{
		$where = ['0=0']; // do nothing
		foreach( static::getFields() as $fieldname=> $description ) {
			if( isset($this-> $fieldname) ) {
				if( strstr('=!*<>&|^U', substr( $this-> $fieldname,0, 1 ) ) ) {
					$operator = substr( $this-> $fieldname, 0, 1);

					// Pop off the first character
					$this-> $fieldname = substr($this-> $fieldname,1);

					// Special case of the SQL 'IN' operator
					if( $operator === 'U' ) {

						// We store the comma seperated operand list as array of values
						// which will be bound later
						$this-> fields[$fieldname] = explode(',', $this-> fields[$fieldname]);

						// create a comma seperated list of numbered placeholders
						// "IN (:name_1,:name_2,....)"
						$in_section = [];
						for( $i=0; $i < count($this-> fields[$fieldname]); $i++ ) {
							$in_section[] = ':'.$fieldname.'_'.$i;
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
				} else {
					// Clear other values
					//$this-> fields[$fieldname] = '';
				}
			}
		}
		$where = implode(' and ', $where );
		return $where;
	}
  	/**
	* Bind the set values to the statement
	* @param \PDOStatement $stmt - 
	*/
	private function bindValueByExample( $stmt )
	{
		foreach( array_keys(static::getFields()) as $fieldname )
		{
      if( !isset($this-> $fieldname) ) continue;

      // Special case of the SQL 'IN' operator
			if( is_array($this-> $fieldname) ) {
				for( $i=0; $i < count($this-> $fieldname); $i++) {
					$stmt->bindValue( ':'.$fieldname.'_'.$i, $this-> $fieldname[$i] );
				}
      }

			$stmt->bindValue( ":$fieldname", $this->$fieldname );
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
			$query = sprintf( 'insert into `%s`(%s) values( %s )'
				, static::getTableName()
				, self::getFieldList( )
				, self::getFieldPlacholders( )
				);

			// echo $query . '<br>';
			$this->insert_statement = Database::getConnection( )->prepare( $query );
			$this->bindFields( $this->insert_statement );
		}
		return $this->insert_statement;
	}
	/**
	 * getFieldPlaceholders - 
	 * @return string - all \PDO place holders prefixed :
	 */
	static protected function getFieldPlacholders( )
	{
		return ':' . implode( ',:', static::getFieldNames( ) );
	}
	/** @var \PDOStatement $update_statement bind ID param to PK; bind fields to */
	private $update_statement = null;
	/**
	 * Create Statement, bind to object members and save
	 * return cached select statement
	 * Result columns are bind to the fields
	 * @return \PDOStatement 
	 */
	protected function getUpdateStatement()
	{
		if( is_null($this->update_statement) ) {
			$query = sprintf( 'update `%s` set %s where %s = :ID'
				, static::getTableName()
				, self::getUpdateList( )
				, static::getPrimaryKey( ) );

			// echo $query .'<br>';
			$this->update_statement = Database::getConnection( )->prepare( $query );
			$this->update_statement->bindParam( ':ID', $this->ID );		
			$this->bindFields( $this->update_statement );
		}
		return $this->update_statement;
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
			$query = sprintf( 'delete from `%s` where `%s` = :ID'
				, static::getTableName()
				, static::getPrimaryKey( )
			);
			$this->delete_statement = Database::getConnection( )->prepare( $query );
			$this->delete_statement->bindParam( ':ID', $this->ID );
		}
		return $this->delete_statement;
	}
  /* #endregion */

  /* #region general purpose*/
	public function isRecord():bool { return $this-> {static::getPrimaryKey()} > 0; }
  private static function _q(string $value):string { return '`'.$value.'`'; }
  
	/**
	 * getFieldList return the list with or without PK column
	 * @param bool $withID - true when including parameter
	 */
	static protected function getFieldList( ?bool $withID = false ):string
	{
		if( $withID )
			$result = static::getTableName( ). '.' . static::_q(static::getPrimaryKey( )) . ', ';
		else
			$result = '';
	
		return $result .= static::wrapFieldArray(array_keys(static::getFields( )));
	}
  static private function wrapFieldArray(array $fields):string {
      return static::getTableName().'.`'.implode('`, '.static::getTableName().'.`', $fields).'`';
  }
  /* #endregion */

  /* #region Iterator */
  /** @var bool $valid true if a valid object */
  private bool $valid = false;
  
	public function current ( ): object { return $this; }
	public function key ( )	{ return $this-> {$this->getPrimaryKey()} ; }
	public function valid ( ): bool { return $this-> valid; }
	public function next ( ): void { $this-> findNext(); }
	public function rewind ( ): void { $this-> findFirst(); }
  /* #endregion */
}