<?php declare(strict_types=1);
namespace NeueMedien;

/*
 * projectview – Persistant DB object
 */
final class projectview implements \Persist\PersistInterface, \Iterator
{
	use \DB\PersistTrait,\Persist\PersistIteratorTrait;

	private ?int       $ProjectID;
	private ?string    $ProjectNr;
	private ?string    $ProjectName;
	private ?string    $ProjectType;
	private ?int       $ProjectStatus;
	private ?string    $Coach;

// Persist functions
	static public function getPrimaryKey():string { return 'ProjectID'; }
	static public function getTableName():string { return '`projectview`'; }
	static public function getFields():array {
		return [
			'ProjectID'          => ['int', 10 ],
			'ProjectNr'          => ['string', 10 ],
			'ProjectName'        => ['string', 255 ],
			'ProjectType'        => ['string', 255 ],
			'ProjectStatus'      => ['int', 3 ],
			'Coach'              => ['string', 255 ],
		];
	}
}