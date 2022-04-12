<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * studentprojectview – Persistant DB object
 * int       $ID;
 * string    $Name;
 * string    $ProjectNr;
 * string    $Fullname;
 * string    $Role;
 * \DateTime $Start;
 * \DateTime $End;
 */
final class studentprojectview
	extends \Persist\Base
	implements \Persist\IPersist, \Iterator
{
	use \Persist\IteratorTrait, \DB\DBPersistTrait;

	protected ?int       $ID;
	protected ?string    $Name;
	protected ?string    $ProjectNr;
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
			'Name'               => ['string', 255 ],
			'ProjectNr'          => ['string', 10 ],
			'Fullname'           => ['string', 250 ],
			'Role'               => ['string', 31 ],
			'Start'              => ['Date', 0 ],
			'End'                => ['Date', 0 ],
		];
	}
}