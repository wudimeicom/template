<?php
namespace Wudimei;

use Wudimei\Template\Parser;

require_once __DIR__ . '/Template/Parser.php';
require_once __DIR__ . '/Template/Lexer.php';

class Template
{
  protected  $config = [];
  protected  $op_array = [];
  
	 public  function __construct($cfg){
		    $this->setConfig ($cfg);
	 }

  protected  function setConfig($c){
     
     $cfg = [
         'paths' => [ '' ] ,
         'compiled' => '',
         'ext' => 'html',
	        'force_compile' => true,
	        'compile_check' => true,
	        'write_do_not_edit_comment' => true,
	        'reduce_white_chars' => true,
     ];
     if( !empty( $c ))
     {
       $cfg = array_merge($cfg,$c);
     }
		   $this->config = $cfg;
	 }
  
  public function config($key)
  {
    return $this->config[$key];
  }
	 public function fetch($view,$vars){
	    unset($vars['_']);
	    unset($vars['M']);
	    unset($vars['V']);
	    $cpath = $this->getCompiledPath($view);
	    $require_compile =false;
	    if(file_exists($cpath) == false)
	    {
	      $require_compile =true;
	    }
	    if( $this->config['force_compile'] == true)
	    {
	      $require_compile =true;
	    }
	    elseif( $this->config['compile_check'] == true)
	    {
	      $view_path = $this->getViewPath($view);
	      if( filemtime($view_path) > filemtime($cpath) )
	      {
	        $require_compile =true;
	      }
	    }

			  $parser =new Parser($this,$view);
	    if( $require_compile == true){
			    $code = $parser->parse();
			    $dir = dirname( $cpath );
			    if(!is_dir($dir)){
			     mkdir($dir,0777,true);
			    }
			    file_put_contents($cpath,$code);
			   // echo 'compiling';
	    }
	    require_once $cpath;
	    $cls = $parser->className($view);
	    $obj = new $cls();
	    $c = $obj->M($vars);
	    return $c;
	 }

	 public function getPath($view){
	   $v = str_replace('.','/',$view);
	   return $v .'.' . $this->config['ext'];
	 }


	 public function getViewPath($view){
	   $absPath = '';
	   foreach( $this->config['paths'] as $path )
	   {
	      $p = $path .'/' . $this->getPath($view);
	      if( file_exists($p ))
	      {
	         $absPath = $p;
	      }
	   }
	   if(!file_exists($absPath))
	   {
	     echo 'view file: '.$this->getPath($view).' not found!';
	   }
	   return $absPath;
	 }

	 public function getCompiledPath($view){
	    $v = str_replace('.','/',$view);
	    return $this->config['compiled'].'/'.$v.'.phtml'; 
	 }

	 public function addOp($op)
	 { 
	   if( is_string($op)){
	     $this->op_array[] =  $op;
	   }
	   else{
	     $this->op_array = array_merge($this->op_array,$op);
	   }
	 }

	 public function getOpArray(){
	    return $this->op_array;
	 }
}
?>