<?php

require_once __DIR__ . '/init.php';



$data =[];
for( $i =1; $i<3;$i++){
		$item =new stdClass();
		$item->id=$i;
		$item->name ='Yang Qing-rong'.$i;
		
		$data[] =$item;
}


$content = $template->fetch('demo.foreach',
                compact('data')
           );
echo $content;
?>