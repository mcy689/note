# lua

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

   * function

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

   * 线程跟协程的区别：线程可以同时多个运行，而协程任意时刻只能运行一个，并且处于运行状态的协程只有被挂起（suspend）时才会暂停。

   * userdata 是一种用户自定义数据，用于表示一种由应用程序或 C/C++ 语言库所创建的类型，可以将任意 C/C++ 的任意数据类型的数据（通常是 struct 和 指针）存储到 Lua 变量中调用。

5. Lua变量

   * 变量在使用前, 必须在代码中进行声明, 即创建该变量. 编译程序执行代码之前编译器需要知道如何给语句变量开辟存储区, 用于存储变量的值.

   * Lua 变量有三种类型: 全局变量、局部变量、表中的域

     * lua中的变量全是全局变量， 那怕是语句块或是函数里， 除非用`local` 显式声明为局部变量
     * 局部变量的作用域为从声明位置开始到所在语句块结束

   * 变量的默认值为nil

     ```lua
     function joke()
     	c = 5 --全局变量
     	local d = 6 -- 局部变量
     end
     joke()
     print(c,d) -- 5 nil

     do
     	local a=6 --局部变量
     	b = 6 --全局变量
     	print(a,b) -- 6 6
     end
     print(a,b) --nil 6
     ```

6. 赋值语句

   * Lua可以对多个变量同时赋值, 变量列表和值列表的各个元素用逗号分开, 赋值语句右边的值会依次赋给左边的变量.

     ```lua
     x = 6
     a, b =  10, 2*x 
     print(a,b)
     ```

   * 遇到赋值语句Lua会先计算右边所有的值然后再执行赋值操作, 所以我们可以这样进行交换变量的值

     ```lua
     tab = {key1='val1',key2='val2'}
     tab.key1, tab.key2 = tab.key2, tab.key1
     print(type(tab))   -- table
     for k,v in pairs(tab) do
     	print(k..' = '..v);  --key1 = val2 key2 = val1
     end
     ```

   * 当变量个数和值的个数不一致时, lua会一直以变量个数为基础采取以下策略

     ```lua
     --a. 变量个数 > 值的个数      按变量个数不足nil
     --b. 变量个数 < 值的个数      多余的值会被忽略
     a,b,c = 1,2
     print(a,b,c) --1  2  nil

     a,b = a+1,b+1,b+2
     print(a,b)  --2  3
     ```

   * 多值赋值经常用来交换变量, 或将函数调用返回给变量

     ```lua
     --下面的这种用法类似于php中的  list($a,$b) = fun()
     a, b = f()
     --[[  
     	f() 返回两个值, 第一个赋给a, 第二个赋给b
     --]]
     ```

7. 循环

   * 控制结构的条件表达式结果可以是任何值, Lua认为false和nil为假, true和非nil为真

     ```lua
     if(0)
     then
     	print('0为true') --输出这个
     else
     	print('false真是false');
     end
     ```

8. 函数

   * 函数的声明

     ```lua
     --示例一
       function max(num1,num2)
           if(num1 > num2) then
               result = num1;
           else 
               result = num2;
           end
           return result
       end
       print('两者比较最大值为',max(2,3))
     --示例二
     	local myprint = function(param) --这是个疑问,将这里的变量变成一个局部变量但是在函数里还是能调用
     	print('这是打印函数 ##',param,'##')
         end
         function add(num1,num2)
             result = num1 + num2
             myprint(result)
         end
         add(2,3)
     ```

   * Lua函数可以返回多个结果值 , 在return后列出要返回的值得列表即可返回多值

     ```lua
     --这个函数返回 表中最大值和最大值的索引
     function maximum(a)
     	local mi = 1
     	local m = a[mi]
     	for i,val in pairs(a) do
     		if val > m then
     			mi = i
     			m = val
     		end
     	end
     	return m,mi
     end
     print(maximum({8,10,23,12,5}));
     ```

   * lua函数可以接受可变数目的参数, 函数参数列表中使用三点( ... )表示函数有可变的参数 , lua将函数的参数放在一个叫`arg的表` 中, `#arg` 表示传入参数的个数

     ```lua
     function average(...)
     	result = 0
     	local args={...}
     	for i,v in ipairs(args) do
     		result = result + v
     	end
     	print('总共传入'..#args..'个数');
     	return result /#args
     end
     print(average(10,23,19,30));

     总共传入4个数
     20.5
     ```

9. lua迭代器

   1. 泛型for迭代器

      泛型for在自己内部保存迭代函数, 实际上它保存三个值; 迭代函数, 状态常量, 控制变量 .

      ```lua
      for k,v in pairs(t) do
        print(k,v)
       end

      k,v 为变量列表, pair(t)为表达列表
      ```

   2. 无状态的迭代器

      迭代函数都是用两个变量 ( 状态常量和控制变量 ) 的值作为参数被调用

      ```lua
      function square(iteratorMaxCount,currentNumber)
      	if currentNumber < iteratorMaxCount
      	then
      		currentNumber = currentNumber + 1
      	return currentNumber * currentNumber ,currentNumber
      	end
      end

      for i,n in square,3,0 do
      	print(i,n)
      end
      ```

      迭代的状态包括被遍历的表( 循环过程中不会改变状态常量 ) 和当前的索引下标 ( 控制变量 ) 

      ```lua
      function iter(a,i)
      	i = i + 1
      	local v = a[i]
      	if v then 
      		return i,v
      	end
      end
      test = {'top','down'}
      for k,n in iter,test,0 do
      	print(k,n)
      end

      当lua函数调用 
      ```

      ​

10. lua中的模块

   1. Lua的模块是由变量, 函数等已知元素组成的table

      ```lua
      --module.lua 文件
          module = {}
          --这是声明常量
          module.constant = '这是一个常量'
          --这是一个公有函数
          function module.func1()
              io.write('这是一个公有函数!\n')
          end
          --私有函数
          local function func2()
              print('这是一个私有函数!')
          end
          --调用私有函数
          function module.func3()
              func2()
          end
      	return module
      --引用文件modul.lua
          b = require('./module')
          print(b.constant)
          b.func3()

      ```

11. Lua元表

    * Lua提供元表( Metatable ), 可以对两个table进行操作

    * __元方法__ 当Lua试图对两个表进行相加时, 先检查两者之一是否有元素, 之后检查是否有一个叫`__add` 的字段, 若找到, 则调用对应的值. `__add` 等即时字段, 其对应的值就是__元方法__

      ```lua
      示例
      mytable = setmetatable({key1 = value1},{
          __index = function(mytable,key)
            if( key == 'key2') then
              return 'metatablevalue'
             else
              return nil
              end
            end
        })
      print(mytabl.key1,mytabl.key2)

      如果 __index包含一个函数的话, Lua就会调用那个函数, table和键名会作为参数传递给函数
      ```

    * Lua查找一个表元素时的规则

      1. 在表中查找, 如果找到, 返回该元素, 找不到则继续
      2. 判断该表是否元表, 如果没有元表, 返回nil, 有元表则继续
      3. 判断元素有没有` __index`方法, 如果`__index` 方法为nil, 则返回nil, 如果`__index `  方法是一个表, 则重复, 如果`__index` 方法是个函数, 则返回该函数的返回值

    * `__newindex` 元方法

      `__newindex`  元方法用来对表更新, `__index` 则用来对表访问

      当你给表的一个缺少的索引赋值, 解释器就会查找 `__newindex`  元方法; 如果存在则调用这个函数而不进行赋值

      ```lua
      mymetatable = {}
      mytable = setmetatable({key1 = 'value1'},{__newindex = mymetatable})
      ```

12. 协同程序

    * 线程与协同程序的主要区别在于, 一个具有多个线程的程序可以同时运行几个线程, 而协同程序却需要彼此协作的运行

    * 在任一指定时刻只有一个协同程序在运行, 并且这个正在运行的协同程序只有在明确的被要求挂起的时候才会被挂起

      ​