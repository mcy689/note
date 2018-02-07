### 生成器

1. PHP 5.5版本开始支持生成器( Generator )

   * 一个生成器函数看起来像一个普通的函数, 不同的是普通函数返回一个值, 而一个生成器可以`yield` 生成许多它所需要的值
   * 当一个生成器被调用的时候, 它返回一个可以被遍历的对象, 当你遍历这个对象的时候( 通过foreach循环 ) php将会在每次需要值的时候调用生成器函数, 并在产生一个值之后保存生成器的状态, 这样它就可以在需要产生下一个值的时候恢复调用状态.

2. 示例说明

   ```php
    function createRange($number)
    {
        for ($i=0;$i<$number;$i++) {
            yield time(); //一次只产生一个值
        }
    }
   $result = createRange(10);
   foreach($result as $value) {
        sleep(1);
        echo $value.'<br />';
   }
   //调用函数时就会返回一个生成器(Generator)的对象, 这个对象实现了Iterator接口
   var_dump ($result instanceof Iterator);
   ```

3. 说明

   > 跟普通函数值返回次值不同的是, 生成器可以根据需要yield多次, 以便生成需要迭代的值. 普通函数return后, 函数会被从栈中移除, 终止执行, 但是yield会保存生成器的状态, 当被再次调用时, 迭代器会从上次yield的地方恢复调用状态继续执行.

   ```php
   function xrange($start,$end,$step)
   {
       echo 'The generator has started';
       for ($i = $start; $i<= $end; $i += $step){
           yield $i;
           echo "Yielded $i" ."\r\n";
       }
       echo 'The genetator has ended';
   }
   foreach(xrange(1,10,3) as $v){
       echo 'return '.$v."\r\n";
   }


   /* The generator has startedreturn 1
   Yielded 1
   return 4
   Yielded 4
   return 7
   Yielded 7
   return 10
   Yielded 10
   The genetator has ended       */
   ```

   ​