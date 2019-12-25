# exmple

## hello world

```lua
print("Hello World")
```

## 计算阶乘

```lua
function fact(n)
    if n == 0 then
        return 1;
    else
        return n*fact(n-1)
    end
end

print("enter a number")
local a = io.read("*number")
print(fact(a))
```

