<?php

declare(strict_types=1);
require_once './inc/util.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./assets/style/main.css">
  <script src="./assets/js/main.js"></script>
  <title>Projekten</title>
</head>

<body>
  <nav>
    <a href="./index.php">Home</a>
    <a href="./timesheet-set.html">Zeiterfassung</a>
    <a href="./equipment-list.php">Equipment</a>
    <a href="./equipment-list.php">Equipment</a>
    <a href="./index.php">Home</a>
  </nav>

  <section class="object-list">
    <?php
    foreach (new \NeueMedien\projectview() as $projectID => $project) { ?>
      <div class='object-tile' href='project-detail.php?pid=<?= $projectID ?>'>
        <div class='flip-card-inner'>
          <div class='flip-card-front'>
            <h1><?= $project->ProjectName ?>
          </div>
          <div class='flip-card-back'>
            <h3><?= $project->ProjectName ?>
            <p><?=$project->ProjectNr?>
            <p>Type: <?=$project->ProjectType?>
            <p>Coach: <?=$project->Coach?>
          </div>
        </div>
      </div>
    <?php } ?>
  </section>

</body>
<script>
  $('.object-list').addEventListener('click', function(e) {
    const projectTile = e.target.closest('.object-tile');
    if (projectTile) {
      e.preventDefault();
      window.location.href = projectTile.getAttribute('href');
    }
  });
</script>

</html>