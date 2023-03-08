<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * vacation – Persistant DB object
 * int       $ID;
 * int       $VacationTypeID;
 * int       $StudentID;
 * \DateTime $FromDate;
 * \DateTime $ToDate;
 */
final class vacation
	extends \Persist\Base
	implements \Persist\IPersist
{
	use \DB\DBPersistTrait;

	protected ?int       $ID;
	protected ?int       $VacationTypeID;
	protected ?int       $StudentID;
	protected ?\DateTime $FromDate;
	protected ?\DateTime $ToDate;

	// Persist functions
	static public function getPrimaryKey():string { return 'ID'; }
	static public function getTableName():string { return '`vacation`'; }
	static public function getFields():array {
		return [
			'ID'                 => ['int', 10 ],
			'VacationTypeID'     => ['int', 10 ],
			'StudentID'          => ['int', 10 ],
			'FromDate'           => ['Date', 0 ],
			'ToDate'             => ['Date', 0 ],
		];
	}
}