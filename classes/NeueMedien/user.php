<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * user – Persistant DB object
 * string    $username;
 * string    $email;
 * string    $password;
 */
final class user
	extends \Persist\Base
	implements \Persist\IPersist
{
	use \DB\DBPersistTrait;

	protected ?string    $username;
	protected ?string    $email;
	protected ?string    $password;

	// Persist functions
	static public function getPrimaryKey():string { return 'ID'; }
	static public function getTableName():string { return '`user`'; }
	static public function getFields():array {
		return [
			'username'           => ['string', 16 ],
			'email'              => ['string', 255 ],
			'password'           => ['string', 32 ],
		];
	}
}