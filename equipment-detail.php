<?php declare(strict_types=1);
require_once './inc/util.php';

$eq = new \NeueMedien\equipmentview();
$eq-> thaw($_GET['pid']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?=$eq-> Name?></title>
</head>
<body>
  
</body>
</html>

<link rel="stylesheet" href="./assets/style/main.css">

<section class="project-detail">
<?php
echo wrap_tag('h1', $eq->Name);
echo wrap_tag('p', $eq-> Number);
echo wrap_tag('p', $eq-> Description);
?>
<a href="./equipment-list.php">Zurück</a>
</section>

<?php
$details = new \NeueMedien\reservationview();
$details-> setWhere([ 'ID' => '=' . $eq->ID ]);
foreach($details as $id=> $detail) {?>
<section class="student-detail">
  <h2>Reservationen</h2>
  <ul>
    <li data-id="<?= $detail->ID ?>">
      <?= $detail-> Fullname ?>
      (<?= $detail-> Start-> format('d.m.Y H:i')?>)
    </li>
  </ul>
<?php  }
   
