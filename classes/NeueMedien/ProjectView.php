<?php declare(strict_types=1);

namespace NeueMedien;

/*
 * projectview – Persistant object
 */
class projectview implements \DB\DBRecordInterface, \Iterator{
	use \DB\Persist;

	private $ProjectID;
	private $ProjectNr;
	private $ProjectName;
	private $ProjectDescription;
	private $ProjectTypeName;
	private $ProjectStatus;
	private $Coach;

// Persist functions
	static public function getPrimaryKey():string { return 'ProjectID'; }
	static public function getTableName():string { return '`projectview`'; }
	static public function getFields():array {
		return [
			'ProjectID' => ['integer', 10 ],
			'ProjectNr' => ['string', 10 ],
			'ProjectName' => ['string', 255 ],
			'ProjectDescription' => ['string', 8192 ],
			'ProjectTypeName' => ['string', 255 ],
			'ProjectStatus' => ['integer', 3 ],
			'Coach' => ['string', 255 ],
		];
	}
}