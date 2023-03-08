<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * vacationview – Persistant DB object
 * int       $ID;
 * string    $VacationType;
 * int       $StudentID;
 * string    $Fullname;
 * \DateTime $FromDate;
 * \DateTime $toDate;
 */
final class vacationview
	extends \Persist\Base
	implements \Persist\IPersist
{
	use \DB\DBPersistTrait;

	protected ?int       $ID;
	protected ?string    $VacationType;
	protected ?int       $StudentID;
	protected ?string    $Fullname;
	protected ?\DateTime $FromDate;
	protected ?\DateTime $toDate;

	// Persist functions
	static public function getPrimaryKey():string { return 'ID'; }
	static public function getTableName():string { return '`vacationview`'; }
	static public function getFields():array {
		return [
			'ID'                 => ['int', 10 ],
			'VacationType'       => ['string', 255 ],
			'StudentID'          => ['int', 10 ],
			'Fullname'           => ['string', 250 ],
			'FromDate'           => ['Date', 0 ],
			'toDate'             => ['Date', 0 ],
		];
	}
}