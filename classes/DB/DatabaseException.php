<?php declare(strict_types=1);
/**
 * Exception - Stub für einfacher Zugang Exception kalse
 * @package db
 * @version $i$d 
 * @author Johannes Kingma jkingma@sbw-media.ch
 * @copyright 2013 SBWNMAG
 */

namespace db;

/**
 * DatabaseException
 */
class DatabaseException extends \Exception {
	const ERROR_START = 0x2000; // Vieww errors start here
	const ERR_STATEMENT = ERROR_START+0;
	const ERR_NOT_RECORD = ERROR_START+1;
	const ERR_NORECORDS_FOUND = ERROR_START+2;

	private static $messageEN = array
	(	DatabaseException::ERR_STATEMENT=>       'Statement exec error: %s'
	,	DatabaseException::ERR_NOT_RECORD=>      '$record is not \db\Record'
	,	DatabaseException::ERR_NORECORDS_FOUND=> 'no records found'
	);
			
	/**
	 * __construct
	 *
	 * @param  int $code
	 * @param  \Exception $previous
	 * @param  string $message
	 * @return \db\DatabaseException
	 */
	public function __construct (int $code, ?\Exception $previous = NULL, ?string $message = NULL )
	{
		if( $message ){
			parent::__construct( sprintf(self::$messageEN[$code], $message), $code, $previous );
		 } else {
			parent::__construct( self::$messageEN[$code], $code, $previous );
		}
	}
		
	

	final function getMessageLang(string $lang): string
	{
		return self::$messageEN[$this->code];
	}
};