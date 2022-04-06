<?php declare(strict_types=1);
namespace NeueMedien;
require '../inc/util.php';
$allowed = ['test', 'project', 'projectview','address', 'country', 'projectrole', 'projecttype', 'student', 'studentrole', 'teacher', 'user' ];

$requestMethod = $_SERVER["REQUEST_METHOD"];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
if( !isset($uri[1]) or !in_array($uri[1], $allowed) ) {
  sendResponse(notFoundResponse());
  exit();
}
if( isset($uri[2]) and !is_numeric($uri[2]) ) {
  sendResponse(unprocessableEntityResponse());
  exit();
}

// prepend the namespace
$uri[1] = __NAMESPACE__ . '\\' . $uri[1];

switch($requestMethod) {
  case 'GET':    $response = doGet($uri);               break;
  case 'POST':   $response = doPost($uri);              break;
  case 'PUT':    $response = doPut($uri);               break;
  case 'DELETE': $response = doDelete($uri);            break;
  default:       $response = $this->notFoundResponse(); break;
}

sendResponse($response);

/**
 * Send a prepared response
 *
 * @param  array $response - containing [0]=> respose code, [1]=> response body
 * @return void
 */
function sendResponse(array $response): void
{
  header($response['status_code_header']);
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

  if ($response['body']) {
    echo $response['body'];
  }
}

/**
 * Handle a GET request, if {id} is provide attempt to retrieve one, otherwis all.
 *
 * @param  mixed $uri
 * @return array
 */
function doGet(array $uri): array
{
  if( isset($uri[2]) ) {
    if( $obj = new $uri[1]((int)$uri[2]) ) {
      $response['status_code_header'] = 'HTTP/1.1 200 OK';
      $response['body'] = json_encode( $obj-> getArrayCopy() );
    } else {
      return notFoundResponse();
    }
  } else {
    $result = [];
    foreach(new $uri[1] as $id=>$obj)
      $result[] = $obj-> getArrayCopy();
  
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode($result);
  }

  return $response;
}

/**
 * Handle a POST request create a record
 *
 * @param  mixed $uri
 * @return array
 */
function doPost(array $uri): array
{
  $response = [];
  $input = json_decode(file_get_contents('php://input'), true);
  $obj = $uri[1]::createFromArray($input);
  if( $obj-> freeze() ) {
    $response['status_code_header'] = 'HTTP/1.1 201 Created';
    $response['body'] = json_encode( ['id'=> $obj-> getKeyValue() ] );
  } else {
    $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
  }
  return $response;
}

/**
 * Handle PUT request, update a record for {id}
 *
 * @param  mixed $uri
 * @return array
 */
function doPut(array $uri): array
{
  $response = [];

  if( !isset($uri[2]) ) {
    return unprocessableEntityResponse();
  }

  if($obj = new $uri[1]((int)$uri[2]) ) {
    $input = json_decode(file_get_contents('php://input'), true);
    $obj-> setFromArray( $input );

    if( $result = $obj-> freeze() ) {
      $response['status_code_header'] = 'HTTP/1.1 201 Updated';
      $response['body'] = json_encode( ['id'=> $obj-> getKeyValue(), 'result'=> $result ] );

    } else {
      $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
    }
    return $response;
  }
  return notFoundResponse();
}
/**
 * Handle DELETE request, delete a record for {id}
 *
 * @param  mixed $uri
 * @return array
 */
function doDelete(array $uri): array
{
  $response = [];
  if( !isset($uri[2]) ) {
    return notFoundResponse();
  }
  $obj = new $uri[1]((int)$uri[2]);
  if( !$obj-> isRecord() ) {
    return notFoundResponse();
  }
  $response['status_code_header'] = 'HTTP/1.1 202 DELETED';
  $response['body'] = json_encode( [ 'id'=> (int)$uri[2], 'result'=> $obj->delete() ] );
  return $response;

  return notFoundResponse();
}

/**
 * Create a 404 response
 *
 * @return void
 */
function notFoundResponse(): array
{
  $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
  $response['body'] = null;
  return $response;
}

/**
 * Create a 422 response
 *
 * @return void
 */
function unprocessableEntityResponse(): array
{
  $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
  $response['body'] = json_encode([ 'error' => 'Invalid input' ]);
  return $response;
}