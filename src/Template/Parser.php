<?php
namespace Wudimei\Template;

class Parser
{
  protected $template;
  protected $view;
  private $_includes =[];

	 public  function __construct($template,$view){
		  $this->template = $template;
		  $this->view = $view;
	 }

	 public  function parse(){
	    $viewPath = $this->template->getViewPath($this->view);
	    $content = file_get_contents($viewPath);
	    $tks = Lexer::lex($content);
	    $tks = $this->translate($tks);
	    $struct = $this->parse_template_struct($tks);
	    return $this->generateCode($struct);
	 }

	 protected function translate($tks){
	   foreach( $tks as $i => $t){
	      $tag = $t['TAG'];
	      $src = isset($t['SRC'])?$t['SRC']:'';
	      $code = '';
	      if( $tag == 'COMMENT'){
	        $code = $this->php( '/'.'* '.$src.' *'.'/' );
	      }
	      elseif( $tag == 'AT')
	      {
	        $code ='$__TPL .=\'@\'; ' ;
	      }
	      elseif( $tag == 'PHP')
	      {
	        $code = $this->php($src );
	      }
	      elseif( in_array($tag, ['KEEP','PLAIN']))
	      {
	        $code = '$__TPL .= '.var_export($src,true) .';';
	      }
	      elseif( $tag == 'OUT' )
	      {
	        $code = $this->php( '$__TPL .=  htmlspecialchars('. $src.');' );
	      }	
	      elseif( $tag == 'OUT_UNESCAPED' )
	      {
	        $code = $this->php( '$__TPL .= '. $src.';' );
	      }
	      elseif( $tag == 'OP')
	      {
	        $name = $t['NAME'];
	        $args = $t['ARGS'];
	        if( in_array($name,['if'])){
	          $code = $this->php( $name .'( '.$args.' ){ ') ;
	        }
	        elseif(in_array($name,['elseif'])){
	        
	          $code = $this->php( '}'. $name .'( '.$args.' ){ ') ;
	        }
	        elseif(in_array($name,['else'])){
	          $code = $this->php( '}'. $name .'{ ') ;
	        }
	        elseif(in_array($name,['endif'])){
	          $code = $this->php( '}') ;
	        }
	        elseif(in_array($name,['foreach'])){
	          list($data,$item) =preg_split('#\s+as\s+#i',$args);
	          $c = 'if( !empty('.$data.')){';
	          $c .= 'foreach('.$args.'){';
	          $code = $this->php( $c) ;
	        }
	        elseif(in_array($name,['foreachelse'])){
	        
	          $code = $this->php( '}}else{{') ;
	        }
	        elseif(in_array($name,['endforeach'])){
	        
	          $code = $this->php( '}}') ;
	        }
	        elseif( $name == 'include' )
	        {
	           $code = '$inc_args =['.$args.'];'."\n\n";
	           
	           preg_match('#\'([^\']+)\'#A',$args,$a);
	           //print_r($a);
	           $inc_name = trim($a[1],'"\'');
	           $this->_includes[$inc_name]=1;
	           $cls =$this->className($inc_name);
	           $code .= '$inc_obj =new '.$cls.'();'."\n\n";
	           $code .= '$__TPL .=$inc_obj->__MAIN($inc_args[1]);'."\n\n";
	        }
	        else{
	          $opArr = $this->template->getOpArray();
	          if( in_array($name,$opArr))
	          {
	            $code .= call_user_func_array('op_'.$name,[$args]);
	          }
	        }
	      }
	      $t['CODE'] = $code;
	      $tks[$i] = $t;
	   }
	   return $tks;
	 }

	 protected function php($code)
	 {
	   return ' '. $code .' ';
	 }
	 
	 protected function parse_template_struct($tks)
	 {
	    $section = '__MAIN';
	    $sections = [$section=>[]];
	    $stack = [$section];
	    $super_view ='';
	    foreach( $tks as $i => $t){
	      $args = isset($t['ARGS'])?$t['ARGS']:'';
	      $op_name = isset($t['NAME'])?$t['NAME']:'';
	      if($op_name == 'section')
	      {
	        
	        $a =token_get_all($args);
	        $section_name = trim($a[0][1],'"\'');
	        $sections[$section][] = [
	         'TAG' => 'CALL',
	         'CODE' => '$__TPL .= $this->'.$section_name.'($__VARS);'
	        ];
	        $section = $section_name;
	        array_push($stack,$section);
	      }
	      elseif($op_name == 'endsection')
	      {
	        array_pop($stack);
	        $section =end($stack);
	      }
	      elseif($op_name == 'extends')
	      {
	        $a =token_get_all($args);
	        $super_view = trim($a[0][1],'"\'');
	        
	      }
	      else{
	        if( !isset($sections[$section]))
	        {
	           $sections[$section]=[];
	        }
	        $sections[$section][]=$t;
	      }
	    }
	   // print_r($sections);
	    return [
	      'view' => $this->view ,
	      'class' => $this->className($this->view),
	      'super_view' => $super_view,
	      'super_class' => $this->className($super_view),
	      'sections' => $sections
	    ];
	 }
	 
	 public function className($view){
	   if(trim($view)==''){
	     return '';
	   }
	   $cls = str_replace('.','_',$view);
	   return 'view_'.$cls;
	 }
	 
	 protected function generateCode($tpl_struct)
	 {
	   $class = $tpl_struct['class'];
	   $view = $tpl_struct['view'];
	   $super_view = $tpl_struct['super_view'];
	   $super_class = $tpl_struct['super_class'];
	   $sections = $tpl_struct['sections'];
	   $code ='';
	   foreach( $this->_includes as $vn => $_)
	   {
	    $parser =new Parser($this->template,$vn );
	    $code .= $parser->parse();
	   }
	   if($super_class !='')
	   {
	    $parser =new Parser($this->template,$super_view);
	    $code .= $parser->parse();
	   }
	   
	   $code .='<'.'?php '."\n\n";
	   $code .= "/** Please don't edit this content,it was generated by Wudimei Template Engine. */ \n\n";
	   $code .= 'class '.$class;
	   if($super_class !='')
	   {
	      $code .= ' extends '.$super_class;
	   }
	   
	   $code .= '{ '."\n\n";
	   foreach($sections as $method => $codes)
	   {
	      
	     $code .= 'public function '.$method.'($__VARS){ '."\n";
	     if($method =='__MAIN' && $super_class !='')
	     {
	        $code .= '  return parent::__MAIN($__VARS);'."\n";
	     }
	     else{
		     $code .= 'extract($__VARS);'."\n";
		     $code .= '$__TPL =\'\';'."\n";
		     
		     foreach($codes as $t){
		       $code .= $t['CODE']."\n";
		     }
		     $code .= 'return $__TPL;' ."\n";
	     }
	     $code .= '} '."\n\n\n";
	   }
	   
	   $code .= '} ';
	   $code .= ' ?'.'>';
	   return $code;
	 }
}


?>