## 声明和初始化

1. map 类型是引用类型，零值为 `nil` 。
2. 禁止对 map 元素取址的原因是 map 可能随着元素数量的增长而重新分配更大的内存空间，从而可能导致之前的地址无效。

```go
map[KeyType]ValueType

//声明
  ages1 := make(map[string]int) 
  // ages1 := map[string]int{} 这种声明方式和上面的是等价的。

//字面量的声明
  ages2 := map[string]int{
    "alice":31,
    "charlie":34,
  }
```

## 注意

下面这种写法，m 的值是一个 nil；它没有指向一个已初始化的 map。读取一个值为 nil 的 map 就像读物一个空的 map 一样，但如果尝试像一个值为 nil 的 map 写入数据时，就会产生运行时 panic。

```go
//错误
  var m map[string]int
  m["alice"] = 20

//正确做法
  m = make(map[string]int)
```

## 使用 map

```go
//删除元素，不会返回任何值，并且如果指定的键不存在时，它将什么也不做。
  delete(ages,"alice")

//获取
  m := make(map[string]int)
	m["route"] = 66
	if i, ok := m["route"]; ok {
		fmt.Println(i)
	}

//仅仅是检查
  _,ok := m["route"]

//当键不存在时，map 检索会产生零值
  m := make(map[string]bool)
	if !m["route"] {
		fmt.Println("not found")
	}
```

## 并发

1. sync 包中提供了并发安全的 map。
2. 通过下面方法来实现并发安全。

```go
var counter = struct {
  sync.RWMutex
  m map[string]int
}{m: make(map[string]int)}
counter.RLock()
n := counter.m["some_key"]
counter.RUnlock()
fmt.Println(n)
```

## 迭代顺序

Map的迭代顺序是不确定的，并且不同的哈希函数实现可能导致不同的遍历顺序。在实践中，遍历的顺序是随机的，每一次遍历的顺序都不相同。这是故意的，每次都使用随机的遍历顺序可以强制要求程序不会依赖具体的哈希函数实现。如果要按顺序遍历key/value对，我们必须显式地对key进行排序，可以使用sort包的Strings函数对字符串slice进行排序。下面是常见的处理方式：

```go
package main

import (
  "fmt"
  "sort"
)

func main(){
  ages := map[string]int{
    "alice":20,
    "charlie":34,
	}
//预先分配指定容量
  names := make([]string, 0, len(ages))
  for name := range ages {
    names = append(names,name)
  }
  sort.Strings(names)
  for _, name := range names {
    fmt.Printf("%s\t%d\n", name, ages[name])
  }
}
```

