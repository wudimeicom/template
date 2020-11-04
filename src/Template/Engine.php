<?php
namespace Wudimei\Template;

use Wudimei\Template\Parser;

require_once __DIR__ . '/Parser.php';
require_once __DIR__ . '/Lexer.php';

class Engine
{
  protected  $config = [];
  protected  $op_array = [];
  
	 public  function __construct($cfg){
		    $this->config ($cfg);
	 }

  
  public function config($key,$value=null )
  {
    if( $value == null ){
      if( is_string( $key ))
      {
        return $this->config[$key];
      }
      elseif( is_array( $key ))
      {
			     $cfg = [
			         'paths' => [ '' ] ,
			         'compiled' => '',
			         'cache_path' => '',
			         'cache_N' =>  3,
			         'ext' => 'html',
				        'force_compile' => true,
				        'compile_check' => true,
				        'write_do_not_edit_comment' => true,
				        'reduce_white_chars' => true,
			     ];
			     if( !empty( $key ))
			     {
			       $cfg = array_merge($cfg,$key);
			     }
					   $this->config = $cfg;
      }
    }
    else{
      
      return $this->config[$key]=$value;
    }
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

	 public function getCachePath($cacheName){
	    $p = $this->config['cache_path'];
	    $N = $this->config['cache_N'];
	    if($N>10)
	    {
	      $N =10;
	    }
	    $md5 = md5($cacheName);
	    for($i=0;$i<$N;$i++){
	      $p .= '/'.substr($md5,$i,1);
	    }
	    $p .= '/'.$cacheName;
	    
	    return $p; 
	 }
	 
	 public function cache( $cacheName,$seconds,$func ){
	    $cachePath = $this->getCachePath($cacheName);
	    if( file_exists($cachePath) ){
	       if( filemtime($cachePath) +$seconds > time() )
	       {
	          return file_get_contents($cachePath);
	       }
	    }
	    $str = call_user_func($func);
	    $dir = dirname( $cachePath);
	    if( !is_dir($dir))
	    {
	      mkdir( $dir,0777,true);
	    }
	    file_put_contents( $cachePath,$str  );
	    
	    return $str;
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