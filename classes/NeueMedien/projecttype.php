<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * projecttype – Persistant DB object
 * int       $ID;
 * string    $Name;
 */
final class projecttype
	extends \Persist\Base
	implements \Persist\IPersist
{
	use \DB\DBPersistTrait;

	protected ?int       $ID;
	protected ?string    $Name;

	// Persist functions
	static public function getPrimaryKey():string { return 'ID'; }
	static public function getTableName():string { return '`projecttype`'; }
	static public function getFields():array {
		return [
			'ID'                 => ['int', 10 ],
			'Name'               => ['string', 255 ],
		];
	}
}