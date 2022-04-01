<?php
require_once './inc/util.php';

?>
<link rel="stylesheet" href="./assets/style/main.css">
<section class="project-list">
<?php
foreach( new \NeueMedien\projectview() as $projectID=>$project ){
  echo "<a class='project-tile' href=project-detail.php?pid=$projectID>";
  echo wrap_tag('div',
    wrap_tag( 'h2', $project-> ProjectName ) .
    wrap_tag( 'p', $project-> ProjectDescription ) .
    wrap_tag( 'p', "({$project-> Coach})" ) ,
    "project-tile", null, ['pid'=>$projectID] );
  echo "</a>";
} 
?>
</section>
