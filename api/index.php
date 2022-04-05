<?php declare(strict_types=1);

require '../inc/util.php';

if(isset($_GET['type'])) {
  $type = '\\NeueMedien\\'.$_GET['type'];

  if(isset($_GET['id'])) {
    $obj = new $type((int)$_GET['id']);    
    if($obj-> isRecord()) {
      response(200, 'OK', $obj->getJSON());   }
    else {
      response(404, 'Not found', 'No record found');
    }
  } else {
    response(400, 'Bad request', 'No ID given');
  }
} else {
  response(400, 'Bad request', 'No type given');
}


function response($status,$status_message,$data)
{
	header("HTTP/1.1 ".$status);
	
	$response['status']=$status;
	$response['status_message']=$status_message;
	$response['data']=$data;
	
	$json_response = json_encode($response);
	echo $json_response;
}