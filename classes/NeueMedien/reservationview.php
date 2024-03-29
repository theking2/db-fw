<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * reservationview – Persistant DB object
 * int       $ID;
 * int       $EquipmentID;
 * string    $Equipment;
 * string    $Number;
 * int       $StudentID;
 * string    $Fullname;
 * \DateTime $Start;
 * \DateTime $End;
 */
final class reservationview
	extends \Persist\Base
	implements \Persist\IPersist
{
	use \DB\DBPersistTrait;

	protected ?int       $ID;
	protected ?int       $EquipmentID;
	protected ?string    $Equipment;
	protected ?string    $Number;
	protected ?int       $StudentID;
	protected ?string    $Fullname;
	protected ?\DateTime $Start;
	protected ?\DateTime $End;

	// Persist functions
	static public function getPrimaryKey():string { return 'ID'; }
	static public function getTableName():string { return '`reservationview`'; }
	static public function getFields():array {
		return [
			'ID'                 => ['int', 10 ],
			'EquipmentID'        => ['int', 10 ],
			'Equipment'          => ['string', 45 ],
			'Number'             => ['string', 6 ],
			'StudentID'          => ['int', 10 ],
			'Fullname'           => ['string', 250 ],
			'Start'              => ['Date', 0 ],
			'End'                => ['Date', 0 ],
		];
	}
}