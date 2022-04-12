<?php
require_once './inc/util.php';

?>
<link rel="stylesheet" href="./assets/style/main.css">
<section class="project-list">
<?php
foreach( new \NeueMedien\reservationview() as $resID => $reservation ){
  echo "<a class='project-tile' href=project-detail.php?pid=$resID>";
  echo wrap_tag('div',
    wrap_tag( 'h2', $reservation-> Name ) .
    wrap_tag( 'p', "[{$reservation-> Number}]" ) . 
    wrap_tag( 'p', "Type: {$reservation-> Fullname}" ) . 
    wrap_tag( 'p', "Start: {$reservation-> Start->format('d.m.Y h:m')}" ) );
    if( $reservation-> End ) {
      echo wrap_tag('p', "End: {$reservation-> End->format('d.m.Y h:m')}" );
    } else {
      echo wrap_tag('p', "End: <i>unbekannt</i>" );
    }
  echo "</a>";
} 
?>
</section>
