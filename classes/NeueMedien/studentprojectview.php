<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * studentprojectview – Persistant DB object
 * int       $ID;
 * int       $ProjectID;
 * string    $Name;
 * string    $ProjectNr;
 * int       $StudentID;
 * string    $Fullname;
 * string    $Role;
 * \DateTime $Start;
 * \DateTime $End;
 */
final class studentprojectview
	extends \Persist\Base
	implements \Persist\IPersist
{
	use \DB\DBPersistTrait;

	protected ?int       $ID;
	protected ?int       $ProjectID;
	protected ?string    $Name;
	protected ?string    $ProjectNr;
	protected ?int       $StudentID;
	protected ?string    $Fullname;
	protected ?string    $Role;
	protected ?\DateTime $Start;
	protected ?\DateTime $End;

	// Persist functions
	static public function getPrimaryKey():string { return 'ID'; }
	static public function getTableName():string { return '`studentprojectview`'; }
	static public function getFields():array {
		return [
			'ID'                 => ['int', 10 ],
			'ProjectID'          => ['int', 10 ],
			'Name'               => ['string', 255 ],
			'ProjectNr'          => ['string', 10 ],
			'StudentID'          => ['int', 10 ],
			'Fullname'           => ['string', 250 ],
			'Role'               => ['string', 31 ],
			'Start'              => ['Date', 0 ],
			'End'                => ['Date', 0 ],
		];
	}
}