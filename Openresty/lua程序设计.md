# 程序设计

## Lua 关键字

```lua
and		break		do		else		elseif
end		false		for		function		if
in		local		nil		not		or
repeat		return		then		true		until
while
```

**Lua 是区分大小写的，`and` 是保留字，但`And`和`AND`是不同的标识符**

## 解释器程序

这项特征主要是为了方便在系统中将Lua作为一种脚本解释器来使用。

```lua
--在代码文件的第一行，根据lua解释器的路径
#!/usr/bin/lua
```

## 深入函数

### 闭合函数(closure)

```lua
#!/usr/bin/lua
function newcounter()
    local i = 0;	--非局部的变量
    return function ()
        i = i + 1
        return i
    end
end

c1 = newcounter()
print(c1())		-- 1
print(c1())		-- 2
print(c1())		-- 3
```

### 非全局的函数

1. Lua 是将每个程序块（chunk）作为一个函数来处理的，所以在一个程序块中声明的函数就是局部函数，这些局部函数值在该程序块中可见。

   ```lua
   local f = function(参数)
       <函数体>
   end
   local g = function(参数)
       f()  --在这里可见
   end
   ```

2. 定义递归局部函数

   * **错误示例**

     ```lua
     --当Lua编译到函数体中调用fact(n-1)的地方时，由于局部的fact尚未定义完毕，因此这句表达式其实是调用了一个全局的fact。
     local fact = function(n)
         if n == 0 then
             return 1
         else
             return n*fact(n-1)
         end
     end
     ```

   * **正确示例**

     ```lua
     local fact
     fact = function(n)
         if n == 0 then
             return 1
         else
             return n*fact(n-1)
         end
     end
     ```

## 迭代器

### 迭代器与 `closure`

1. 所谓"迭代器"就是一种可以遍历一种集合中所有元素的机制。
2. 每个迭代器都需要在每次成功调用之间保持一些状态，这样才能知道它所在的位置及如何步进到下一个位置。`closure` 对于这类任务提供了极佳的支持，一个`closure` 就是一种可以访问其外部嵌套环境中的局部变量的函数。

```lua
-- 迭代器
function values(t)
    local i = 0
    return function()
        i = i + 1
        return t[i]
    end
end

-- while 循环
t = {10,20,30}
iter =values(t)
while true do
    local element = iter()
    if element == nil then
        break
    end
    print(element)
end

-- 泛型for
t = {10,20,30}
for element in values(t) do
    print(element)
end
```

### 无状态的迭代器

```lua
a = {"one","two","three"}
for i,v in ipairs(a) do
    print(k,v)
end
```

## 协同程序

### 基础

1. 当一个协同程序 A 唤醒另一个协同程序 B 时，协同程序 A 就处于一个特殊状态，既不是挂起状态（无法继续 A 的执行），也不是运行状态（是 B 在运行）。所以将这时的状态称为 `normal`。

```lua
-- 函数 create 用于创建新的协同程序，create 会返回一个 trread 类型的值。
co = coroutine.create(function()
    print("hi")
end)

-- 一个协同程序可以处于4种不同的状态；挂起(susoended)、运行(running)、死亡(dead)和正常(normal)
print(coroutine.status(co))		-- suspended

-- coroutine.resume 用于启动或者再次启动一个协同程序的执行，并将其状态由挂起改为运行
coroutine.resume(co)			-- hi
print(coroutine.status(co))		-- dead

--函数 yield ，该函数可以让一个运行中的协同程序挂起，而之后可以在恢复它的运行。
co = coroutine.create(function()
    for i=1, 10 do
        print("co",i)
        coroutine.yield()
    end
end)
coroutine.resume(co)			--1
print(coroutine.status(co))		--suspended
coroutine.resume(co)			--2
coroutine.resume(co)			--3

--传参数
co = coroutine.create(function(a, b, c)
    print(a, b ,c)
end)
print(coroutine.resume(co,20,30,40));
```

## 弱引用 table

```lua
-- 在本例中，第二句赋值 key = {} 会覆盖第一个key。当收集器运行时，由于没有其他地方在引用第一个 key，因此第一个 key 就被回收了，并且 table中的相应条目也被删除了。至于第二个key，变量key仍引用它，因此它没有被回收
a = {}
b = {__mode = "k"}
setmetatable(a,b)       -- 现在'a'的key就是弱引用
key = {}                -- 创建第一个key
a[key] = 1
key = {}                -- 创建第二个key
a[key] = 2
collectgarbage()        -- 强制进行一次垃圾收集
for k, v in pairs(a) do
    print(v)
end
```