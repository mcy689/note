## DArg

### 格式

```go
func Arg(i int) sting
```

### 描述信息

1. 如果请求的元素不存在，则 Arg 返回一个空字符串。
2. 返回第i＋1个非flag命令行参数的值。

### eg

```go
package main

import (
	"flag"
	"fmt"
)

func main(){
	flag.Parse()
	fmt.Println(flag.Arg(0),flag.Arg(1))
}

//command
go run flag.go  9 19 // 9 19
```

## Args

### 格式

```go
func Args() []string
```

### 描述信息

1. 返回非flag命令行参数集合。

### eg

```go
package main

import (
	"flag"
	"fmt"
)

func main(){
	flag.Parse()
	fmt.Printf("This is cmmand %#v\n",flag.Args())
}

//command
This is cmmand []string{"value1", "value2"}
```

## Bool

### 格式

```go
func Bool(name string, value bool, usage string) *bool
```

### 描述信息

1. 定义一个带默认值和提示语句的 bool 类型的 flag。
2. 返回一个 bool 类型的 flag 值的地址（指针）。

### eg

```go
package main

import (
	"flag"
	"fmt"
)

var help = flag.Bool("h",false,"this help")
func main(){
	flag.Parse()
	fmt.Printf("This is bool %#v\n",*help)
}

//cmmand
go run flag.go -h  // This is bool true
go run flag.go		// This is bool false
```

## BoolVar

### 格式

```go
func BoolVar(p *bool, name string, value bool, usage string)
```

### 描述信息

1. BoolVar 使用指定的名称，默认值和用法字符串定义一个bool标志。
2. 参数 p 指向一个bool变量，用于存储标志的值。

### eg

```go
package main

import (
	"flag"
	"fmt"
)

var help bool

func init(){
	flag.BoolVar(&help,"h",false,"this is help")
}

func main(){
	flag.Parse()
	fmt.Printf("this is %#v\n",help)
}

//cmmand 
go run flag.go -h //this is true
go run flag.go 		//this is false
```

## Duration

### 格式

```go
func Duration(name string, value time.Duration, usage string) *time.Duration
```

### 描述信息

1. 定义一个带默认值和提示语句的 Duration 类型 flag，返回 flag 对应值的地址。
2. 返回值是存储标记值的 `time.Duration` 变量的地址。该标志接受 `time.ParseDuration` 可接受的值。

### eg

```go
package main

import (
	"flag"
	"fmt"
	"time"
)
var durationTime *time.Duration

func init(){
	durationTime = flag.Duration("t", 10000, "need setting time eg:1000ms")
}

func main() {
	flag.Parse()
	fmt.Println(*durationTime)
}
// cmmand 
go run flag.go -t 1000ms  // 1s
```

## DurationVar

### 格式

```go
func DurationVar(p *time.Duration, name string, value time.Duration, usage string)
```

### eg

```go
package main

import (
	"flag"
	"fmt"
	"time"
)
var durationTime time.Duration

func init(){
	flag.DurationVar(&durationTime,"t", 10000, "need setting time eg:1000ms")
}

func main() {
	flag.Parse()
	fmt.Println(durationTime)
}

//command
go run flag.go -t 1000ms  //	1s
```

## NArg

### 格式

```go
func NArg() int
```

### 描述信息

1. NFlag 返回已设置的命令行 flag 的数量。

### eg

```go

```

