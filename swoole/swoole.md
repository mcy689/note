# swoole

## 常用的命令

```html
查看是否启动了swoole 
	查看那个程序监听了这个端口
netstat -anp | grep 9501
查看进程数
ps aft | grep tcp.php
查看进程树 传入父进程的ID
pstree -p 1782
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

## Swoole 定时器

1. `swoole_timer_tick` 定时器
   * 定时器仅在当前进程空间内有效
   * 定时器是纯异步实现的，不能与阻塞IO的函数一起使用，否则定时器的执行时间会发生错乱
2. `swoole_timer_after`函数是一个一次性定时器，执行完成后就会销毁。此函数与`PHP`标准库提供的`sleep`函数不同，`after`是非阻塞的。而`sleep`调用后会导致当前的进程进入阻塞，将无法处理新的请求。---- [swoole官网的解释](https://wiki.swoole.com/wiki/page/319.html)

## 异步redis -- redis 服务器安装

1. redis 服务

2. hiredis 库

   ```
   去swoole官网下载
   make -j
   make install
   ldconfig   #让linux重新加载配置
   ```

3. 编译swoole 需要加入  --enable-async-redis

   ```
   make clean  #清除上一次编译产生的文件
   ./configure --with-php-config=/usr/local/php/bin/php-config --enable-async-redis
   make && make install
   ```

4. 查看是否编译成功

   ```
   1. php -m 查看是否有swoole模块
   2. php --ri swoole  查看是否支持 异步redis
      （async redis client => enabled）
   ```

5. 安装遇到的问题

   ```html
   编译安装完成以后 执行 php -m 的时候查看不到 swoole 扩展
   php -c /usr/local/php/etc/php.ini  后出现这个错误 
   	libhiredis.so.0.13: cannot open shared object file: No such file or director
   然后通过这样解决的 
   		echo '/usr/local/lib' >>/etc/ld.so.conf  
            ldconfig
   ```

## 进程

进程（Process）是计算机中的程序关于某数据集合上的一次运行活动，是系统进行资源分配和调度的基本单位，是操作系统结构的基础。在早期面向进程设计的计算机结构中，进程是程序的基本执行实体；在当代面向线程设计的计算机结构中，进程是线程的容器。程序是指令、数据及其组织形式的描述，进程是程序的实体。 

## 协程

1. 概念是操作系统能够进行运算调度的最小单位。它被包含在进程之中，是进程中的实际运作单位。一条线程指的是进程中一个单一顺序的控制流，一个进程中可以并发多个线程，每条线程并行执行不同的任务。

