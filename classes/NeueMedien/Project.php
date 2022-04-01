<?php declare(strict_types=1);

namespace NeueMedien;

/*
 * project – Persistant object
 */
class project implements \DB\DBRecordInterface, \Iterator{
	use \DB\Persist;

	private $ID;
	private $ParentID;
	private $Number;
	private $Name;
	private $Description;
	private $TypeID;
	private $CustomerID;
	private $Coach;
	private $Status;

// Persist functions
	static public function getPrimaryKey():string { return 'ID'; }
	static public function getTableName():string { return '`project`'; }
	static public function getFields():array {
		return [
			'ParentID' => ['integer', 10 ],
			'Number' => ['string', 10 ],
			'Name' => ['string', 255 ],
			'Description' => ['string', 8192 ],
			'TypeID' => ['integer', 10 ],
			'CustomerID' => ['integer', 10 ],
			'Coach' => ['string', 3 ],
			'Status' => ['integer', 3 ],
		];
	}
}