# lua

## 注释

```lua
-- 两个减号就是单行注释
--[[  这样就是多行注释 --]]
```

## 数据类型

```lua
print(type("hello world"))  --string
print(type(print))			--funtion
print(type(true))			--boolean
print(type(300))			--number
print(type(nil))			--nil
-- table 类型
> tbl={}
> print(type(tbl))			--table
```

### nil

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

### boolean

```lua
--Lua 把 false 和 nil 看作是"假"，其他的都为"真":
if false or nil then
	print('至少有一个是true');
else
	print('false 和 nil 都为 false!');
end
```

### string

```lua
--声明
    string1 = 'hello'
    string2 = "hello lua"
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

1. Lua 的字符串是不可改变的值，不能像在 c 语言中那样直接修改字符串的某个字符，而是根据修改要求来创建一个新的字符串。
2. Lua 的字符串不能通过下标来访问字符串的某个字符。

### number

在对一个数字字符串上进行算术操作时，Lua 会尝试将这个数字字符串转成一个数字。 

### function

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

--Lua函数可以返回多个结果值 , 在return后列出要返回的值得列表即可返回多值
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

--lua函数可以接受可变数目的参数, 函数参数列表中使用三点( ... )表示函数有可变的参数 , lua将函数的参数放在一个叫`arg的表` 中, `#arg` 表示传入参数的个数
function average(...)
	result = 0
	local args={...}
	for i,v in ipairs(args) do
		result = result + v
	end
	print('总共传入'..#args..'个数');
	return result /#args
end
print(average(10,23,19,30));	--20.5
```

### table

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

### userdata 

是一种用户自定义数据，用于表示一种由应用程序或 C/C++ 语言库所创建的类型，可以将任意 C/C++ 的任意数据类型的数据（通常是 struct 和 指针）存储到 Lua 变量中调用。

## 表达式

```lua
--算术
print(1+3)		-- 3	加
print(1-3)		-- -2	减
print(1*2)		-- 2	乘
print(5/20)		-- 0.25	除
print(2^3)		-- 8	指数
print(print(57%50))	-- 7 取模

--关系运算
print(1 < 2)			--true		小于
--[[
	> 大于
	<= 小于等于
	>= 大于等于
--]]
print(1 == 2)			--false		等于
print(1 ~= 2)			--true		不等于

--逻辑运算
--[[
	and	逻辑与
	or  逻辑或
	not	逻辑非
--]]
local c = nil
local d = 0
local e = 100
print(c and d)  -->打印 nil
print(c and e)  -->打印 nil
print(d and e)  -->打印 100
print(c or d)   -->打印 0
print(c or e)   -->打印 100
print(not c)    -->打印 true
print(not d)    -->打印 false
```

## 变量

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

1. Lua 变量有三种类型: 全局变量、局部变量、表中的域。

2. 变量的默认值为nil。

### 全局变量

1. Lua 中的变量全是全局变量， 那怕是语句块或是函数里， 除非用`local` 显式声明为局部变量

2. 全局变量不需要声明，给一个变量赋值后创建了这个全局变量，访问一个没有初始化的全局变量也不会出错，只不过得到的结果是`nil`，这样变量就好像从没被使用过一样。换句话说, 当且仅当一个变量不等于`nil`时，这个变量即存在。

   ```lua
   > print(a)
   nil
   >
   ```

### 局部变量

1. 局部变量的作用域为从声明位置开始到所在语句块结束。

## 赋值

1. Lua可以对多个变量同时赋值, 变量列表和值列表的各个元素用逗号分开, 赋值语句右边的值会依次赋给左边的变量。

   ```lua
   x = 6
   a, b =  10, 2*x 
   print(a,b)
   ```

2. 遇到赋值语句Lua会先计算右边所有的值然后再执行赋值操作, 所以我们可以这样进行交换变量的值。

   ```lua
   tab = {key1='val1',key2='val2'}
   tab.key1, tab.key2 = tab.key2, tab.key1
   print(type(tab))   -- table
   for k,v in pairs(tab) do
   	print(k..' = '..v);  --key1 = val2 key2 = val1
   end
   ```

3. 当变量个数和值的个数不一致时, lua会一直以变量个数为基础采取以下策略。

   ```lua
   --a. 变量个数 > 值的个数      按变量个数不足nil
   --b. 变量个数 < 值的个数      多余的值会被忽略
   a,b,c = 1,2
   print(a,b,c) --1  2  nil
   
   a,b = a+1,b+1,b+2
   print(a,b)  --2  3
   ```

4. 多值赋值经常用来交换变量, 或将函数调用返回给变量。

   ```lua
   --下面的这种用法类似于php中的  list($a,$b) = fun()
   a, b = f()
   --[[  
   	f() 返回两个值, 第一个赋给a, 第二个赋给b
   --]]
   ```

## 控制结构

## 迭代器

1. 泛型for迭代器。

   ```lua
   --泛型for在自己内部保存迭代函数, 实际上它保存三个值; 迭代函数, 状态常量, 控制变量。
   for k,v in pairs(t) do
     print(k,v)
    end
   
   k,v 为变量列表, pair(t)为表达列表
   ```

2. 无状态的迭代器。

   ```lua
   --迭代函数都是用两个变量 ( 状态常量和控制变量 ) 的值作为参数被调用。
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
   
   --迭代的状态包括被遍历的表( 循环过程中不会改变状态常量 ) 和当前的索引下标 ( 控制变量 ) 
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
   ```

## 模块

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

## 元表

1. Lua提供元表( Metatable ), 可以对两个table进行操作。

### 元方法

当Lua试图对两个表进行相加时, 先检查两者之一是否有元素, 之后检查是否有一个叫`__add` 的字段, 若找到, 则调用对应的值. `__add` 等即时字段, 其对应的值就是__元方法__。

1. `__newindex` 元方法用来对表更新。

   当你给表的一个缺少的索引赋值, 解释器就会查找 `__newindex`  元方法; 如果存在则调用这个函数而不进行赋值。

   ```lua
   mymetatable = {}
   mytable = setmetatable({key1 = 'value1'},{__newindex = mymetatable})
   ```

2. `__index` 则用来对表访问。

   ```lua
   --如果 __index包含一个函数的话, Lua就会调用那个函数, table和键名会作为参数传递给函数
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
   ```