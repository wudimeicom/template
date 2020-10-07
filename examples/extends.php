<?php

require_once __DIR__ . '/init.php';


$title ='This is title';

$data =[];
for( $i =1; $i<3;$i++){
		$item =new stdClass();
		$item->id=$i;
		$item->name ='Yang Qing-rong'.$i;
		
		$data[] =$item;
}

echo $template->fetch('demo.extends',compact('data','title'));

?>