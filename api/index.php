<?php declare(strict_types=1);

require '../inc/util.php';
$allowed = ['test', 'project', 'projectview','address', 'country', 'projectrole', 'projecttype', 'student', 'studentrole', 'teacher', 'user' ];

$requestMethod = $_SERVER["REQUEST_METHOD"];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
if( !isset($uri[1]) or !in_array($uri[1], $allowed) ) {
  header("HTTP/1.1 404 Not Found");
  die( "<h1>Not Found</h1><p>The requested URL was not found on this server.</p><hr>" );
}
if( !isset($uri[2]) and gettype($uri[2]!='int') ) {
  header("HTTP/1.1 404 Not Found");
  die( "<h1>Not Found</h1><p>The requested URL was not found on this server.</p><hr>" );
}
$class = "\\NeueMedien\\$uri[1]";
$uri[1] = new $class;

switch($requestMethod) {
  case 'GET':
    $response = doGet($uri);
    break;
  case 'POST':
    $response = doPost($uri);
    break;
  case 'PUT':
    $response = doPut($uri);
    break;
  case 'DELETE':
    $response = doDelete($uri);
    break;
  default: 
    $response = $this->notFoundResponse();
    break;


}
header($response['status_code_header']);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($response['body']) {
  echo $response['body'];
}

function doGet($uri): array
{
  if( isset($uri[2]) ) {
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    if( $obj = $uri[1]->thaw((int)$uri[2]) ) {
      $response['body'] = $obj-> getJSON();
    } else {
      $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
      $response['body'] = null;
      return $response;
    }
  } else {
    $result = [];
    foreach($uri[1] as $id=>$obj)
      $result[] = $obj-> getArrayCopy();
  
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode($result);
  }

  return $response;
}
function response( $status,$status_message,$body )
{
	header("HTTP/1.1 ".$status);
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

	$json_response = json_encode($response);

}