<?php

require_once __DIR__ . '/init.php';


$title ='This is title';



echo $template->fetch('demo.op_parent',compact('title'));

?>