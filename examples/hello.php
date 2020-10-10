<?php

require_once __DIR__ . '/init.php';


$name ='Yang Qing-rong';


echo $template->fetch('demo.hello',compact('name'));

?>