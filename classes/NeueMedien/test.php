<?php declare(strict_types=1);

namespace NeueMedien;

/*
 * test – Persistant object
 */
class test implements \DB\DBRecordInterface, \Iterator{
	use \DB\Persist;

	private $test_ID;
	private $Name;
	private $grösse;

// Persist functions
	static public function getPrimaryKey():string { return 'test_ID'; }
	static public function getTableName():string { return '`test`'; }
	static public function getFields():array {
		return [
			'Name' => ['string', 255 ],
			'grösse' => ['integer', 5 ],
		];
	}
}