<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * country – Persistant DB object
 * string    $ISO;
 * string    $German;
 */
final class country
	extends \Persist\Base
	implements \Persist\IPersist
{
	use \DB\DBPersistTrait;

	protected ?string    $ISO;
	protected ?string    $German;

	// Persist functions
	static public function getPrimaryKey():string { return 'ISO'; }
	static public function getTableName():string { return '`country`'; }
	static public function getFields():array {
		return [
			'ISO'                => ['string', 3 ],
			'German'             => ['string', 255 ],
		];
	}
}