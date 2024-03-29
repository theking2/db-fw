<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * teacher – Persistant DB object
 * string    $Abbr;
 * string    $Name;
 * string    $Firstname;
 * string    $FullName;
 */
final class teacher
	extends \Persist\Base
	implements \Persist\IPersist
{
	use \DB\DBPersistTrait;

	protected ?string    $Abbr;
	protected ?string    $Name;
	protected ?string    $Firstname;
	protected ?string    $FullName;

	// Persist functions
	static public function getPrimaryKey():string { return 'Abbr'; }
	static public function getTableName():string { return '`teacher`'; }
	static public function getFields():array {
		return [
			'Abbr'               => ['string', 3 ],
			'Name'               => ['string', 255 ],
			'Firstname'          => ['string', 255 ],
			'FullName'           => ['string', 255 ],
		];
	}
}