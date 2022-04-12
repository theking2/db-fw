<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * studentroleproject – Persistant DB object
 */
final class studentroleproject
	extends \Persist\Base
	implements \Persist\IPersist, \Iterator
{
	use \Persist\IteratorTrait, \DB\DBPersistTrait;

	protected ?int       $ID;
	protected ?int       $StudentID;
	protected ?int       $ProjectID;
	protected ?\DateTime $Start;
	protected ?string    $ProjectRoleID;
	protected ?\DateTime $End;

	// Persist functions
	static public function getPrimaryKey():string { return 'ID'; }
	static public function getTableName():string { return '`studentroleproject`'; }
	static public function getFields():array {
		return [
			'ID'                 => ['int', 10 ],
			'StudentID'          => ['int', 10 ],
			'ProjectID'          => ['int', 10 ],
			'Start'              => ['Date', 0 ],
			'ProjectRoleID'      => ['string', 3 ],
			'End'                => ['Date', 0 ],
		];
	}
}