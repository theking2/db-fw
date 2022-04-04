<?php declare(strict_types=1);

require_once './inc/util.php';

if( isset($_GET['id']) ) {
  $id = $_GET['id'];
  $project = new \NeueMedien\project($id);
  $project->thaw( (int)$id );
  echo $project->getJSON();
} else {
  echo '[';
  foreach( new \NeueMedien\project() as $id=> $project ) {
    echo $project->getJSON();
    echo ',';
  }
  echo ']';

}

