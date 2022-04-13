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
  <title>Projekten</title>
</head>
<body>

<section class="project-list">
<?php
foreach( new \NeueMedien\projectview() as $projectID => $project ){
  echo "<div class='project-tile' href='project-detail.php?pid=$projectID'>";
  echo "<div class='flip-card-inner'>";
  echo wrap_tag('div', wrap_tag( 'h1', $project-> ProjectName), 'flip-card-front');
  echo wrap_tag('div',
    wrap_tag( 'h3', $project-> ProjectName) .
    wrap_tag( 'p', "[{$project-> ProjectNr}]" ) . 
    wrap_tag( 'p', "Type: {$project-> ProjectType}" ) . 
    wrap_tag( 'p', "Coach: {$project-> Coach}" ), 
    'flip-card-back');
  echo "</div>";	
  echo "</div>";
} 
?>
</section>
  
</body>
<script>
  var projectList = document.querySelector('.project-list');
  projectList.addEventListener('click', function(e){
    const projectTile = e.target.closest('.project-tile');
    if(projectTile){
      e.preventDefault();
      window.location.href = projectTile.getAttribute('href');
    }
  });


</script>
</html>