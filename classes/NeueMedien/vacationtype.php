<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * vacationtype – Persistant DB object
 * int       $ID;
 * string    $Name;
 */
final class vacationtype
	extends \Persist\Base
	implements \Persist\IPersist, \Iterator
{
	use \Persist\IteratorTrait, \DB\DBPersistTrait;

	protected ?int       $ID;
	protected ?string    $Name;

	// Persist functions
	static public function getPrimaryKey():string { return 'ID'; }
	static public function getTableName():string { return '`vacationtype`'; }
	static public function getFields():array {
		return [
			'ID'                 => ['int', 10 ],
			'Name'               => ['string', 255 ],
		];
	}
}