```go
package main

import (
	"fmt"
	"runtime"
	"sync"
	"time"
)

func main() {
	//runtime.GOOS
	if runtime.GOOS == "windows" {
		fmt.Println("windows")
	} else if runtime.GOOS == "darwin" {
		fmt.Println("darwin")
	} else if runtime.GOOS == "linux" {
		fmt.Println("linux")
	} else {
		fmt.Println("other")
	}
	// 返回 go 的根目录
	fmt.Println(runtime.GOROOT())

	// 返回 go 的版本信息
	fmt.Println(runtime.Version())

	//GC 执行一次垃圾回收
	pipe := &sync.Pool{
		New: func() interface{} {
			return "hello"
		},
	}
	fmt.Println(pipe.Get())
	pipe.Put("world")
	runtime.GC()            //作用是GC执行一次垃圾回收
	fmt.Println(pipe.Get()) //hello，本来是 world

	// 调用runtime.goExit()将立即终止当前goroutine执行，调度器确保所有已注册defer延迟调度被执行。
	go func() {
		defer fmt.Println("defer B")
		func() {
			defer fmt.Println("defer A")
			runtime.Goexit()
			fmt.Println("C")
		}()
	}()

	//runtime.Gosched() 使当前go程放弃处理器，以让其它go程运行。它不会挂起当前go程，因此当前go程未来会恢复执行
	go func() {
		for i := 0; i < 3; i++ {
			runtime.Gosched()
			fmt.Println("gosched A")
		}
	}()
	fmt.Println("finish")

	//NumGoroutine 返回当前存在的Go程数。
	fmt.Println(runtime.NumGoroutine())

	// NumCPU 返回本地机器的逻辑CPU个数。
	fmt.Println(runtime.NumCPU())
	
	time.Sleep(time.Second * 2)
}
```

