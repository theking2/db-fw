<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * equipment_reservation – Persistant DB object
 * int       $ID;
 * int       $EquipmentID;
 * int       $StudentID;
 * \DateTime $Start;
 * \DateTime $End;
 */
final class equipment_reservation
	extends \Persist\Base
	implements \Persist\IPersist, \Iterator
{
	use \Persist\IteratorTrait, \DB\DBPersistTrait;

	protected ?int       $ID;
	protected ?int       $EquipmentID;
	protected ?int       $StudentID;
	protected ?\DateTime $Start;
	protected ?\DateTime $End;

	// Persist functions
	static public function getPrimaryKey():string { return 'ID'; }
	static public function getTableName():string { return '`equipment_reservation`'; }
	static public function getFields():array {
		return [
			'ID'                 => ['int', 10 ],
			'EquipmentID'        => ['int', 10 ],
			'StudentID'          => ['int', 10 ],
			'Start'              => ['\DateTime', 0 ],
			'End'                => ['\DateTime', 0 ],
		];
	}
}