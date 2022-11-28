<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * taskview – Persistant DB object
 * int       $id;
 * int       $ProjectID;
 * string    $ProjectName;
 * int       $StudentID;
 * string    $StudentName;
 * string    $TaskName;
 * \DateTime $Start;
 * \DateTime $Due;
 * int       $done;
 */
final class taskview
	extends \Persist\Base
	implements \Persist\IPersist
{
	use \DB\DBPersistTrait;

	protected ?int       $id;
	protected ?int       $ProjectID;
	protected ?string    $ProjectName;
	protected ?int       $StudentID;
	protected ?string    $StudentName;
	protected ?string    $TaskName;
	protected ?\DateTime $Start;
	protected ?\DateTime $Due;
	protected ?int       $done;

	// Persist functions
	static public function getPrimaryKey():string { return 'ID'; }
	static public function getTableName():string { return '`taskview`'; }
	static public function getFields():array {
		return [
			'id'                 => ['int', 10 ],
			'ProjectID'          => ['int', 10 ],
			'ProjectName'        => ['string', 255 ],
			'StudentID'          => ['int', 10 ],
			'StudentName'        => ['string', 250 ],
			'TaskName'           => ['string', 255 ],
			'Start'              => ['Date', 0 ],
			'Due'                => ['Date', 0 ],
			'done'               => ['int', 1 ],
		];
	}
}