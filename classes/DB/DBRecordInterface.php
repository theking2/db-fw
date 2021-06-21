<?php

namespace DB;

interface DBRecordInterface
{
  public function getID();
  public function getFields();
  public static function getTableName();
}
