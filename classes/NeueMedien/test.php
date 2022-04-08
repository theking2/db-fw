<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * test – Persistant DB object
 */
final class test
	extends \Persist\Base
	implements \Persist\IPersist, \Iterator
{
	use \Persist\IteratorTrait, \DB\DBPersistTrait;

	protected ?int       $test_ID;
	protected ?string    $Name;
	protected ?float     $groesse;
	protected ?\DateTime $Date;

	// Persist functions
	static public function getPrimaryKey():string { return 'test_ID'; }
	static public function getTableName():string { return '`test`'; }
	static public function getFields():array {
		return [
			'test_ID'            => ['int', 10 ],
			'Name'               => ['string', 255 ],
			'groesse'            => ['float', 0 ],
			'Date'               => ['Date', 0 ],
		];
	}
}