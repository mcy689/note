### 为了redis学了lua哈哈 2017-1-22 21:22

1. lua的注释

   ```lua
   -- 两个减号就是单行注释
   --[[  这样就是多行注释 --]]
   ```

2. lua是区分大小写的

3. 全局变量

   * 在默认的情况下,变量总是全局的,全局变量不需要声明，给一个变量赋值后即创建了这个全局变量，访问一个没有初始化的全局变量也不会出错，只不过得到的结果是：nil

4. lua数据类型

   ```lua
   --给全局变量或者 table 表里的变量赋一个 nil 值，等同于把它们删掉
   tab1 = { key1 = 'val1',key2 = 'val2','val3','val4'}
   for k,v in pairs(tab1) do
   	print(k..'-'..v)
   end

   tab1.key1 = nil
   for k,v in pairs(tab1) do
   	print(k..'-'..v)
   end
   ```

   * boolean 类型只有两个可选值：true（真） 和 false（假），Lua 把 false 和 nil 看作是"假"，其他的都为"真":

     ```lua
     --示例
     if false or nil then
     	print('至少有一个是true');
     else
     	print('false 和 nil 都为 false!');
     end
     ```

   * 字符串

     ```lua
     --声明
         string1 = '这是字符串'
         string2 = "这是字符串"
         string3 = [[
             <html>
             <head> </head>
             <body>
                 <a href="http:www.baidu.com"> 百度</a>
             </body>
             </html>
         ]]
     --字符串的连接使用
     	print('123'..'789'); --123789
     --使用 # 来计算字符串的长度，放在字符串前面
         len = 'www.runoob.com'
         print(#len);  -- 14
     ```

   * number ( 在对一个数字字符串上进行算术操作时，Lua 会尝试将这个数字字符串转成一个数字 ) 

   * table 类型

     ```lua
     --创建一个空的table
     	local tab1 = {}
     --直接初始表
     	local tab2 = {'apple','pear','orange','grape'}
     --Lua 中的表（table）其实是一个"关联数组"（associative arrays），数组的索引可以是数字或者是字符串。
     	a={}
         a['key'] = 'value'
         key = 10
         a[key] = 22
         a[key] = a[key] + 11
         for k,v in pairs(a) do 
             print(k..'---'..v)
         end
     --不同于其他语言的数组把 0 作为数组的初始索引，在 Lua 里表的默认初始索引一般以 1 开始。
         local tb1 = {'apple','pear','orange'}
         for key,val in pairs(tb1) do
             print('key',key)
         end
     --table 不会固定长度大小，有新数据添加时 table 长度会自动增长，没初始的 table 都是 nil。
       a3 = {}
       for i = 1, 10 do
           a3[i] = i
       end
       a3["key"] = "val"
       print(a3["key"])  --val
       print(a3["none"]) --nil
     ```

5. function

   ```lua
   --第一个示例 函数可以存在变量里:
       function factorial1(n)
           if n == 0 then
               return 1
           else 
               return n* factorial1(n-1) --调用自己
           end
       end
       print(factorial1(5))
       factorial2 = factorial1
       print(factorial2(5))

   --可以以匿名函数（anonymous function）的方式通过参数传递:
       function testFun(tab,fun)
           for k,v in pairs(tab) do
               print(fun(k,v))
           end
       end
       tab = {key1='val1',key2='val2'}
       testFun(tab,
           function(key,val)  --匿名函数
               return key..'='..val
           end
       )
   ```

6. 线程跟协程的区别：线程可以同时多个运行，而协程任意时刻只能运行一个，并且处于运行状态的协程只有被挂起（suspend）时才会暂停。

7. userdata 是一种用户自定义数据，用于表示一种由应用程序或 C/C++ 语言库所创建的类型，可以将任意 C/C++ 的任意数据类型的数据（通常是 struct 和 指针）存储到 Lua 变量中调用。