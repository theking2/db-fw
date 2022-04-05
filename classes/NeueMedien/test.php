<?php declare(strict_types=1);
namespace NeueMedien;

/*
 * test – Persistant DB object
 */
final class test implements \Persist\PersistInterface, \Iterator
{
	use \DB\PersistTrait,\Persist\PersistIteratorTrait;

	private int       $test_ID;
	private string    $Name;
	private float     $groesse;

// Persist functions
	static public function getPrimaryKey():string { return 'test_ID'; }
	static public function getTableName():string { return '`test`'; }
	static public function getFields():array {
		return [
			'test_ID' => ['int', 10 ],
			'Name' => ['string', 255 ],
			'groesse' => ['float', 0 ],
		];
	}
}