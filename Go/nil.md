## `nil` 是一个预声明的标识符

```go
var nil Type // Type must be a pointer, channel, func, interface, map, or slice type
```

1. nil 是一个预先声明的标识符，代表指针、通道、func、接口、map 或者切片类型的零值。
2. nil 只能赋值给指针、channel、func、interface、map 或者 slice 类型的变量，否则会引发panic。

## 预声明标识符 `nil` 没有默认类型

```go
package main

import "fmt"

func main() {
  //必须在代码中提供足够的信息以便让编译器能够推断出一个类型不确定的 nil 值的期望类型。
	_ = (*int)(nil) //指针类型
	_ = []int(nil) //切片
	_ = map[int]bool(nil) //map
	_ = chan string(nil)
	_ = (func())(nil) //函数
	_ = (interface{})(nil) //接口
}
```

## nil 不是一个关键字

```go
package main

import "fmt"

func main() {
	nil := 10
	fmt.Println(nil) //10
}
```

## 不同种类的类型的nil值的尺寸很可能不相同

```go
package main

import (
	"fmt"
	"unsafe"
)

func main() {
	var p *struct{} = nil
	fmt.Println(unsafe.Sizeof(p)) //8

	var s []int = nil
	fmt.Println(unsafe.Sizeof(s)) //24

	var m map[int]bool = nil
	fmt.Println(unsafe.Sizeof(m)) //8

	var f func() = nil
	fmt.Println(unsafe.Sizeof(f)) //8

	var i interface{} = nil
	fmt.Println(unsafe.Sizeof(i)) //16
}
```

## 两个不同类型的`nil` 值可能不能相互比较

```go
package main

import (
	"fmt"
	"unsafe"
)

func main() {
// 编译不通过
	var _ = (*int)(nil) == (*bool)(nil)

// 可以比较
  type IntPtr *int
	// 类型 IntPtr 底层类型是 *int
	var _ = IntPtr(nil) == (*int)(nil)
	//任何类型都实现了 interface 类型
	var _ = interface{}(nil) == (*int)(nil)
	//一个双向通道可以隐式转换为和它的元素类型一样的单项通道类型。
	var _ = (chan int)(nil) == (chan<- int)(nil)
  var _ = (chan int)(nil) == (<-chan int)(nil)
}
```

在 go 中，映射类型、切片类型和函数类型是不支持比较类型。比较同一个不支持比较的类型的两个值（包括nil值）是非法的。

```go
var _ = ([]int)(nil) == ([]int)(nil)
var _ = (map[int]bool)(nil) == (map[int]bool)(nil)
var _ = (func())(nil) == (func())(nil)
```

但是，可以和类型不确定的`nil` 比较

```go
var _ = ([]int)(nil) == nil
var _ = (map[int]bool)(nil) == nil
var _ = (func())(nil) == nil
```

### 其他

```go
var p *int
p == nil  //true

//指针类型的值为 nil ，也能调用方法
type TestNil struct {
  Name string
}
func (e *TestNil) Error() string {
  return "a"
}
func TestFunc(t *testing.T){
  var test *TestNil
  fmt.Println(test == nil) //true
  fmt.Println(test.Error()) //a
}

//不能进行赋值操作，否则就会引起 panic
	var a int = nil //panic

//使用
type A interface{}
type B struct{}
var a *B

a == nil 	//true
a == (*B)(nil)  //true
(A)(a) == (*B)(nil) //true
(A)(a) == nil //false
```





---

> 参考：go101 

