<?php
require_once './inc/util.php';

?>
<link rel="stylesheet" href="./style/main.css">

<section class="project-detail">
<?php
$project = new \NeueMedien\Project($_GET['pid']);
echo wrap_tag('h1', $project->ProjectName);
echo wrap_tag('p', $project-> ProjectNr);
echo wrap_tag('p', $project-> ProjectCoach);
echo wrap_tag('p', $project-> ProjectDescription);
?>
<a href="./">Zurück</a>
</section>