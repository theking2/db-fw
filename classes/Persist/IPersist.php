<?php declare(strict_types=1);

namespace Persist;

/**
 * Interface \Persist\Interface
 * @package Persist
 */

interface IPersist
{
  public static function getPrimaryKey(): string;
  public static function getFields(): array;
  public static function getTableName(): string;

  public function __get(string $field);
  public function __set(string $field, $value ): void;
	/**
	 * create – create a new record in the database or update an existing one
	 * @return bool
	 */
	public function freeze( ):bool;
  /**
   * thaw – fetch a record from the database by key
	 * this assumes keys are singel and ints!!
   *
   * @param  int $id
   * @return object
   */
  public function thaw($id): ?IPersist;
  /**
   * Return the primary key of the object
   *
   * @return mixed
   */
  public function getKeyValue(): ?int;  
  /**
   * Get the object values as array
   *
   * @return array
   */
  public function getArrayCopy(): array;  
  /**
   * Create a new object from an array
   *
   * @param  mixed $data
   * @return \Persist\Base
   */
  public static function createFromArray(array $data): Base;
	/**
	 * setFromArray
	 *
	 * @param  mixed $array
	 * @return Base
	 */
	public function setFromArray(array $array): Base;
/**
   * Get the object as a JSON string
   *
   * @return string
   */
  public function getJson(): string;  
  /**
   * Create a new object from a JSON string
   *
   * @param  mixed $json
   * @return \Persist\Base
   */
  public static function createFromJson(string $json): Base;
}
