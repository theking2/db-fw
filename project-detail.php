<?php
require_once './inc/util.php';

?>
<link rel="stylesheet" href="./assets/style/main.css">

<section class="project-detail">
<?php
$project = new \NeueMedien\project();
$project-> thaw($_GET['pid']);
echo wrap_tag('h1', $project->Name);
echo wrap_tag('p', $project-> Number);
echo wrap_tag('p', $project-> Coach);
echo wrap_tag('p', $project-> Description);
?>
<a href="./">Zurück</a>
</section>

<?php
$details = new \NeueMedien\studentprojectview();
$details-> setWhere([ 'ProjectID' => '=' . $project->ID ]);
foreach($details as $id=> $detail) {?>
<section class="student-detail">
  <ul>
    <li data-id="<?= $detail->StudentID ?>">
      <?= $detail->Fullname ?>
      (<?= $detail-> Role?>)</li>
  </ul>
<?php  }
   
