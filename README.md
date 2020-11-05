# template
this project has been move to : [wudimei/template](https://github.com/wudimei/template)
Wudimeicom/template is a php template engine like blade,They aren't the same.


# License
This software is distributed under the [LGPL 2.1](http://www.gnu.org/licenses/lgpl-2.1.html) license, along with the [GPL Cooperation Commitment](https://gplcc.github.io/gplcc/). Please read LICENSE for information on the software availability and distribution.

# Installation
```sh
composer require wudimei/template:dev-main
```

# Usage

### examples/init.php
```php
<?php
/*
ini_set("display_errors",true);
error_reporting(E_ALL|E_ERROR);
*/
use Wudimei\Template\Engine;

//require_once __DIR__ . '/../src/Template/Engine.php';
require_once __DIR__ . '/vendor/autoload.php';

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


$template =new Engine($config);

```

### examples/hello.php
```php
<?php

require_once __DIR__ . '/init.php';


$vars =[];
$vars['name'] ='Yang Qing-rong';


echo  $template->fetch('demo.hello',$vars);

?>
```

### examples/view/demo/hello.html
```Blade
hello,{{$name}}!
```

### browser output
```html
hello,Yang Qing-rong!
```
## keywords

Template's keywords are : `_`,`V`,`M`

`$_` the template content variable.

`$V` keep the variables assigned by you.

`M`  is the main section name.

## @
two `@@` represent `@` itself.

input
```Blade
Email: yangqingrong@@wudimei.com
```
output
```html
Email: yangqingrong@wudimei.com
```
### examples/ifelse.php
```php
<?php

require_once __DIR__ . '/init.php';


$vars =[];
$vars['score'] = 85;


echo  $template->fetch('demo.ifelse',$vars);

?>
```
### examples/view/demo/ifelse.html
```Blade

@if( 90 <= $score && $score <=100)
A
@elseif( 80 <= $score && $score <90)
B
@elseif( 70 <= $score && $score <80)
C
@elseif( 60 <= $score && $score <70)
D
@else
E
@endif


```

### browser output
```html
B
```

### examples/foreach.php
```php
<?php
require_once __DIR__ . '/init.php';

$data =[];
for( $i =1; $i<3;$i++){
 $item =new stdClass();
	$item->id=$i;
	$item->name ='Yang Qing-rong'.$i;
	$data[] =$item;
}

echo $template->fetch('demo.foreach',compact('data'));
?>
```
# Foreach
`@foreach` as same as `foreach` in php
if $data is empty,goto `@foreachelse` block.

### examples/view/demo/foreach.html
```Blade
<table border="1">
@foreach($data as $row)
 @if($row->id > 0)
  <tr>
   <td>{{$row->id }}</td>
   <td>{!!$row->name!!}</td>
  </tr>
 @endif
@foreachelse
  
 Sorry,no data.

@endforeach
</table>
```

### browser output
```html
<table border="1">

 
  <tr>
   <td>1</td>
   <td>Yang Qing-rong1</td>
  </tr>
 
 
  <tr>
   <td>2</td>
   <td>Yang Qing-rong2</td>
  </tr>
 </table>
```

### comment
```Blade
{{--
comment here,won't be shown
--}}
```


### @php @endphp
the code inside `@php` and `@endphp` will be translate to php tags `<?php` and `?>`.
if you wanna display a variable,please append the var to `$_`,the content var.

```Blade
@php
$ad ="Wudimei Template Engine is free of charge.";
$_ .= $ad; //output to template

@endphp
```
### keep
the code between `@keep` and `@endkeep` don't change.

```Blade
@keep
	
	@foreach($data as $row)
	 @if($row->id > 0)
	  {{$row->id }}
	 @endif
	@endforeach
@endkeep
```

## @include
`@include(const string viewName,array $variables)`

 include a view by viewName,also pass view variables to it.
 
###  examples/view/components/nav.html
```Blade
<nav style="background-color:#E8E8E8;">

{{$date}}

{{$title}}

</nav>
```
In another file,let's include `components.nav`,and pass an array to the second argument.
```Blade
@include('components.nav',['date' => '2020-10-07','title'=>$title])
 
```


## Extends 

`@extends(const string parentViewName )`

`@extends` similar the OOP's extending.

The super view,or parent view look like below:

###  examples/view/layout/default.html
```Blade
<!DOCTYPE html>
<html>
<head>
@section('head')

@endsection
</head>
<body>

@section('content')

@endsection

</body>
</html>
```

### examples/view/demo/extends.html
now,we create a sub view,to enhance it.

```Blade
@extends('layout.default')

@section('head')

  
@endsection

@section('content')

 @include('components.nav',['date' => '2020-10-07','title'=>$title])
 
 <h1>
 {{$title}}
 </h1>
 
@endsection
 ```
## yield

file name: `examples/layout/yield.html`

`@yield('section_name' ,'default value')`

`@yield` is similar the `@section`,but `@yield` is a bachelor,no `@endyield`.
```Blade
<!DOCTYPE html>
<html>
<head>
@section('head')

@endsection
</head>
<body>

@yield('content' ,$title)

</body>
</html>
```
file name: `examples/demo/yield.html`

`@parent` get parent section or yield 's content,and render here.

```Blade
@extends('layout.yield')

@section('head')

  
@endsection

@section('content')
 @parent()
 <h1>
 {{$title}}
 </h1>
 
@endsection
```

## Page Cache


`public Template::cache( $cacheName,$seconds,$func )`

if page cache exists and not expired,return cache content.

otherwise,call $func you have given,store result in cache file,finally,return the cache content.

```php
<?php

require_once __DIR__ . '/init.php';

$cid =1;
$page =2;

$cacheName= 'article_'.$cid.'_'.$page;

echo $template->cache( $cacheName , 5,function() use($template,$cid,$page){

  $name ='Yang Qing-rong';
  $name .= ' , cid: '.$cid . ' , '.$page .' ';
  $name .= date('Y-m-d H:i:s');
  
  return $template->fetch('demo.hello',compact('name'));

});


?>
```

 ## customize
```php
<?php

require_once __DIR__ . '/init.php';

function op_loop( $args ){
  list( $data,$item ) =preg_split('#\s*,\s*#',$args);
  $code = ' foreach( ' .$data .' as '.$item .'){ ';
  return $code;
}

function op_endloop( $args ){
  return '}';
}

function op_sayHello( $args )
{
  $code = ' $arr = ['.$args.']; ';
  $code .= ' $__TPL .= "hello,".$arr[0]."!"; ';
  return $code;
}

$template->addOp(['loop','endloop']);
$template->addOp('sayHello');


$students =[ ['name'=>'yqr','id'=>1],
 ['name'=>'yqr2','id'=>2]];
 

echo $template->fetch('demo.customize',compact('students'));

?>
```

### examples/view/demo/customize.html
The OP `@loop`,`@endloop` and `@sayHello` were defined above.
 
```Blade
@loop( $students , $stu )

 @if( $stu['id'] > 0)
 {{$stu['id']}} {{$stu['name']}} <br />
 @endif
 
@endloop


@sayHello('Wudimei Template Engine!')
```

## Donation

if you want,feel free to donate very small amout money to me for helping this project,include future improvement,bug fix.

TIP:THIS PROJECT IS `FREE OF CHARGE` ! DONATION IS `NOT REQUIRED`!

wechat:wudimei_com

alipay:wudimei_com@163.com

paypal: yangqingrong@gmail.com

![wechat](https://assets.wudimei.com/YangQing-rong/YangQing-rong.wechat.png)

![alipay](https://assets.wudimei.com/YangQing-rong/YangQing-rong.alipay.jpg)

[paypal.me/yangqingrong1985](https://paypal.me/yangqingrong1985)

TIP:THIS PROJECT IS `FREE OF CHARGE` ! DONATION IS `NOT REQUIRED`!

