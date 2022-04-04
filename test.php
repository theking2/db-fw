<?php declare(strict_types=1);

include './inc/util.php';
$obj = new \NeueMedien\test();

$obj->Name = 'Aaa';
$obj->freeze();