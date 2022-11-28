<?php declare(strict_types=1);

require_once './inc/util.php';

if( isset($_GET['id']) ) {
  $id = $_GET['id'];
  $project = new \NeueMedien\project($id);
  $project->thaw( (int)$id );
  echo json_encode($project-> getArrayCopy() );
} else {
  $result = [];
  foreach( \NeueMedien\project::findAll() as $id=> $project ) {
    $result[] = $project-> getArrayCopy();
  }

  header("Content-Type: application/json; charset=UTF-8");
  echo json_encode( $result );
}