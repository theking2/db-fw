<?php
require_once './inc/util.php';

?>
<link rel="stylesheet" href="./assets/style/main.css">
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