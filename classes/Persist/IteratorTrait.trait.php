<?php declare(strict_types=1);

namespace Persist;


trait IteratorTrait {
	public function current ( ): object { return $this; }
	#[\ReturnTypeWillChange]
	public function key ( )	{ return $this-> {$this->getPrimaryKey()} ; }
	public function valid ( ): bool { return $this-> _valid; }
	public function next ( ): void { $this-> findNext(); }
	public function rewind ( ): void { $this-> findFirst(); }
  /* #endregion */
};