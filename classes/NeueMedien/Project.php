<?php declare(strict_types=1);
namespace NeueMedien;

/*
 * project – Persistant DB object
 */
final class project implements \Persist\PersistInterface, \Iterator
{
	use \DB\PersistTrait,\Persist\PersistIteratorTrait;

	private ?int       $ID;
	private ?int       $ParentID;
	private ?string    $Number;
	private ?string    $Name;
	private ?string    $Description;
	private ?int       $TypeID;
	private ?int       $CustomerID;
	private ?string    $Coach;
	private ?int       $Status;

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