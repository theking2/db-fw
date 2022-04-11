<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * student – Persistant DB object
 */
final class student
	extends \Persist\Base
	implements \Persist\IPersist, \Iterator
{
	use \Persist\IteratorTrait, \DB\DBPersistTrait;

	protected ?int       $ID;
	protected ?string    $Name;
	protected ?string    $Firstname;

	// Persist functions
	static public function getPrimaryKey():string { return 'ID'; }
	static public function getTableName():string { return '`student`'; }
	static public function getFields():array {
		return [
			'ID'                 => ['int', 10 ],
			'Name'               => ['string', 255 ],
			'Firstname'          => ['string', 255 ],
		];
	}
}