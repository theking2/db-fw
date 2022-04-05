<?php declare(strict_types=1);

namespace Persist;

trait JSONTrait
{  
  /**
   * getJSON Get a json string from a database object
   *
   * @return void
   */
  public function getJSON() 
  {
    return json_encode($this->getArrayCopy(), JSON_FORCE_OBJECT);
  }
  /**
   * getArrayCopy - Returns an array copy of the object
   *
   * @return array
   */
  public function getArrayCopy(): array
  {
    $array = [];
    foreach( array_keys( $this->getFields() ) as $field ) {
      $array[$field] = (string)$this-> $field;
    }
    return $array;
  }
}