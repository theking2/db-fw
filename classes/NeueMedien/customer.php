<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * customer – Persistant DB object
 * int       $ID;
 * string    $Name;
 * string    $Email;
 * int       $AddressID;
 * int       $BillingAddressID;
 */
final class customer
	extends \Persist\Base
	implements \Persist\IPersist, \Iterator
{
	use \Persist\IteratorTrait, \DB\DBPersistTrait;

	protected ?int       $ID;
	protected ?string    $Name;
	protected ?string    $Email;
	protected ?int       $AddressID;
	protected ?int       $BillingAddressID;

	// Persist functions
	static public function getPrimaryKey():string { return 'ID'; }
	static public function getTableName():string { return '`customer`'; }
	static public function getFields():array {
		return [
			'ID'                 => ['int', 10 ],
			'Name'               => ['string', 255 ],
			'Email'              => ['string', 255 ],
			'AddressID'          => ['int', 10 ],
			'BillingAddressID'   => ['int', 10 ],
		];
	}
}