<?php declare(strict_types=1);

include '.\inc\util.php';
include '.\classes\DB\Persist.php';

$obj = new \NeueMedien\Project();
$obj-> thaw(164);
echo "
<pre>
{$obj-> Name}
{$obj-> Description}
{$obj-> Coach}
{$obj-> Status}
{$obj-> ParentID}
{$obj-> Number}
{$obj-> TypeID}
{$obj-> CustomerID}
</pre>";


$obj = new \NeueMedien\Test();
$obj-> thaw(1);
var_dump($obj);
$obj-> Name = 'Ccc';
$obj-> freeze();