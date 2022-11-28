<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * timesheetview – Persistant DB object
 * int       $ID;
 * int       $StudentID;
 * string    $Fullname;
 * string    $ProjectName;
 * string    $Number;
 * \DateTime $Date;
 * int       $Minutes;
 */
final class timesheetview
	extends \Persist\Base
	implements \Persist\IPersist
{
	use \DB\DBPersistTrait;

	protected ?int       $ID;
	protected ?int       $StudentID;
	protected ?string    $Fullname;
	protected ?string    $ProjectName;
	protected ?string    $Number;
	protected ?\DateTime $Date;
	protected ?int       $Minutes;

	// Persist functions
	static public function getPrimaryKey():string { return 'ID'; }
	static public function getTableName():string { return '`timesheetview`'; }
	static public function getFields():array {
		return [
			'ID'                 => ['int', 10 ],
			'StudentID'          => ['int', 10 ],
			'Fullname'           => ['string', 250 ],
			'ProjectName'        => ['string', 255 ],
			'Number'             => ['string', 10 ],
			'Date'               => ['Date', 0 ],
			'Minutes'            => ['int', 3 ],
		];
	}
}