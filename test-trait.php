<?php declare(strict_types=1);

include '.\inc\util.php';

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
$name = $obj-> Name = chr(rand(40,90));
$obj-> groesse = 1.60+rand(0,3);
$obj-> freeze();

$obj-> Name = "=$name";
foreach($obj as $key => $value) {
  $obj-> delete();
}


// for( $i=100000; $i>0; $i-- ) {
//   $obj = new \NeueMedien\Test();
//   $obj-> Name = chr(rand(40,90));
//   $obj-> groesse = 1.60+rand(0,30)/100;
//   $obj-> freeze();
// }
$s = "0000-0-0 00:00:00";
$d = \DateTime::createFromFormat('Y-m-d H:i:s',$s);
echo $d-> format("d.m.Y H:i:s");

$obj = new \NeueMedien\Test();
$obj-> setWhere( [ 'Name' => "<D" ]);
foreach($obj as $test ) {
  echo "
  <pre>
  {$test-> Name}
  {$test-> groesse}
  {$test-> Date-> format('d.m.Y')}
  </pre>";
}
  

// $obj = new \NeueMedien\Test();
// $obj-> setWhere( ["groesse" => ">1.62"]);
// foreach( $obj as $id=> $obj) {
//   $obj-> delete();
// }
// $obj = new \NeueMedien\Test();
// $obj-> setWhere( ["Name" => "UY,V"]);
// foreach( $obj as $id=> $obj) {
//   $obj-> delete();
// }