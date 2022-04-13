<?php declare(strict_types=1);
require_once './inc/util.php';

?>
<!DOCTYPE html>
<link rel="stylesheet" href="./assets/style/main.css">

<section class="project-detail" style="width:400px;margin:auto">
<?php
$project = new \NeueMedien\Project();
$project-> thaw((int)$_GET['pid']);
?>
<h1><?= $project-> Name ?></h1>
<input type="hidden" id="pid" value="<?= $project-> ID ?>">
<label for="project-nr">Projektnummer</label><input type="text" id="project-nr" value="<?= $project-> Number ?>" readonly>
<label for="project-type">Projekttyp</label>
<select id="project-type">
<?php
foreach( new \NeueMedien\ProjectType() as $typeID => $type ){
  echo "<option value='$typeID' " . ($typeID == $project-> TypeID ? 'selected' : '') . ">{$type-> Name}</option>";
}
?>
</select>
<label for="coach">Coach</label>
<select id="coach">
<?php
foreach( new \NeueMedien\Teacher() as $coachID => $coach ){
  echo "<option value='$coachID' " . ($coachID == $project-> Coach ? 'selected' : '') . ">{$coach-> FullName}</option>";
}
?>
<label for="description">Beschreibung:</label><textarea id="description" name="description"><?=$project-> Description;?></textarea>

<a href="./">Zurück</a>
</section>
<script>
  document.getElementById('project-type').onchange = async ev=> {
    const projectID = document.getElementById('pid').value;
    const projectTypeID = ev.target.value;
    const response = await fetch(`./project/${projectID}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ TypeID: projectTypeID })
    });
    if( response.ok ) {
      location.reload();
    }
  };
</script>
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
   
