<?php
require_once './inc/util.php';

?>
<link rel="stylesheet" href="./assets/style/main.css">

<section class="project-detail">
<?php
$project = new \NeueMedien\Project();
$project-> thaw($_GET['pid']);
echo wrap_tag('h1', $project->Name);
echo wrap_tag('p', $project-> Number);
echo wrap_tag('p', $project-> Coach);
echo wrap_tag('p', $project-> Description);
?>
<a href="./">Zurück</a>
</section>