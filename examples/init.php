<?php
ini_set("display_errors",true);
error_reporting(E_ALL|E_ERROR);

use Wudimei\Template;

require_once __DIR__ . '/../src/Template.php';

$config =[
  'paths' => [
    __DIR__.'/view'
  ],
  'compiled' => __DIR__.'/viewc',
  //view's file extension, html
  'ext' => 'html',
  //if true,recompile anyhow
  'force_compile' => true,
  //if view is modified,recompile again.
	 'compile_check' => true,
	 //write "don't edit this content" in compiled file
	 'write_do_not_edit_comment' => false,
	 //multiple white characters to one blank char
	 'reduce_white_chars' => false,
];


$template =new Template($config);
