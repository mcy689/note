### smatry模板引擎

1. Smarty  开源的模板引擎

2. Smarty解析在开始的时候首先将注释替换掉,因为这对程序的执行没有任何意义

3. `smarty` 的注释  `{* 我是一段注释哦 *}` 

4. smarty的调试

   > Smarty内置一个调试控制器，通过调试控制器，你可以获取当前模版页面中所有的变量信息：包括include进来的模版信息，所有在php脚本中assign过的变量以及当前模版页面请求的所有配置文件中的变量，但是不会获取在模版页中通过{assign}内置方法定义的变量信息。
   >
   > **打开调试控制器：**
   > 1.通过设置smarty的$debugging值为TRUE:
   >
   > ```
   > 1 $smarty->debugging = true;
   > ```
   >
   >  
   >
   > 2.通过修改$debugging_ctrl变量，该变量接受两个值：NONE和URL，当设置为NONE时，不会启动调试程序；当设定值为URL时，如果在url地址栏中发现SMARTY_DEBUG请求信息，便会打开控制台。如：
   >
   > ```
   > 1 $smarty->debugging_ctrl = 'URL';
   > 2 //.../index.php?SMARTY_DEBUG
   > ```
   >
   > 可以通过设定smarty_debug_id属性来指定url地址栏中的关键字，默认是SMARTY_DEBUG
   >
   > **注:如果设定$smarty->debugging = true;$debugging_ctrl将不在起作用。** 
   >
   > ​
   >
   > 3.在模版页中使用{debug}标签。使用该标签后，将会忽略PHP脚本中debugging的设定，即使debugging=false，也会打开调试控制脚本。
   > **注:调试控制器只会在使用display()显示模版时生效，而不能在fetch()中使用。** 
   > ​
   >
   > **调试控制器模版：**
   > 默认的调试控制器模版是smarty目录下的debug.tpl文件。可以通过$debug_tpl变量来指定一个自己的控制器模版。
   >
   > ```
   > 1 $smarty->debug_tpl = '.../myDebug.tpl';
   > ```
   >
   > ​

5. Smarty配置代码示例

   ```php
   //引入类文件
   	include './libs/Smarty.class.php';
   	//实例化对象
   	$smarty = new Smarty;

   	//初始化参数
   	$smarty->template_dir = './view';//默认的模板(html文件)存放目录
   	$smarty->compile_dir = './view_c';//临时文件(编译文件)的存放目录
   	$smarty->config_dir = './config';//配置文件目录
   	$smarty->left_delimiter = '{';
   	$smarty->right_delimiter = '}';

   	//分配变量
   	$smarty->assign('title','smarty基本使用')
       //显示
       $smarty -> display('1.html');
   ```

6. block可以在模板上定义一块区域，以进行模板继承。

   * 子模板中的`{block}`  区域代码 , 将会替换父模板对应的区域代码

     ```html
     <--父级html中的代码-->
     <!DOCTYPE html>
     <html lang="en">
     <head>
     	<meta charset="UTF-8">
     	<title>首页页面</title>
     	{block name="css"} {/block}
     </head>
     <body>
         {block name="header"}
     	<header style="height:100px;background:orange"></header>
     	{/block}
       
     	<section style="height:400px;background:yellowgreen"></section>

     	{block name="footer"}
     	<footer style="height:100px;background:pink"></footer>
     	{/block}
     </body>
     </html>

     <--子级个html中的代码-->
     {extends file="index.html"}

     {*修改元素*}
     {block name="header"}
     <header style="height:100px;background:green">{include file="slider.html"}</header>
     {/block}

     {*增加*}
     {block name="css"} 
     <link rel="stylesheet" href="3.css">
     {/block}

     {*删除*}
     {block name="footer"}
     {/block}
     ```

7.  {include} 引入文件

   ```html
   {include file="header.html"}
   	<section style="height:400px;background:yellowgreen"></section>
   {include file="footer.html"}
   ```

8. include   和 extends 模板继承 区别

   >  模板继承
   >
   > * 模板继承可以让你定义一个或多个父模板，提供给子模板来进行扩展。 扩展继承意味着子模板可以覆盖部分或全部父模板的块区域。
   >
   > * 继承结构可以是多层次的，所以你可以继承于一个文件，而这个文件又是继承于其他文件，等等。
   >
   > * 在覆盖父模板的{block}块以外的地方， 子模板不能定义任何内容。任何在{block}以外的 内容都会被自动忽略。
   >
   > * 在子模板和父模板中的`{block}` 内容，可以通过 `append` 和 ` prepend` 来进行合并。 `{block}`  的选项，和 `{$smarty.block.parent}`  或  `{$smarty.block.child}` 会持有这些内容。
   >
   > * 模板继承在编译时将编译成单独的一个编译文件。对比效果相似的{include}包含模板功能，模板继承的性能更高。
   >
   > * 子模板继承使用{extends}标签， 该标签一定放要在子模板的第一行。
   >
   >   ### Note
   >
   >   如果你的子模板里面有用到[`{include}`](http://www.smarty.net/docs/zh_CN/language.function.include.tpl) 来包含模板，而被包含的模板里面存在供[`{include}`](http://www.smarty.net/docs/zh_CN/language.function.include.tpl)模板 调用的[`{block}`](http://www.smarty.net/docs/zh_CN/language.function.block.tpl)区域， 那么在最顶层的父模板里面，你需要放置一个空的 [`{block}`](http://www.smarty.net/docs/zh_CN/language.function.block.tpl) 来作为继承。
   >
   >   ​

9.  数组的遍历  `{foreach $arr as $ k => $v }`    `{/foreach}` 

   ```html
   <!-- 循环控制 -->
   	<ul>
   	{foreach $fruit as $k=>$v}
   		<li>{$v}</li>
   	{/foreach}
   	</ul>
   ```

10. 流程控制 `{if $a == 1}    {else}    {/if} `

   ```html
   	<!-- 流程控制 -->
   	{if $a == 1}
   		牛逼 没谁了!!!
   	{else}
   		呵呵
   	{/if}
   ```

   ​

11. `  {literal} {/literal}`  标签区域内的数据将被当作文本处理，此时模板将忽略其内部的所有字符信息. 该特性用于显示有可能包含大括号等字符信息的 javascript 脚本 、css样式. 当这些信息处于 `{literal} {/literal}`  标签中时，模板引擎将不分析它们，而直接显示.

    ```html
    {literal}
    	<style>
    		*{margin:0px;padding:0px}
    	</style>
    {/literal}
    ```

12. 在html模板中显示对象的信息时  是采用外部调用的方式  `{$obj->name}`

   ### 注意

   * 在模板中只要是满足解析条件的内容,都会被解析,不管是不是在html注释或者是js注释中
   * 在外部的js文件中和css文件中,不要写smarty的语法内容,因为他们都是静态文件,不会执行php解析
   * 模板的路径是相对于当前请求的php脚本的,而不是当前的html文件
   * 页面中的相对路径,是相对于当前正在请求的那个脚本

   ​

13. smarty 原理php代码示例

    ```php
    <?php 
    	$pdo = new PDO('mysql:host=localhost;dbname=lamp;charset=utf8','root','');
    	$stmt = $pdo -> query('select * from users limit 1');
    	$user = $stmt->fetch();

    	//引入模板文件并  读取html文件的内容
    	$str = file_get_contents('user.html');

    	// 替换已经在html中特殊标识的内容  {$user['name']}  => echo $user['name']
    	$str = str_replace('{', '<?php echo ', $str);
    	$str = str_replace('}', ' ?>', $str);

    	//将内容写入到临时文件
    	file_put_contents('temp.php', $str);

    	//引入到脚本中
    	include 'temp.php';

     ?>

    ```

    ​

    ​ 
