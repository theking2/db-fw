<?php declare(strict_types=1);

namespace NeueMedien;

/*
 * test – Persistant object
 */
class test implements \DB\DBRecordInterface, \Iterator{
	use \DB\Persist;

	private int       $test_ID;
	private string    $Name;
	private float     $grösse;

// Persist functions
	static public function getPrimaryKey():string { return 'test_ID'; }
	static public function getTableName():string { return '`test`'; }
	static public function getFields():array {
		return [
			'test_ID' => ['int', 10 ],
			'Name' => ['string', 255 ],
			'grösse' => ['float', 0 ],
		];
	}
}