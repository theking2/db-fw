<?php declare(strict_types=1);

include '.\inc\util.php';

$obj = new \NeueMedien\project();
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

$obj = new \NeueMedien\test();
$name = $obj-> Name = chr(rand(40,90));
$obj-> Size = 1.60+rand(0,3);
$obj-> freeze();

// $obj-> Name = "=$name";
// foreach($obj as $key => $value) {
//   $obj-> delete();
// }


// for( $i=100000; $i>0; $i-- ) {
//   $obj = new \NeueMedien\test();
//   $obj-> Name = chr(rand(40,90));
//   $obj-> size = 1.60+rand(0,30)/100;
//   $obj-> freeze();
// }
$s = "0000-0-0 00:00:00";
$d = \DateTime::createFromFormat('Y-m-d H:i:s',$s);
echo $d-> format("d.m.Y H:i:s");

$obj = new \NeueMedien\test();
$obj-> setWhere( [ 'Name' => "<D" ]);
foreach($obj as $test ) {
  echo "
  <pre>
  {$test-> Name}
  {$test-> Size}
  {$test-> Date-> format('d.m.Y')}
  </pre>";
}
  

// $obj = new \NeueMedien\test();
// $obj-> setWhere( ["size" => ">1.62"]);
// foreach( $obj as $id=> $obj) {
//   $obj-> delete();
// }
// $obj = new \NeueMedien\test();
// $obj-> setWhere( ["Name" => "UY,V"]);
// foreach( $obj as $id=> $obj) {
//   $obj-> delete();
// }