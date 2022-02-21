## 概览

`socket`  数据形式有两种：数据报和字节流。

1. 以数据报为数据形式意味着数据接收方的 socket 接口程序可以意识到数据的边界并会对它们进行切分，这样就省去了接受方的应用程序寻找数据边界和切分数据的工作量。
2. 以字节流为数据形式的数据传输实际上传输的是一个字节接着一个字节的串，我们可以把它想象成一个很长的字节数组。一般情况下，字节流并不能体现出那些字节属于哪个数据包。因此，socket 接口程序是无法从中分离出独立的数据包的，这一工作只能由应用程序去完成。

<img src="./image/socket.png" alt="socket" style="zoom:50%;" />

## net.Listen

```go
func Listen(network, address string) (Listener, error)
```

用于获取监听器，他接受两个 `string` 类型的参数。第一个参数的含义是以何种协议监听给定的地址。

net.Listen 函数的第一个参数的值必须是 tcp、tcp4、tcp6、unix 和 unixpacket 中的一个。

address 它的格式是 `host:port`

```go
listener, err := net.Listen("tcp", "127.0.0.1:8888")
```

`net.Listen` 函数被调用之后，会返回两个结果值：第一个结果值是 `net.Listener` 类型的，它代表的就是接听器；第二个结果值是一个 `error` 类型的值，记得一定要先判断该值是否为`nil`。在进行必要的检查之后，就可以开始等待客户端的连接请求了。

```go
conn, err := listener.Accept()
```

当调用监听器的 `Accept` 方法时，流程会被阻塞，直到某个客户端程序与当前程序建立 TCP 连接。Accept 方法会返回两个结果值：1. 代表了当前 TCP 连接的 `net.Conn` 类型值，2. 结果值依然是一个 `error` 类型的值。

## net.Dial

