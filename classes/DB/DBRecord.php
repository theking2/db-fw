<?php

namespace DB;
/**
 * @package DB-Framework
 * @copyright:	2013 SBW Neue Media
 * @version $Revision: $ 
 * @author:	Johannes Kingma
 */
abstract class DBRecord implements \Iterator
{
	protected $dirty = [];     // gab's änderungen?
	protected $fields = null;     // hier werden die gespeichert
	protected $ID = 0;            // ID der Datensatz in der Datenbank, (0 wenn nur in Speicher)
	private $valid = false;

	/**
	 * Iterator implementation
	 */
	public function current ( ) { return $this; }
	public function key ( )	{ return $this->ID; }
	public function valid ( ) { return $this->valid; }
	public function next ( ) { return $this->findNext(); }
	public function rewind ( ) { $this-> findFirst(); }


	/** @var PDOStatement $select_statement bind ID for PK; bind columns for select*/
	private $select_statement = null;
	/**
	 * Create Statement, bind to object members and save
	 * return cached select statement
	 * Result columns are bind to the fields
	 * @return PDOStatement 
	 */
	protected function getSelectStatement()
	{
		if( is_null( $this->select_statement ) ) {
			$query = sprintf
				( 'select %s from `%s` where `%s` = :ID'
				, self::getFieldList( false )
				, static::getTableName()
				, static::getPrimaryKeyName( )
				);
			$this->select_statement = Database::getConnection( )->prepare( $query );
			$this->select_statement->bindParam( ':ID', $this->ID );
			$this->bindColumns( $this->select_statement );
			
		}

		return $this->select_statement;
	}

	/** @var PDOStatement $insert_statement bind fields to values */
	private $insert_statement = null;
	/**
	 * Create Statement, bind to object members and save
	 * return cached select statement
	 * Result columns are bind to the fields
	 * @return PDOStatement 
	 */
	protected function getInsertStatement()
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

	/** @var PDOStatement $update_statement bind ID param to PK; bind fields to */
	private $update_statement = null;
	/**
	 * Create Statement, bind to object members and save
	 * return cached select statement
	 * Result columns are bind to the fields
	 * @return PDOStatement 
	 */
	protected function getUpdateStatement()
	{
		if( is_null($this->update_statement) ) {
			$query = sprintf( 'update `%s` set %s where %s = :ID'
				, static::getTableName()
				, self::getUpdateList( )
				, static::getPrimaryKeyName( ) );

			// echo $query .'<br>';
			$this->update_statement = Database::getConnection( )->prepare( $query );
			$this->update_statement->bindParam( ':ID', $this->ID );		
			$this->bindFields( $this->update_statement );
		}
		return $this->update_statement;
	}

	/** @var PDOStatement $delete_statement bind ID param to PK */
	private $delete_statement = null;	
	/**
	 * Create Statement, bind to object members and save
	 * return cached select statement
	 * Result columns are bind to the fields
	 * @return PDOStatement 
	 */
	protected function getDeleteStatement()
	{
		if( is_null( $this->delete_statement) ) {
			$query = sprintf( 'delete from `%s` where `%s` = :ID'
				, static::getTableName()
				, static::getPrimaryKeyName( )
			);
			$this->delete_statement = Database::getConnection( )->prepare( $query );
			$this->delete_statement->bindParam( ':ID', $this->ID );
		}
		return $this->delete_statement;
	}
	/*/
	 * Constructor
	 * @param int $id - wenn nicht null wird den Wert als ID in die Tabelle verwendet.
	 */
	public function __construct( $id = null )
	{
		$this-> clear();

		if( !is_null( $id ) ) {
			$this->find( $id );
		}
	}

	public function clear()
	{
		$this->zeroFieldList( );
		$this-> ID = 0;

	}

	/**
	 * access the Internal Fields array
	 * @return mixed[] - associative array of current values
	 */
	public function getFields( ) { return $this->fields; }

	/**
	 * Resturn a JSON String
	 * As the ID is not part of the fields array we add it temporarily
	 * @return string JSON string of the content.
	 */
	public function getFieldsJSON( ) { 
		//Add the id temporarily
		$this-> fields += ['ID'=> $this-> ID ];
		
		$result = json_encode( $this->fields );
		
		//remove th the ID;
		unset( $this-> fields['ID'] );
		
		return $result;
	}
	
	/**
	 * Schon im Datenbank?
	 */
	public function isRecord( )	{ return $this->ID != 0; }

	/**
	 * ID ausgeben
	 */
	public function getID( ) {	return $this->ID; }

	/**
	 * Welcher Tabellenname
	 */
	static protected function getTableName() {throw new \Exception('tableName not defined');}
	/**
	 * Was ist die Primary Key spallte?
	 */
	static protected function getPrimaryKeyName() {throw new \Exception('primaryKeyName not defined');}
	/**
	 * In welche Spallten sind wir interessiert?
	 */
	static protected function getFieldNames() {throw new \Exception('fieldNames not defined');}

	/**
	 * Field
	 */
	protected function zeroFieldList()
	{
		foreach( static::getFieldNames() as $fieldname )
		{
			$this->fields[$fieldname] = '';
		}
	}
	/**
	 * Attribut abfragen
	 * @param string $name Name des Attributs
	 */
	public function __get( $name )
	{
		// do not overload existing properties
		if( property_exists( $this, $name ) )
			return $this->$name;

		if( $name === $this->getPrimaryKeyName() )
			return $this->ID;
		if( !array_key_exists( $name, $this->fields ) ) {
			throw new \Exception( 'Unknown parameter ' . $name );
		}
		return $this->fields[$name];
	}

	/**
	 * Attribut setzen
	 * @param string $name Name des Attributs
	 * @param mixed $value Neuer Wert des Attributs
	 */
	public function __set( $name, $value )
	{
		// do not overload existing properties
		if( property_exists( $this, $name ) )
			$this->$name = $value;

		else {
			$this->fields[$name] = $value;
			if( !in_array( $name, $this-> dirty ) ) $this->dirty[] = $name;
		}
	}

	/**
	 * Record in der Datenbank suchen
	 * @param int $id ID to find
	 */
	public function find( $id )
	{
		$this->ID = $id;
		if( $this-> getSelectStatement()->execute( ) ) {
			if( $result = $this-> getSelectStatement()->fetch( \PDO::FETCH_BOUND ) ) {
				return true;
			}
			else {
				$this->zeroFieldList();
				$this->ID = 0;
				$refl = new \ReflectionClass( $this );
				$message = $refl->getFileName( ) . ':Datensatz mit ' . $id . ' nicht gefunden';
				
				return false;
			}
		}
		else {
			$refl = new \ReflectionClass( $this );
			$message = $refl->getFileName( ) . ':Datensatz mit ' . $id . ' nicht gefunden';
			throw new DatabaseException( DatabaseException::ERR_STATEMENT, NULL, $message );
		}
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
	private function getWhereByExample()
	{
		$where = ['0=0']; // do nothing
		foreach( static::getFieldNames() as $fieldname ) {
			if( $this->fields[$fieldname] != '' ) {
				if( strstr('=!*<>&|^U', substr( $this-> $fieldname,0, 1 ) ) ) {
					$operator = substr( $this-> $fieldname, 0, 1);

					// Pop off the first character
					$this->fields[$fieldname] = substr($this->fields[$fieldname],1);

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
					$this-> fields[$fieldname] = '';
				}
			}
		}
		$where = implode(' and ', $where );
		return $where;
	}

	/** @var PDOStatement $current_statement */
	private $current_statement = null;
	/**
	 * Datensätze suchen
	 * @param string[] $order - array von fields
	 * @param string $where 
	 * @return Record - instance of actual record
	 */
	public function findFirst( $order = null )
	{
		$query = sprintf( 'select %s from `%s`'
			, self::getFieldList( true )
			, static::getTableName()
		);
		
		$query .= ' where ' . $this-> getWhereByExample();
		
		if( $order && is_array( $order ) ) {
			$query .= ' order by ';
			$query .= '`' . static::getTableName() . '`.`';
			$query .= implode( '`, `' . static::getTableName() .'`.`', $order );
			$query .= '`';
		}

		$stmt = Database::getConnection( )->prepare( $query );
		$this-> bindValueByExample( $stmt );

		try {
			if( $stmt->execute( ) ) {
				$this-> bindColumns( $stmt, true );

				if( $result = $stmt->fetch( \PDO::FETCH_BOUND ) ) {
					$this->valid = true;
					$this->current_statement = $stmt;

					return $this;
				} else {
					$this->valid = false;
					$this->current_statement = null;

					return false;
				}

			}
			else {
				$message = sprintf( 'Could not find %s, (%s)', static::getTableName(), $query );
				throw new \Exception( $message );
			}
		} catch( \Exception $e ) {
			$stmt-> debugDumpParams();

			_log( $e-> getMessage() );
		}
	}
	/**
	 * function findNext navigate to next record
	 * new record available
	 */
	public function findNext( )
	{
		return $this-> valid = $this->current_statement->fetch( \PDO::FETCH_BOUND );
	}
	/**
	* Bind the set values to the statement
	* @param PDOStatement $stmt - 
	*/
	private function bindValueByExample( $stmt )
	{
		foreach( static::getFieldNames() as $fieldname )
		{
			if( is_array($this-> fields[$fieldname]) ) {
				for( $i=0; $i < count($this-> fields[$fieldname]); $i++) {
					$stmt->bindValue( ':'.$fieldname.'_'.$i, $this->fields[$fieldname][$i] );
				}

			} elseif( $this->fields[$fieldname] != '' ) {
				$stmt->bindValue( ":$fieldname", $this->fields[$fieldname] );
			}
			$this-> fields[$fieldname] = null;
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
			$this->dirty = array();
			$this->ID = 0;
		}
		else {
			$errorInfo = $this->getDeleteStatement()-> errorInfo( );
			$message = sprintf( 'Could not delete %s, (%s)', $this->getTableName(), $errorInfo[2] );
			throw new \Exception( $message );
		}
	}

	/**
	 * Record in Datenbank speichern
	 * If $id is not either update the record by id or insert a new record
	 * If $id is set than insert a new record under that ID
	 * Update is not supported wiht a specified $id
	 */
	public function store( )
	{
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
	 * Synchronize changes in Database
	 * @return void
	 */
	protected function update( )
	{
		if( $this->getUpdateStatement()->execute( ) ) {
			$this->dirty = array();
		}
		else {
			$errorInfo = $this->getUpdateStatement()->errorInfo( );
			$message = sprintf( 'Could not save %s. (%s):', static::getTableName(), $errorInfo[2], $this-> update_statement-> debugDumpParams() );
			throw new DatabaseException( DatabaseException::ERR_STATEMENT, NULL, $message );
		}
	}

	/**
	 * getFieldList return the list with or without PK column
	 * @param bool $withID - true when including parameter
	 */
	static protected function getFieldList( $withID = false )
	{
		if( $withID )
			$result = '`' . static::getTableName( ) . '`' .
				'.`' . static::getPrimaryKeyName( ) . '`, ';
		else
			$result = '';
	
		return $result .= '`' . static::getTableName( )	. '`.' . 
			'`' . implode( '`, `'.static::getTableName( ) . '`.`', static::getFieldNames( ) ) . '`';
	}

	/**
	 * getFieldPlaceholders - 
	 * @return string - all PDO place holders prefixed :
	 */
	static protected function getFieldPlacholders( )
	{
		return ':' . implode( ',:', static::getFieldNames( ) );
	}

	/**
	 * getUpdateList - SQL updates section
	 * @return string
	 */
	static private function getUpdateList( )
	{
		$result = array( );
		foreach( static::getFieldNames( ) as $field ) {
			if( $field === static::getPrimaryKeyName() ) continue;
			$result[] = '`' . $field . '`=:' . $field;
		}
		return implode( ',', $result );
	}

	/**
	 * Bind the fields to PDO placeholders
	 * @param PDOStatement $stmt statement that the fields are bound to
	 * @return void
	 */
	protected function bindFields( $stmt )
	{
		foreach( array_keys($this->fields) as $field ) {
			// skip PK
			if( $field === static::getPrimaryKeyName() ) continue;
			$stmt->bindParam( ':' . $field, $this->fields[$field] );
		}
	}
	/**
	 * Bind the fields to the placeholders
	 * @param PDOStatement $stmt - that the fields are bind to
	 * @param bool $withID - include the ID as result as well
	 * @return void
	 */
	protected function bindColumns( $stmt, $withID = false )
	{
		if( $withID )
			$stmt->bindColumn( static::getPrimaryKeyName(), $this->ID );

		foreach( static::getFieldNames() as $fieldname ) {
			$stmt->bindColumn( $fieldname, $this->fields[$fieldname] );
		}	
	}

	/**
	 * parseResultset
	 * Set the values of the select results, resets dirty (object is in sync)
	 * @param mixed[] $result - associative array
	 */
	protected function parseResultset( $result )
	{
		foreach( $result as $field=> $value ) {
			if( $field === static::getPrimaryKeyName() )
				$this->ID = $value;
			$this->fields[$field] = $value;
		}
		$this->dirty = array();
	}

}
