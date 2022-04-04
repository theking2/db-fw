<?php declare(strict_types=1);
namespace NeueMedien;
/**
 * TestObject – a test object using Persist
 */
class TestObject implements \Iterator
{
  use \DB\Persist;

  public static function getTableName(): string { return '`test`'; }
  public static function getPrimaryKeyName():string { return 'id'; }
  public static function getFields()
  {
    return
    ['id'=> 'int'
    ,'name'=> 'string'
    ,'date'=> 'DateTime'
    ,'unsigned'=> 'unsigned'
    ];
  }

  private int $id;
  private string $name;
  private \DateTime $date;
  private int $unsigned;
  
  function __construct(?int $id = null) {
    if( !is_null($id) ) {
      $this-> thaw( $id );
    }
  }
}