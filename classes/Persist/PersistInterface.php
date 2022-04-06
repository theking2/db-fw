<?php declare(strict_types=1);

namespace Persist;

/**
 * Interface PersistInterface
 * @package Persist
 */

interface PersistInterface
{
  public static function getPrimaryKey(): string;
  public static function getFields(): array;
  public static function getTableName(): string;

  public function __get(string $field);
  public function __set(string $field, $value ): void;
  
  /**
   * Return the primary key of the object
   *
   * @return mixed
   */
  public function getKeyValue(): ?int;  
  /**
   * Get all fields of the object as an array
   *
   * @return array
   */
  public function getArrayCopy(): array;  
  /**
   * Create a new object from an array
   *
   * @param  mixed $data
   * @return PersistInterface
   */
  public static function createFromArray(array $data): PersistInterface;
  
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
   * @return PersistInterface
   */
  public static function createFromJson(string $json): PersistInterface;
}
