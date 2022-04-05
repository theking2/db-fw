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
if( isset($uri[2]) and !is_numeric($uri[2]) ) {
  header("HTTP/1.1 404 Not Found");
  die( "<h1>Not Found</h1><p>The requested URL was not found on this server.</p><hr>" );
}
$class = "\\NeueMedien\\$uri[1]";
$uri[1] = new $class;

switch($requestMethod) {
  case 'GET':    $response = doGet($uri);               break;
  case 'POST':   $response = doPost($uri);              break;
  case 'PUT':    $response = doPut($uri);               break;
  case 'DELETE': $response = doDelete($uri);            break;
  default:       $response = $this->notFoundResponse(); break;
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
    if( $obj = $uri[1]->thaw((int)$uri[2]) ) {
      $response['status_code_header'] = 'HTTP/1.1 200 OK';
      $response['body'] = $obj-> getJSON();
    } else {
      return notFoundResponse();
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

function doPost($uri): array
{
  $response = [];
  $input = json_decode(file_get_contents('php://input'), true);
  $uri[1]-> setFromArray($input);
  if( $id = $uri[1]-> freeze() ) {
    $response['status_code_header'] = 'HTTP/1.1 201 Created';
    $response['body'] = $id;
  } else {
    $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
  }
  return $response;
}

function doPut($uri): array
{
  $response = [];

  if($uri[1]->thaw((int)$uri[2]) ) {
    $input = json_decode(file_get_contents('php://input'), true);
    $uri[1]-> setFromArray( $input );

    if( $id = $uri[1]-> freeze() ) {
      $response['status_code_header'] = 'HTTP/1.1 201 Created';
      $response['body'] = $id;

    } else {
      $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
    }
    return $response;
  }

  return notFoundResponse();
}
function doDelete($uri): array
{
  $response = [];
  if( !isset($uri[2]) ) {
    return notFoundResponse();
  }
  if( $obj = $uri[1]->thaw((int)$uri[2]) ) {
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = $obj->delete();
    return $response;
  }

  return notFoundResponse();
}

function notFoundResponse()
{
  $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
  $response['body'] = null;
  return $response;
}