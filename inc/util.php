<?php declare(strict_types=1);

if(!defined('PATH_ROOT')) {
  define('PATH_ROOT', __DIR__ . '/../');
}
if( !isset($log_file) ) {
  $log_file = PATH_ROOT.'log/project_%s.log';
}
$log_file_handle = null;

if( !$settings = parse_ini_file(PATH_ROOT.'config/settings.ini', true) ) {
  _log('Could not load settings.ini');
  exit(0);
}
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
    $log_file_handle = fopen( sprintf( $log_file, gmdate("Ymd") ), 'a' );
  }
	fprintf( $log_file_handle, "%s;%s;%s\r" , gmdate("Y-m-d H:i:s"), $level, $mess );
}




/**
 * Autoloader
 * namespaced classes are loaded from the classes folder
 */
spl_autoload_register( function( string $class_name ) {
	$class_name = str_replace('\\', '/', $class_name);
	$filename = $_SERVER["DOCUMENT_ROOT"] . sprintf( '/classes/%s.php', $class_name );
  if( file_exists($filename) ) {
    require_once $filename;
    return;
  } else {
    throw new Exception( sprintf( 'Could not load %s', $filename ) );
  }

});


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