## go 圣经

## 函数

1. 函数声明

   ```go
   func add(a int,b int) int{
       return a + b
   }
   //等价
   func add(a ,b int) int{
       return a + b
   }
   ```

2. 多返回值。

   ```go
   package main
   import "fmt"
   func swop(a int, b int) (int ,int) {
   	return  b,a
   }
   func main(){
   	fmt.Println(swop(1,2))
   }
   ```

### 函数值

1. 函数像其它值一样，拥有类型，可以被赋值给其他变量，传递个函数，从函数返回。对函数值的调用类似函数调用。

   ```go
   package main
   import "fmt"
   func add(a int, b int) int {
   	return a + b
   }
   func main(){
   	f := add
   	fmt.Println(f(1,2));
   }
   ```

2. 函数类型的零值是 nil。调用值为 nil的函数值会引起 panic 错误。

   ```go
   var f func(int) int
   f(3) //此处f的值为nil，会引起panic错误。
   ```

3. 函数值可以与 nil 比较。

   ```go
   var f func(int) int
   if f != nil{
       f(3)
   }
   ```

4. 函数值之间不可比较，也不能用函数值作为 map 的 key。

### 可变参数

```go
package  main
import "fmt"
func sum(vals ... int)int{
	var total int;
	for _,item := range vals{
		total += item
	}
	return total
}
func main(){
	fmt.Println(sum(1,2,3,4,4,5))
    var ab = []int{1,3,3,4,5}
	fmt.Println(sum(ab ...)) //原始参数已经是切片类型调用
}
```

### deferred 函数

1. 应该场景：defer语句经常被用于处理成对的操作，如打开、关闭、连接、断开连接、加锁、释放锁。通过defer机制，不论函数逻辑多复杂，都能保证在任何执行路径下，资源被释放。释放资源的defer应该直接跟在请求资源的语句后。

2. 函数返回的过程：先给返回值赋值，然后调用defer 表达式。最后才是返回到调用函数中。

   ```go
   package  main
   import "fmt"
   func test() (result int){
   	defer func(){
   		result ++
   	}()
   	return 0
   }
   func test2() (r int) {
        t := 5
        defer func() {
          t = t + 5
        }()
        return t
   }
   func test3() (r int) {
       defer func(r int) {
             r = r + 5
       }(r)
       return 1
   }
   func main(){
   	fmt.Println(test()) //1
       fmt.Println(test2()) //5
       fmt.Println(test3()) //1
   }
   ```


### 异常以及捕获

```go
package main
import "fmt"
func main(){
	defer func(){
		if p := recover(); p != nil {
			fmt.Println(p)
		}
	}()
	panic("test error")
}
```

## channel

1. Channel 是 goroutine 之间的通信机制。它可以让一个 goroutine 通过它给另一个grouting 发送信息。

   ```go
   ch := make(chan string)
   ```

2. 当复制一个 channel 或者用于函数参数传递时，只是拷贝一个 channel 引用。

3. 发送和接收连个操作都使用 `<-` 运算符。在发送语句中，`<-` 运算符分割 channel 和要发送的值。在接收语句中，`<-` 运算符写在 channel 对象之前。在接收语句中，`<-` 运算符写在 channel 对象之前。一个不实用接收结果的接收操作也是合法的。

   ```go
   ch <- x		//send
   x = <- ch 	//receive
   <-ch 		//receive
   ```

4. Channel 支持 close 操作，用于关闭 channel，关闭后对基于该 channel 的任何发送操作都将导致 panic 异常。对于一个已经被 close 过的 channel 进行接收操作依然可以接收到之前已经成功发送的数据；如果 channel 中已经没有数据的话讲产生一个零值的数据。

   ```go
   close(ch)
   ```

5. 最简单方式调用 make 函数创建的是一个无缓存的 channel，但是也可以指定第二个整形参数，该参数对应 channel 的容量，如果 channel 的容量大于零，那么该 channel 就是带缓存的 channel。

   ```go
   ch := make(chan int)	//unbuffered
   ch := make(chan int,0)	//unbuffered
   ch := make(chan int,3)  //buffered
   ```

6. 一个基于无缓存 channel 的发送操作将导致发送者 goroutine 阻塞，直到另一个goroutine 在相同的channeles 上执行接收操作，当发送的值通过 channels 成功传输之后，两个 goroutine 可以继续执行后面的语句。反之亦然。

7. 单方向的 channel，这种限制将在编译期检测。

   ```go
   func ping(chan <- string){}	//表示一个只发送的channel；
   func pong(<- chan string){} //表示一个只接收的channel；
   ```

8. 因为关闭操作只用于断言不再向 channel 发送新的数据，所以只有在发送者所在的 goroutine 才会调用 close 函数，因此对一个只接收的 channel 调用 close 将是一个编译错误。

### 缓存 channel

1. 带缓存的 channel 内部持有一个元素队列。队列的最大容量是在调用 make 函数创建 channel 通过第二个参数指定的。

   ```go
   ch := make(chan string, 3)
   ```

2. 向缓存 channel 的发送操作就是向内部缓存队列的尾部插入元素，接收操作则是从队列的头部删除元素。如果内部缓存队列是满的，那么发送操作将阻塞直到因另一个 goroutine 执行接收操作而释放了新的队列空间。相反，如果 channel 是空的，接收操作将阻塞直到有另一个 goroutine 执行发送操作而向队列插入元素。

3. Channel 的缓存队列解藕了接收和发送的 goroutine。

4. 使用内置 `cap` 函数获取内部可以缓存的容量。使用内置的 `len` 函数，将返回内部缓存队列中有效元素的个数。

   ```go
   ch := make(chan string,3)
   fmt.Println(cap(ch))		// 3
   ch <- "A"
   fmt.Println(len(ch)) 		// 1
   ```

5. 示例。

   ```go
   func mirroredQuery() string {
   	responses := make(chan string,3)
   	go func(){responses <- request("asia.gopl.io")}()
   	go func(){responses <- request("asia.gopl.io")}()
   	go func(){responses <- request("asia.gopl.io")}()
   	return <- responses
   }
   func request( hostname string) string {
   	return /*....*/
   }
   ```

   该程序并发的向三个镜像站点发送请求，三个镜像站点分散在不同的地理位置。它们分别收到的响应发送到带缓存 channel 。最后接收者之接收到一个收到的响应。

   如果使用了无缓存的 channel ，那么两个慢的 goroutine 将会因为没有人接收而被永远卡住。这种情况称为 goroutine 泄漏。


### select

1. Go 的select语句是一种仅能用于channl发送和接收消息的专用语句。

2. Go 的**channel 选择器** 让你可以同时等待多个通道操作。

3. 这些 `case` 中的表达式都必须与 channel 的操作有关，也就是 Channel 的读写操作。

   ```go
   package main
   
   import "time"
   import "fmt"
   
   func main() {
       // 在我们的例子中，我们将从两个通道中选择。
       c1 := make(chan string)
       c2 := make(chan string)
       
       // 各个通道将在若干时间后接收一个值，这个用来模拟例如
       // 并行的 Go 协程中阻塞的 RPC 操作
       go func() {
           time.Sleep(time.Second * 1)
           c1 <- "one"
       }()
       go func() {
           time.Sleep(time.Second * 2)
           c2 <- "two"
       }()
   
       // 我们使用 `select` 关键字来同时等待这两个值，并打
       // 印各自接收到的值。
       for i := 0; i < 2; i++ {
           select {
           case msg1 := <-c1:
               fmt.Println("received", msg1)
           case msg2 := <-c2:
               fmt.Println("received", msg2)
           }
       }
   }
   ```

## 基于共享变量的并发

1. 一个函数在线性程序中可以正确地工作，如果在并发的情况下，这个函数依然可以正确地工作的话，那么这个函数是并发安全的。

2. **数据竞争** 会在两个以上的 goroutine 并发访问相同的变量并且至少其中一个为写操作是发生。

3. 不要使用共享数据来通信，使用通信来共享数据。

4. 一个提供对一个指定的变量通过 channel 来请求的 goroutine 叫做这个变量的监控。

   ```go
   package main
   
   var deposits = make(chan int)
   var balances = make(chan int)
   
   func Deposit(amount int){
   	deposits <- amount
   }
   
   func Balance()int{
   	return <-balances
   }
   
   func teller(){
   	var balance int
   	for{
   		select {
   		case amount := <- deposits:
   			balance += amount
   		case balances <- balance:
   		}
   	}
   }
   ```

### gorutines 和线程

#### 动态栈

1. 每一个 os 线程都有一个固定大小的内存块（一般会是2mb）来做栈，这个栈会用来存储当前正在被调用或者挂起的函数的内部变量。

