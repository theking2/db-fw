<?php

declare(strict_types=1);
require_once './inc/util.php';

?>
<!DOCTYPE html>
<link rel="stylesheet" href="./assets/style/main.css">
<script src="./assets/js/main.js"></script>
<section class="project-detail" style="width:400px;margin:auto">
  <?php
  $project = new \NeueMedien\project();
  if ($project->thaw((int)$_GET['pid'])) : ?>

    <h1><?= $project->Name ?></h1>
    <input type="hidden" id="pid" value="<?= $project->ID ?>">
    <label for="project-nr">Projektnummer</label><input type="text" id="project-nr" value="<?= $project->Number ?>" readonly>
    <label for="project-type">Projekttyp</label>
    <select id="project-type">
      <?php foreach( \NeueMedien\projecttype::findAll() as $typeID => $type) { ?>
        <option value='<?=$typeID?>' <?=$typeID === $project->TypeID ? 'selected' : ''?> ><?=$type->Name?></option>
      <?php } ?>
    </select>
    <label for="coach">Coach</label>
    <select id="coach">
      <?php foreach( \NeueMedien\teacher::findAll() as $coachID => $coach) { ?>
        <option value='<?=$coachID?>' <?=$coachID === $project->Coach ? 'selected' : ''?> ><?=$coach->FullName?></option>
      <?php } ?>
      <label for="description">Beschreibung:</label>
      <textarea id="description" name="description"><?= $project->Description; ?></textarea>
      <section class="student-detail">
        <?php
        $where = ['ProjectID' => '=' . $project->ID];
        $order = ['Fullname'=> "ASC"];
        foreach (\NeueMedien\studentprojectview::findAll(where: $where, order: $order ) as $id => $detail) { ?>
          <ul>
            <li data-id="<?= $detail->StudentID ?>">
              <?= $detail->Fullname ?>
              (<?= $detail->Role ?>)</li>
          </ul>
        <?php
        } ?>
      </section>
    <?php else : ?>
      <p>Das Projekt konnte nicht gefunden werden.</p>
    <?php endif; ?>

    <a href="./">Zurück</a>
</section>
<script src="./assets/js/autocomplete.js"></script>
<script>
  $('#project-type').onchange = async ev => {
    const projectID = document.getElementById('pid').value;
    const projectTypeID = ev.target.value;
    const response = await fetch(`./project/${projectID}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        TypeID: projectTypeID
      })
    });
    if (response.ok) {
      location.reload();
    }
  };
  $('#coach').onchange = async ev => {
    const projectID = document.getElementById('pid').value;
    const coachID = ev.target.value;
    const response = await fetch(`./project/${projectID}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        Coach: coachID
      })
    });
    if (response.ok) {
      location.reload();
    }
  };
</script>