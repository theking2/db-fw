<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * task – Persistant DB object
 * int       $ID;
 * int       $ProjectID;
 * int       $StudentID;
 * string    $Name;
 * \DateTime $Start;
 * \DateTime $Due;
 * int       $Done;
 */
final class task
	extends \Persist\Base
	implements \Persist\IPersist, \Iterator
{
	use \Persist\IteratorTrait, \DB\DBPersistTrait;

	protected ?int       $ID;
	protected ?int       $ProjectID;
	protected ?int       $StudentID;
	protected ?string    $Name;
	protected ?\DateTime $Start;
	protected ?\DateTime $Due;
	protected ?int       $Done;

	// Persist functions
	static public function getPrimaryKey():string { return 'ID'; }
	static public function getTableName():string { return '`task`'; }
	static public function getFields():array {
		return [
			'ID'                 => ['int', 10 ],
			'ProjectID'          => ['int', 10 ],
			'StudentID'          => ['int', 10 ],
			'Name'               => ['string', 255 ],
			'Start'              => ['Date', 0 ],
			'Due'                => ['Date', 0 ],
			'Done'               => ['int', 1 ],
		];
	}
}