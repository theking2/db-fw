<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * timesheet – Persistant DB object
 */
final class timesheet
	extends \Persist\Base
	implements \Persist\IPersist, \Iterator
{
	use \Persist\IteratorTrait, \DB\DBPersistTrait;

	protected ?int       $ID;
	protected ?int       $ProjectID;
	protected ?int       $StudentID;
	protected ?\DateTime $Date;
	protected ?int       $Minutes;

	// Persist functions
	static public function getPrimaryKey():string { return 'ID'; }
	static public function getTableName():string { return '`timesheet`'; }
	static public function getFields():array {
		return [
			'ID'                 => ['int', 10 ],
			'ProjectID'          => ['int', 10 ],
			'StudentID'          => ['int', 10 ],
			'Date'               => ['Date', 0 ],
			'Minutes'            => ['int', 3 ],
		];
	}
}