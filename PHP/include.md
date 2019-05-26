#### include 

1. include引入文件
- 被包含文件先按参数给出的路径寻找
- 如果没有给出目录（只有文件名）时则按照 include_path 指定的目录寻找(D:\XAMPP\php\PEAR 目录下)
- 如果在 include_path 下没找到该文件则 include 最后才在调用脚本文件所在的目录和当前工作目录下寻找
- 如果最后仍未找到文件则 include 结构会发出一条警告；这一点和 require 不同，后者会发出一个致命错误。 



2. include是一个特殊的语言结构,其参数不需要括号,在比较返回值的时候__注意__

   ```php
   vars.php
     <?php
     	return ['info'=> 'true'];
   	?>
         
   test.php
    <?php
        if((include 'vars.php')['info'] == true) {
           echo '这是正确的用法';
        }
   ?>
   ```

2. 当一个文件被包含时,语法解析器在目标文件的开头脱离php模式并进入HTML模式 , 到文件结尾处恢复 , 由于此原因, 目标文件中需要作为php代码执行和任何代码都必须被包括在php起始和结束标记

3. 如果include出现于调用文件中的一个函数里, 则被调用的文件中所包含的所有代码将表现的如同它们是在该函数内部定义的一样. 所以它将遵循该函数的变量范围 . 此规则的一个例外是魔术常量, 它们是在发生包含之前就已被解析器处理了.

   ```php
   vars.php
     $color = 'info';
   test.php

   function foo() {
     include 'vars.php';
       echo $color;   //info
   }
   echo $color  //输出变量未定义
   ```

4. 在失败时include 返回false 并且发出警告 , 成功包含则返回1 , 可以在被包括的文件中使用return 语句来终止该文件中程序的执行并返回调用它的脚本,也可以从被包含的文件中返回值. 

   ```php
   vars.php
   <?php
     return [
     			'host' => 'localhost',
   			'dbname' => 'mysql',
     			'port' => '3306'
   	]
     
    test.php
     
     $res = include 'vars.php';

   ```

   ​