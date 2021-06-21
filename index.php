<?php
require_once './inc/util.php';

?>
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

<style>
html {
  box-sizing: border-box;
}
*, *:before, *:after {
  box-sizing: inherit;
  margin: 0; padding:0;
}
.project-list {
  display: grid;
  grid-template-columns: repeat(auto-fill,minmax(300px,1fr));
  gap: 5px;
  margin: 0 0 5px;
}

a.project-tile {
  display: block;
  height: 8em;
  width:  300px;
  padding: 10px;
  margin: 10px 20px;
  box-shadow: 0 0 5px teal;
  transition: box-shadow 150ms;
  text-decoration: inherit;
  color: inherit;
}
a.project-tile:hover {
  box-shadow: 0 0 15px teal;

}

</style>