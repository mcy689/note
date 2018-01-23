#### table的声明

1. 什么是table？

   * table是`Lua`最复杂最强大的数据结构，`Lua` 本身并不是面向对象语言。最简单地，我们可以把table理解为数组，最复杂的，我们可以把table理解为”世间万物”，因为它可以创造出很多你想象不到的东西。

2. 创建table

   ```lua
   local a = {}
   ```

3. 初始化table, 以及使用

   ```lua
   --第一种
     local a = {["x"] = 12, ["mutou"] = 99, [3] = "hello"}
     print(a["x"]);
   --第二种
     local a = {x = 12, mutou = 99, [3] = "hello"}
     print(a["x"]);
   --第三种
     local a = {x = 12, mutou = 99, [3] = "hello"}
     print(a.x); --记住，是字符串下标才这么做。
   --第四种
     local a = {[1] = 12, [2] = 43, [3] = 45, [4] = 90}
     local a = {12, 43, 45, 90}  --这个下标是从1开始的
     print(a[1]);  --与第三种对比较, 数字的时候必须按规矩来
   --第五种 table中的table
     local a = {
        {x = 1, y = 2},
        {x = 3, y = 10}
       }
      print(type(a));      --table
      print(type(a[1]));   --table
      print(a[1].x);       --1
     --[[
         因此，a的第一个元素就是{x = 1, y = 2}，调用a的第一个元素的方式为：a[1]
         由于a[1]又是一个table，所以，再次调用table的x下标的值：a[1].x
     --]]
   ```

   ​