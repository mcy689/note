## nil

### 什么是 nil

当声明了一个变量，但还未赋值时，golang 中会自动给该变量类型默认一个零值。以下是默认的零值

```go
/*
  bool    -> false
  numbers -> 0
  string  -> ""

  pointers -> nil
  slice -> nil
  map -> nil
  channels -> nil
  functions -> nil
  interfaces -> nil
*/
//struct
  type People struct {
    Age int 
    Name string
    Friends []People
  }
  var p People //People{0,"",nil}
```

### 官方定义

```go
var nil Type // Type must be a pointer, channel, func, interface, map, or slice type
```

1. nil 是一个预先声明的标识符，代表指针、通道、func、接口、map 或者切片类型的零值。
2. nil 只能赋值给指针、channel、func、interface、map 或者slice类型的变量，否则会引发panic。

### pointer

nil 指针是一个没有指向任何值的指针。

```go
var a = (*int)(unsafe.Pointer(uintptr(0)))
print(a == nil)
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

