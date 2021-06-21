<?php
require_once '../inc/util.php';

$db = \DB\Database::getConnection();

$sql ="show tables";
$table_stat = $db-> prepare($sql);
$table_stat-> bindColumn(1, $table_name);
$table_stat-> execute();
while( $table_stat->fetch() ) {
  echo wrap_tag('h1',$table_name);

  $sql = "show columns from $table_name";
  $cols_stat = $db-> prepare($sql);
  $cols_stat-> bindColumn(1,$fieldName);
  $cols_stat-> bindColumn(4,$fieldKey);
  $cols_stat-> execute();

  $cols = [];
  while( $cols_stat->fetch( )) {
    if($fieldKey === 'PRI' ) {
      $keyname = $fieldName;
    } else {
      $cols[] = $fieldName;
    }
  }
  echo wrap_tag('p', "key: $keyname" );
  echo '<ul>';
  foreach( $cols as $col ) {
    echo wrap_tag('li', $col);
  }
  echo '</ul>';
  $fh = fopen("$table_name.php", 'w');
  $cols = "'" . implode("',\n\t\t\t'",$cols) . "'";
  fwrite($fh,"<?php
namespace NeueMedien;
  
class $table_name extends \DB\DBRecord implements \DB\DBRecordInterface {
  static public function getPrimaryKeyName() { return '$keyname'; }
  static public function getTableName() { return '$table_name'; }
  static public function getFieldNames()
  {
    return 
    [
      $cols
    ];
  }
}
" );
  fclose($fh);
}