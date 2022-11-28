<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * project – Persistant DB object
 * int       $ID;
 * int       $ParentID;
 * string    $Number;
 * string    $Name;
 * string    $Description;
 * int       $TypeID;
 * int       $CustomerID;
 * string    $Coach;
 * int       $Status;
 */
final class project
	extends \Persist\Base
	implements \Persist\IPersist
{
	use \DB\DBPersistTrait;

	protected ?int       $ID;
	protected ?int       $ParentID;
	protected ?string    $Number;
	protected ?string    $Name;
	protected ?string    $Description;
	protected ?int       $TypeID;
	protected ?int       $CustomerID;
	protected ?string    $Coach;
	protected ?int       $Status;

	// Persist functions
	static public function getPrimaryKey():string { return 'ID'; }
	static public function getTableName():string { return '`project`'; }
	static public function getFields():array {
		return [
			'ID'                 => ['int', 10 ],
			'ParentID'           => ['int', 10 ],
			'Number'             => ['string', 10 ],
			'Name'               => ['string', 255 ],
			'Description'        => ['string', 8192 ],
			'TypeID'             => ['int', 10 ],
			'CustomerID'         => ['int', 10 ],
			'Coach'              => ['string', 3 ],
			'Status'             => ['int', 3 ],
		];
	}
}