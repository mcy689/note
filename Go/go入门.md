## go 圣经

## 函数



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

6. 一个基于无缓存 channel 的发送操作讲导致发送者 goroutine 阻塞，直到另一个goroutine 在相同的channeles 上执行接收操作，当发送的值通过 channels 成功传输之后，两个 goroutine 可以继续执行后面的语句。反之亦然。

7. 单方向的 channel，这种限制将在编译期检测。

   ```go
   func ping(chan <- string){}	//表示一个只发送的channel；
   func pong(<- chan string){} //表示一个只发送的channel；
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