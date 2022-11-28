<?php

declare(strict_types=1);

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
  <nav>
    <a href="./index.php">Home</a>
    <a href="./timesheet-set.html">Zeiterfassung</a>
    <a href="./equipment-list.php">Equipment</a>
    <a href="./reservation-set.html">Reservation</a>
    <a href="./index.php">Home</a>
  </nav>
  <section class="object-list">
    <?php
    foreach (\NeueMedien\equipmentview::findAll() as $eID => $equipment) {
      echo "<a class='object-tile' href=equipment-detail.php?pid=$eID>";
      echo wrap_tag(
        'div',
        wrap_tag('h2', $equipment->Name) .
          wrap_tag('p', "[{$equipment->Number}]") .
          wrap_tag('p', "{$equipment->Type}")
      );
      echo "</a>";
    }
    ?>
  </section>

</body>

</html>