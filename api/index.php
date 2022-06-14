<?php declare(strict_types=1);
namespace NeueMedien;
require '../inc/util.php';
$allowed = ['test', 'project', 'projectview', 'address', 'country'
, 'projectrole', 'projecttype', 'student', 'studentrole', 'studentroleproject', 'studentprojectview',
, 'teacher', 'timesheet', 'timesheetview', 'user'
, 'equipment', 'equipmentview', 'reservationview', 'equipment_reservation'];	

$requestMethod = $_SERVER["REQUEST_METHOD"];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$param = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
$uri = explode( '/', $uri );

if( !isEntityValid($uri[1]) ) {
  sendResponse(notFoundResponse());
  exit();
}

if( $param ) {
  $uri[2] = parseParameters($param);
}

// prepend the namespace
$uri[1] = __NAMESPACE__ . '\\' . $uri[1];

switch($requestMethod) {
  case 'GET':     $response = doGet($uri);     break;
  case 'POST':    $response = doCreate($uri);  break;
  case 'PUT':     $response = doUpdate($uri);  break;
  case 'DELETE':  $response = doDelete($uri);  break;
  case 'OPTIONS': $response = okResponse();   break;
  default:        $response = notFoundResponse(); break;
}

sendResponse($response);

/**
 * CHeck if a the request contains a valid entity name
 *
 * @param  array $uri
 * @return void
 */
function isEntityValid( ?string $entity ) {
  global $allowed;
  return $entity and in_array( $entity, $allowed );
}

/**
 * If a parameter isther check if numeric
 *
 * @param  array $uri
 * @return void
 */
function parseParameters( ?string $param ) {
  global $uri;
  if( !isset($uri[2]) )  {
    $param = explode('&', $param);
    $result = [];
    foreach( $param as $param ) {
      $param = explode('=', $param);
      $result[$param[0]] = '*'. str_replace('*','%',$param[1]); // use the like operator
    }
    return $result;
  }
  else {
    return $uri[2];
  }
}

  
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
 * Handle a GET request, if {id} is provided attempt to retrieve one, otherwise all.
 *
 * @param  mixed $uri
 * @return array
 */
function doGet(array $uri): array
{
  if( isset($uri[2]) and !is_array($uri[2]) ) {
    if( $obj = new $uri[1]($uri[2]) and $obj-> isRecord() ) {
      $response['status_code_header'] = 'HTTP/1.1 200 OK';
      $response['body'] = json_encode($obj-> getArrayCopy() );
      return $response;
    } else {
      return notFoundResponse();
    }
  }



  // no key provided, return all
  // paging would be nice here

  $result = [];

  if( isset($uri[2]) and is_array($uri[2]) ) {
    $where = [];
    foreach( $uri[2] as $key => $value ) {
      $where[$key] = urldecode( $value );
    }
    $obj = new $uri[1]();
    $obj-> setWhere($where);
    foreach($obj as $o) {
      $result[] = $o-> getArrayCopy();
    }

  } else {
    foreach(new $uri[1] as $id=>$obj)
      $result[] = $obj-> getArrayCopy();
  }

  if( count($result)===0 ) {
    return notFoundResponse();
  }
  $response['status_code_header'] = 'HTTP/1.1 200 OK';
  $response['body'] = json_encode($result);

  return $response;
}

/**
 * Handle a POST request create a record
 *
 * @param  mixed $uri
 * @return array
 */
function doCreate(array $uri): array
{
  $response = [];
  $input = json_decode(file_get_contents('php://input'), true);
  $obj = $uri[1]::createFromArray($input);
  if( $obj-> freeze() ) {
    $response['status_code_header'] = 'HTTP/1.1 201 Created';
    $response['body'] = json_encode( [ 'id'=> $obj-> getKeyValue(), 'result'=> 'created' ] );
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
function doUpdate(array $uri): array
{
  $response = [];

  if( !isset($uri[2]) ) {
    return unprocessableEntityResponse();
  }

  $obj = new $uri[1]($uri[2]);
  if( $obj-> isRecord()) {
    $input = json_decode(file_get_contents('php://input'), true);
    $obj-> setFromArray( $input );

    if( $result = $obj-> freeze() ) {
      $response['status_code_header'] = 'HTTP/1.1 200 Updated';
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
  $obj = new $uri[1]($uri[2]);
  if( !$obj-> isRecord() ) {
    return notFoundResponse();
  }
  $response['status_code_header'] = 'HTTP/1.1 200 DELETED';
  $response['body'] = json_encode( [ 'id'=> (int)$uri[2], 'result'=> $obj->delete() ] );
  return $response;

  return notFoundResponse();
}


/**
 * create 200 Response
 *
 * @return array
 */
function okResponse(): array
{

  $response['status_code_header'] = 'HTTP/1.1 200 OK';
  $response['body'] = null;

  return $response;
}
/**
 * Create a 404 response
 *
 * @return array
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
 * @return array
 */
function unprocessableEntityResponse(): array
{
  $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
  $response['body'] = json_encode([ 'error' => 'Invalid input' ]);
  
  return $response;
}
