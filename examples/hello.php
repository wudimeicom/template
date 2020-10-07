<?php

require_once __DIR__ . '/init.php';


$vars =[];
$vars['name'] ='Yang Qing-rong';


$content = $template->fetch('demo.hello',$vars);
echo $content;
?>