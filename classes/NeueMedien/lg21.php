<?php declare(strict_types=1);
namespace NeueMedien;

/**
 * lg21 – Persistant DB object
 * string    $﻿Name;
 * string    $FirstName;
 */
final class lg21
	extends \Persist\Base
	implements \Persist\IPersist
{
	use \DB\DBPersistTrait;

	protected ?string    $﻿Name;
	protected ?string    $FirstName;

	// Persist functions
	static public function getPrimaryKey():string { return 'ID'; }
	static public function getTableName():string { return '`lg21`'; }
	static public function getFields():array {
		return [
			'﻿Name'            => ['string', 0 ],
			'FirstName'          => ['string', 0 ],
		];
	}
}