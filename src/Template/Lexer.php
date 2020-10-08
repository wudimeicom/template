<?php
namespace Wudimei\Template;

class Lexer
{

	public static function lex($str){
		$len = strlen($str);
		$offset =0;

		$arr =[];
		$parr =[

		'#^@{2}#msA' => 'AT',
		'#^@php(.*)@endphp#msA' => 'PHP',
		'#^@keep(.*)@endkeep#msA' => 'KEEP',
		'#^\{\{\-\-(.*)\-\-\}\}#msA' => 'COMMENT',

		'#^@\s*([a-zA-Z0-9\_]+)\s*(\((([^()]|(?R))*)?\))?#' => 'OP',

		'#^\{\{([^}]+)\}\}#' => 'OUT',
		'#^\{\!\!([^!]+)\!\!\}#' => 'OUT_UNESCAPED',
		'#^[^@\{]+#' => 'PLAIN',

		];
		$s = $str;
		while( strlen($s)>0){

			foreach($parr as $p => $tag){
			 $ret = preg_match($p,$s,$a,PREG_OFFSET_CAPTURE);

				if($ret==1){

				  $s = substr($s,strlen($a[0][0]));

				  $t = ['TAG' => $tag ,'CODE' => ''];

				  if( in_array( $tag, ['COMMENT','KEEP','PHP']))
				  {
				    $t['SRC'] = $a[1][0];
				  }
				  elseif($tag == 'OP')
				  {
				    $t['NAME'] = $a[1][0];
				    $t['ARGS'] = isset($a[3])?$a[3][0]:'';
				  }
				  elseif($tag == 'PLAIN')
				  {
				    $t['SRC'] = $a[0][0];
				  }
				  elseif( in_array($tag,[ 'OUT','OUT_UNESCAPED']))
				  {
				    $t['SRC'] = $a[1][0];
				  }
				  $arr[] = $t;
				  break 1;
			 }
			}

		}
	 return $arr;
	}

}
?>