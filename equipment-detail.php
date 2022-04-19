<?php
require_once './inc/util.php';

?>
<link rel="stylesheet" href="./assets/style/main.css">

<section class="project-detail">
<?php
$eq = new \NeueMedien\equipmentview();
$eq-> thaw($_GET['pid']);
echo wrap_tag('h1', $eq->Name);
echo wrap_tag('p', $eq-> Number);
echo wrap_tag('p', $eq-> Description);
?>
<a href="./equipment-list">Zurück</a>
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
   
