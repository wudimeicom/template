<?php

require_once __DIR__ . '/init.php';


$vars =[];
$vars['links'] = [
 'hello.php',
 'ifelse.php',
 'foreach.php',
 'extends.php',
 'use_layout.php',
 'parent.php',
 'yield.php',
 'cache.php',
 'combine_cache_with_dynamic.php',
 'customize.php',
];

echo $template->fetch('demo.index',$vars);

?>