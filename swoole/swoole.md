# swoole

## 常用的命令

```html
查看是否启动了swoole
netstat -anp | grep 9501
查看进程数
ps aft | grep tcp.php
```

## WebSocket服务

###基本信息

* WebSocket协议是基于TCP的一种新的网络协议。是一种在单个TCP 连接上进行全双工通讯的协议 。
* 保持连接状态。与HTTP不同的是，Websocket 需要先创建连接，这就使得其成为一种有状态的协议，之后通信时可以省略部分状态信息。而HTTP请求可能需要在每个请求都携带状态信息（如身份认证等）。

### WebSocket特点

* 建立在TCP协议之上
* 性能开销小，通信高效
* 客户端可以与任意服务器通信
* 协议标识符 ws（类似http） wss（表示加密类似https）
* 持久化网络通信协议
* WebSocket 是独立的、创建在 TCP 上的协议。 
* Websocket 通过 hTTP/1.1 协议的101状态码进行握手。 



