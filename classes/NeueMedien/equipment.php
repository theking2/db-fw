<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * equipment – Persistant DB object
 * int       $ID;
 * string    $Name;
 * string    $Number;
 * string    $Description;
 */
final class equipment
	extends \Persist\Base
	implements \Persist\IPersist, \Iterator
{
	use \Persist\IteratorTrait, \DB\DBPersistTrait;

	protected ?int       $ID;
	protected ?string    $Name;
	protected ?string    $Number;
	protected ?string    $Description;

	// Persist functions
	static public function getPrimaryKey():string { return 'ID'; }
	static public function getTableName():string { return '`equipment`'; }
	static public function getFields():array {
		return [
			'ID'                 => ['int', 10 ],
			'Name'               => ['string', 45 ],
			'Number'             => ['string', 6 ],
			'Description'        => ['string', 0 ],
		];
	}
}