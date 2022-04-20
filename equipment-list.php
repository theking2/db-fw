<?php declare(strict_types=1);

require_once './inc/util.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./assets/style/main.css">
  <title>Equipment</title>
</head>
<body>
<section class="project-list">
<?php
foreach( new \NeueMedien\equipmentview() as $eID => $equipment ){
  echo "<a class='project-tile' href=equipment-detail.php?pid=$eID>";
  echo wrap_tag('div',
    wrap_tag( 'h2', $equipment-> Name ) .
    wrap_tag( 'p', "[{$equipment-> Number}]" ) .
    wrap_tag( 'p', "{$equipment-> Type}" ) );
  echo "</a>";
} 
?>
</section>
  
</body>
</html>
