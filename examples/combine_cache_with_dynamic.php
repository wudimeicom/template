<?php

require_once __DIR__ . '/init.php';

$cid =1;
$page =2;

$cacheName= 'article_'.$cid.'_'.$page;

$content= $template->cache( $cacheName , 5,function() use($template,$cid,$page){

  $name ='Yang Qing-rong';
  $name .= ' , cid: '.$cid . ' , '.$page .' ';
  $name .= date('Y-m-d H:i:s');
  
  return $template->fetch('demo.hello',compact('name'));

});

echo $template->fetch('demo.cache_with_dynamic',compact('content'));

?>