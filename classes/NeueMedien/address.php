<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * address – Persistant DB object
 * int       $ID;
 * string    $StreetNr;
 * string    $ZIP;
 * string    $City;
 * string    $CountryID;
 */
final class address
	extends \Persist\Base
	implements \Persist\IPersist, \Iterator
{
	use \Persist\IteratorTrait, \DB\DBPersistTrait;

	protected ?int       $ID;
	protected ?string    $StreetNr;
	protected ?string    $ZIP;
	protected ?string    $City;
	protected ?string    $CountryID;

	// Persist functions
	static public function getPrimaryKey():string { return 'ID'; }
	static public function getTableName():string { return '`address`'; }
	static public function getFields():array {
		return [
			'ID'                 => ['int', 10 ],
			'StreetNr'           => ['string', 255 ],
			'ZIP'                => ['string', 32 ],
			'City'               => ['string', 255 ],
			'CountryID'          => ['string', 3 ],
		];
	}
}