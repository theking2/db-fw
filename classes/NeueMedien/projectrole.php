<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * projectrole – Persistant DB object
 * string    $ID;
 * string    $Name;
 */
final class projectrole
	extends \Persist\Base
	implements \Persist\IPersist
{
	use \DB\DBPersistTrait;

	protected ?string    $ID;
	protected ?string    $Name;

	// Persist functions
	static public function getPrimaryKey():string { return 'ID'; }
	static public function getTableName():string { return '`projectrole`'; }
	static public function getFields():array {
		return [
			'ID'                 => ['string', 3 ],
			'Name'               => ['string', 31 ],
		];
	}
}