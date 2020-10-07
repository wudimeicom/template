<?php

require_once __DIR__ . '/init.php';


$vars =[];
$vars['links'] = [
 'hello.php',
 'ifelse.php',
 'foreach.php',
 'extends.php',
 'customize.php'
];

$content = $template->fetch('demo.index',$vars);
echo $content;
?>