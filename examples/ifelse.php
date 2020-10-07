<?php

require_once __DIR__ . '/init.php';


$vars =[];
$vars['score'] = 85;


$content = $template->fetch('demo.ifelse',$vars);
echo $content;
?>