<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * student – Persistant DB object
 * int       $ID;
 * string    $Name;
 * string    $Firstname;
 * string    $Fullname;
 */
final class student
	extends \Persist\Base
	implements \Persist\IPersist
{
	use \DB\DBPersistTrait;

	protected ?int       $ID;
	protected ?string    $Name;
	protected ?string    $Firstname;
	protected ?string    $Fullname;

	// Persist functions
	static public function getPrimaryKey():string { return 'ID'; }
	static public function getTableName():string { return '`student`'; }
	static public function getFields():array {
		return [
			'ID'                 => ['int', 10 ],
			'Name'               => ['string', 255 ],
			'Firstname'          => ['string', 255 ],
			'Fullname'           => ['string', 250 ],
		];
	}
}