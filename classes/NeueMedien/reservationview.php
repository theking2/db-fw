<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * reservationview – Persistant DB object
 * int       $ID
 * string    $Name;
 * string    $Number;
 * string    $Fullname;
 * \DateTime $Start;
 * \DateTime $End;
 */
final class reservationview
	extends \Persist\Base
	implements \Persist\IPersist, \Iterator
{
	use \Persist\IteratorTrait, \DB\DBPersistTrait;

	protected ?int       $ID;
	protected ?string    $Name;
	protected ?string    $Number;
	protected ?string    $Fullname;
	protected ?\DateTime $Start;
	protected ?\DateTime $End;

	// Persist functions
	static public function getPrimaryKey():string { return 'ID'; }
	static public function getTableName():string { return '`reservationview`'; }
	static public function getFields():array {
		return [
			'ID'                 => ['int', 10 ],
			'Name'               => ['string', 45 ],
			'Number'             => ['string', 6 ],
			'Fullname'           => ['string', 250 ],
			'Start'              => ['\DateTime', 0 ],
			'End'                => ['\DateTime', 0 ],
		];
	}
}