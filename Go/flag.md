## Arg

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



