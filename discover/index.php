<?php
declare(strict_types=1);

require_once '../inc/util.php';

$type_list = [
  'integer'=> [ 'int', 'integer', 'smallint', 'tinyint', 'bigint' ],
  'double'=> [ 'float', 'double', 'real' ],
  'string'=> [ 'char', 'varchar', 'text' ],
  'boolean'=> [ 'bool', 'boolean' ],
  'DateTime'=> [ 'date', 'datetime', 'time' ]
];

$db = \DB\Database::getConnection();

$sql ="show tables";
$table_stat = $db-> prepare($sql);
$table_stat-> execute();

$table_stat-> bindColumn(1, $table_name);

while( $table_stat->fetch() ) {
  echo wrap_tag('h1',$table_name);

  $sql = "show columns from $table_name";
  $cols_stat = $db-> prepare( $sql );
  $cols_stat-> execute();

  $cols_stat-> bindColumn( 1, $fieldName );
  $cols_stat-> bindColumn( 2, $fieldType );
  $cols_stat-> bindColumn( 4, $fieldKey );

  $cols = [];

  $type_pattern = '/(.*)\((.*)\)/';
  while( $cols_stat->fetch() ) {
    if( $fieldKey === 'PRI' ) {
      $keyname = $fieldName;
    }
    elseif( in_array( $fieldType, $type_list[ 'DateTime' ] ) ) {
      $cols[$fieldName] = [ 'DateTime', null ];
    }  
    else {
      preg_match( $type_pattern, $fieldType, $desc );
      if( count( $desc) !== 3 ) 
        continue;
      foreach( $type_list as $php_type => $db_types ) {
        if( in_array( $desc[1], $db_types ) ) {
          $cols[$fieldName] = [ $php_type, $desc[2] ];
          break;
        }
      }
    }
  }
  echo wrap_tag('p', "key: $keyname" );
  echo '<ul>';
  foreach( $cols as $fieldName=> $fieldDescription ) {
    echo wrap_tag('li', $fieldName );
  }
  echo '</ul>';
  $fh = fopen("./src/$table_name.php", 'w');
  //$cols = "'" . implode( "',\n\t\t\t'", $cols ) . "'";
  fwrite( $fh,"<?php
namespace NeueMedien;
  
class $table_name extends \DB\DBRecord implements \DB\DBRecordInterface {
  static public function getPrimaryKeyName() { return '$keyname'; }
  static public function getTableName() { return '$table_name'; }
  static public function getFieldNames()
  {
    return 
    [
");
  foreach( $cols as $fieldName=> $fieldDescription ) {
    fprintf( $fh, "'%s' => ['%s', %d ],\n", $fieldName, $fieldDescription[0], $fieldDescription[1] );
  };
  fwrite( $fh, "];
  }
}" );
  fclose($fh);
}