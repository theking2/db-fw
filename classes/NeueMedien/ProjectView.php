<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * projectview – Persistant DB object
 * int       $ProjectID;
 * string    $ProjectNr;
 * string    $ProjectName;
 * string    $ProjectType;
 * int       $ProjectStatus;
 * string    $Coach;
 */
final class projectview
	extends \Persist\Base
	implements \Persist\IPersist, \Iterator
{
	use \Persist\IteratorTrait, \DB\DBPersistTrait;

	protected ?int       $ProjectID;
	protected ?string    $ProjectNr;
	protected ?string    $ProjectName;
	protected ?string    $ProjectType;
	protected ?int       $ProjectStatus;
	protected ?string    $Coach;

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