<?php
/**
 * Exception - Stub für einfacher Zugang Exception kalse
 * @package db
 * @version $i$d 
 * @author Johannes Kingma jkingma@sbw-media.ch
 * @copyright 2013 SBWNMAG
 */

namespace db;

class DatabaseException extends \Exception {
	const ERROR_START = 0x2000; // Vieww errors start here
	const ERR_STATEMENT =0x2000;
	const ERR_NOT_RECORD = 0x2001;
	const ERR_NORECORDS_FOUND = 0x2002;

	private static $messageEN = array
	(	self::ERR_STATEMENT=>       'Statement exec error: %s'
	,	self::ERR_NOT_RECORD=>      '$record is not \db\Record'
	,	self::ERR_NORECORDS_FOUND=> 'no records found'
	);
	/**
	 * Constructor uses code to find the English error text
	 * @param $code int - ::ERR condant
	 * @param $previous Excption
	 */
	public function __construct ($code, $previous = NULL, $message = NULL )
	{
		if( $message )
			parent::__construct( sprintf(self::$messageEN[$code], $message), $code, $previous );
		else
			parent::__construct( self::$messageEN[$code], $code, $previous );

	}
	
	final function getMessageLang(string $lang)
	{
		return self::$messageEN[$this->code];
	}
};