<?php
require_once './inc/util.php';

?>
<link rel="stylesheet" href="./style/main.css">
<section class="project-list">
<?php
$projects = new \NeueMedien\ProjectView();
foreach( $projects as $pID=>$p ){
  echo "<a class='project-tile' href=project-detail.php?pid=$pID>";
  echo wrap_tag('div',
    wrap_tag( 'h2', $p-> ProjectName ) .
    wrap_tag( 'p', $p-> ProjectDescription ) .
    wrap_tag( 'p', "({$p-> ProjectCoach})" )
    , "project-tile", null, ['pid'=>$pID] );
  echo "</a>";
} 
?>
</section>
