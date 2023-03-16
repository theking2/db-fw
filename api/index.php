<?php

declare(strict_types=1);

namespace NeueMedien;

use HttpStatusCode;

require '../inc/util.php';
/**
 * api has the following endpoints:
 * Request methods allowed: GET, POST, PUT, DELETE, OPTIONS
 * 
 * GET: /api/index.php/<endpoint>[/<id>]
 * - returns a list of all entries in the database or a single object
 * 
 * GET: /api/index.php/<endpoint>?<query>
 * - query is a key=value pair, e.g. ?Name=foo or ?Name=foo&Age=42 or ?Name=foo*
 * - returns a list of all entries in the database or a single object
 *
 * POST: /api/index.php/<endpoint>[/<id>]
 * - creates a new entry in the database
 * 
 * PUT: /api/index.php/<endpoint>
 * - updates an existing entry in the database
 * 
 * DELETE: /api/index.php/<endpoint>[/<id>]
 * - deletes an entry from the database
 * 
 * Payload: JSON
 * Response: JSON array or JSON object or JSON object or error message
 */


/**
 * these entities are allowed, all others get a notFoundResponse
 */
$allowed = [
  'test', 'student', 'teacher', 'user', 'project', 'address', 'country', 'projectrole', 'projecttype', 'studentrole', 'projectview', 'studentroleproject', 'studentprojectview', 'timesheet', 'timesheetview', 'equipment', 'equipment_reservation', 'equipmentview', 'reservationview', 'task', 'taskview', 'vacation', 'vacationtype', 'vacationview'

];

$requestMethod = $_SERVER["REQUEST_METHOD"];
/**
 * get the endpoint from the request
 * e.g. /api/index.php/<endpoint>[/<id>] or /api/index.php/<endpoint>?<query>
 * $uri[0] is always empty, $uri[1] is the endpoint
 */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
/**
 * $uri[2] is the id, if it present
 */
$uri = explode('/', $path);
unset($uri[0]);

if (!isEntityValid($uri[1])) {
  sendResponse(['status_code' => \HttpStatusCode::NotFound, 'body' => null]);
  exit();
}

/**
 * set the query string if query is present
 */
if ($query) {
  $uri[2] = parseParameters($query);
}
unset($path, $query);

// prepend the namespace
$uri[1] = __NAMESPACE__ . '\\' . $uri[1];

switch ($requestMethod) {
  case 'GET':
    $response = doGet($uri);
    break;
  case 'POST':
    $response = doCreate($uri);
    break;
  case 'PUT':
    $response = doUpdate($uri);
    break;
  case 'DELETE':
    $response = doDelete($uri);
    break;
  case 'OPTIONS':
    $response = ['status_code' => \HttpStatusCode::OK, 'body' => null];
    break;
  default:
    $response = ['status_code' => \HttpStatusCode::NotFound, 'body' => null];
    break;
}

sendResponse($response);

/**
 * CHeck if a the request contains a valid entity name
 *
 * @param  array $uri
 * @return void
 */
function isEntityValid(?string $entity)
{
  global $allowed;
  return $entity and in_array($entity, $allowed);
}

/**
 * If a parameter isther check if numeric
 *
 * @param  array $uri
 * @return void
 */
function parseParameters(?string $param)
{
  global $uri;
  if (!isset($uri[2])) {
    $result = [];
    foreach (explode('&', $param) as $param) {
      $param = explode('=', $param);
      $result[$param[0]] = '*' . str_replace('*', '%', $param[1]); // use the like operator
    }
    return $result;
  } else {
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
  // header($response['status_code_header']);
  // header("Access-Control-Allow-Origin: *");
  // header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
  // header("Access-Control-Max-Age: 3600");
  // header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  // header("Content-Type: application/json; charset=UTF-8");

  \HttpHeader::sendAccessControlAllowHeaders('*');
  \HttpHeader::sendAccessControlAllowMethods('OPTIONS,GET,POST,PUT,DELETE');
  \HttpHeader::sendAccessControlMaxAge(3600);
  \HttpHeader::sendAccessControlAllowHeaders('Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
  \HttpHeader::sendStatusCode($response['status_code']);

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
  try {
    if (isset($uri[2]) and !is_array($uri[2])) {
      if ($obj = new $uri[1]($uri[2]) and $obj->isRecord()) {
        $response['status_code'] = \HttpStatusCode::OK;
        $response['body'] = json_encode($obj->getArrayCopy());
        return $response;
      } else {
        return ['status_code' => \HttpStatusCode::BadRequest, 'body' => null];
      }
    }

    // no key provided, return all or selection
    // paging would be nice here

    $result = [];

    if (isset($uri[2]) and is_array($uri[2])) {
      $where = [];
      foreach ($uri[2] as $key => $value) {
        $where[$key] = urldecode($value);
      }
      foreach (($uri[1])::findAll($where) as $o) {
        $result[] = $o->getArrayCopy();
      }
    } else {
      foreach (($uri[1])::findAll() as $id => $obj)
        $result[] = $obj->getArrayCopy();
    }

    if (count($result) === 0) {
      return ['status_code' => \HttpStatusCode::NoContent, 'body' => null];
    }
  } catch (\InvalidArgumentException $e) {
    $body = json_encode(['message' => $e->getMessage()]);
    return [
      'status_code' => \HttpStatusCode::BadRequest,
      'body' => $body,
    ];
  } catch (\Exception $e) {
    $body = json_encode(['message' => $e->getMessage()]);
    return [
      'status_code' => \HttpStatusCode::BadRequest,
      'body' => $body,
    ];
  }

  return ['status_code' => \HttpStatusCode::OK, 'body' => json_encode($result)];
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
  try {
    $obj = $uri[1]::createFromArray($input);
    if ($obj->freeze()) {
      $response['status_code'] = \HttpStatusCode::OK;
      $response['body'] = json_encode(['id' => $obj->getKeyValue(), 'result' => 'created']);
    } else {
      $response['status_code'] = \HttpStatusCode::InternalServerError;
    }
  } catch (\Exception $e) {
    $response['status_code'] = \HttpStatusCode::BadRequest;
    $response['body'] = json_encode(['result' => $e]);
  } finally {
    return $response;
  }
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

  if (!isset($uri[2])) {
    return ['status_code' => \HttpStatusCode::BadRequest, 'body' => null];
  }

  $obj = new $uri[1]($uri[2]);
  if ($obj->isRecord()) {
    $input = json_decode(file_get_contents('php://input'), true);
    try {
      $obj->setFromArray($input);

      if ($result = $obj->freeze()) {
        $response['status_code'] = \HttpStatusCode::OK;
        $response['body'] = json_encode(['id' => $obj->getKeyValue(), 'result' => $result]);
      } else {
        $response['status_code'] = \HttpStatusCode::InternalServerError;
        $response['body'] = null;
      }
    } catch (\Exception $e) {
      $response['status_code'] = \HttpStatusCode::InternalServerError;
      $response['body'] = $e->getMessage();
    } finally {
      return $response;
    }
  }
  return ['status_code' => \HttpStatusCode::NotFound, 'body' => null];
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
  if (!isset($uri[2])) {
    return ['status_code' => \HttpStatusCode::BadRequest];
  }

  try {
    $obj = new $uri[1]($uri[2]);
    if (!$obj->isRecord()) {
      return ['status_code' => \HttpStatusCode::NotFound, 'body' => null];
    }
    $response['status_code_header'] = 'HTTP/1.1 200 DELETED';
    $response['body'] = json_encode(['id' => (int)$uri[2], 'result' => $obj->delete()]);
  } catch (\Exception $e) {
    $response['status_code'] = \HttpStatusCode::BadRequest;
    $response['body'] = $e->getMessage();
  } finally {
    return $response;
  }

  return ['status_code' => \HttpStatusCode::NotFound, 'body' => null];
}
