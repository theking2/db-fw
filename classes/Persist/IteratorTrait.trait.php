<?php declare(strict_types=1);

namespace Persist;

/**
 * IteratorTrait
 * 
 * This trait implements the Iterator interface for the class that uses it.
 * @obsolote user the generator findall() instead
 * @package Persist
 */
trait IteratorTrait {
	public function current ( ): object { return $this; }
	#[\ReturnTypeWillChange]
	public function key ( )	{ return $this-> {$this->getPrimaryKey()} ; }
	public function valid ( ): bool { return $this-> _valid; }
	public function next ( ): void { $this-> findNext(); }
	public function rewind ( ): void { $this-> findFirst(); }
  /* #endregion */
};