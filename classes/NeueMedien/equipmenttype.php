<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * equipmenttype – Persistant DB object
 * int       $ID;
 * string    $Name;
 */
final class equipmenttype
	extends \Persist\Base
	implements \Persist\IPersist
{
	use \DB\DBPersistTrait;

	protected ?int       $ID;
	protected ?string    $Name;

	// Persist functions
	static public function getPrimaryKey():string { return 'ID'; }
	static public function getTableName():string { return '`equipmenttype`'; }
	static public function getFields():array {
		return [
			'ID'                 => ['int', 10 ],
			'Name'               => ['string', 50 ],
		];
	}
}