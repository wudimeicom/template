<?php

require_once __DIR__ . '/init.php';


$title ='This is title';



echo $template->fetch('demo.yield',compact('title'));

?>