<?php declare(strict_types=1);

namespace Persist;

interface PersistInterface
{
  public static function getPrimaryKey(): string;
  public static function getFields(): array;
  public static function getTableName(): string;
}
