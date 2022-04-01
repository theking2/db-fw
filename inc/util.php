<?php
if( !isset($log_file) ) {
  $log_file ='log\project_%s.log';
}
$log_file_handle = null;

/**
 * _log function for writing results or error messages
 * The function starts a new file every day and the name contains the current date.
 * Each line will contain date, level and message text
 *
 * @param string $mess The message to write
 * @param int $level the level that is reported
 * @return void
 */
function _log( $mess, $level = 'i' ) {
  global $log_file_handle;
  global $log_file;
  if( !$log_file_handle ) {
    $log_file_handle = fopen( str_replace( '\\', 'DIRECTORY_SEPARATOR', sprintf( $log_file, gmdate("Ymd") ) ), 'a' );
  }
	fprintf( $log_file_handle, "%s;%s;%s\r" , gmdate("Y-m-d H:i:s"), $level, $mess );
}

spl_autoload_register( 'load_class' );
/**
 * Class autoloader
 * namespaces are folders
 *
 * @param string $className name of the classe
 * @return void
 */
function load_class( $className )
{
	$fileName = __DIR__.'/../classes/' . str_replace('\\', DIRECTORY_SEPARATOR,$className) . '.php';
	require_once $fileName;
}


/**
 * Wrap content in a html tag
 * @param string $tag html tag to be wrapped around the content
 * @param string $text text to output
 * @param string $class class or classes to add, null is ignore
 * @param string $id id to add, null is ignore
 * @param Array $data if a data tag is required use value=val to insert a data-value=val string, null is ignore
 * Can be accessed with element.dataset.value"></tag>
 * @return string complete html string.
 */
function wrap_tag( $tag, $text, $class=null, $id=null, $data=null ) {
  return '<' . $tag

  // add a class string if we have a class
   . ($id?' id="'.$id.'"':'')

  // add an id string if we have an id
   . ($class?" class=\"" . $class . '"': '')

  // add a data field
   . ($data? getData($data): '')

  // end opening tag, add content and close
   . '>' . $text . '</'. $tag . '>' . PHP_EOL;
}
function getData($data) {
  $result = [];
  foreach($data as $key=>$value) {
    $result[] = "data-$key=$value";
  }
  return implode( ' ', $result );
}