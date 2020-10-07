<?php

require_once __DIR__ . '/init.php';

function op_loop( $args ){
  list( $data,$item ) =preg_split('#\s*,\s*#',$args);
  $code = ' foreach( ' .$data .' as '.$item .'){ ';
  return $code;
}

function op_endloop( $args ){
  return '}';
}

function op_sayHello( $args )
{
  $code = ' $arr = ['.$args.']; ';
  $code .= ' $__TPL .= "hello,".$arr[0]."!"; ';
  return $code;
}

$template->addOp(['loop','endloop']);
$template->addOp('sayHello');


$students =[ ['name'=>'yqr','id'=>1],
 ['name'=>'yqr2','id'=>2]];
 

echo $template->fetch('demo.customize',compact('students'));

?>