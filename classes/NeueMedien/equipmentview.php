<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * equipmentview – Persistant DB object
 * int       $ID;
 * string    $Name;
 * string    $Number;
 * string    $Description;
 * string    $Type;
 */
final class equipmentview
	extends \Persist\Base
	implements \Persist\IPersist, \Iterator
{
	use \Persist\IteratorTrait, \DB\DBPersistTrait;

	protected ?int       $ID;
	protected ?string    $Name;
	protected ?string    $Number;
	protected ?string    $Description;
	protected ?string    $Type;

	// Persist functions
	static public function getPrimaryKey():string { return 'ID'; }
	static public function getTableName():string { return '`equipmentview`'; }
	static public function getFields():array {
		return [
			'ID'                 => ['int', 10 ],
			'Name'               => ['string', 45 ],
			'Number'             => ['string', 6 ],
			'Description'        => ['string', 0 ],
			'Type'               => ['string', 50 ],
		];
	}
}