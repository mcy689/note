# 复制

## 概念

* 在 Redis 中，用户可以通过执行 `SLAVEOF` 命令或者设置 slaveof 选项，让一个服务器去复制（replicate）另一个服务器，被复制的服务器为主服务器（master），而对主服务器进行复制的服务器则被称为从服务器（salve）。
* 通过持久化功能, Redis 保证了即使在服务器重启的情况下也不会损失(或者少量损失)数据, 但是由于数据是存储在一台服务器上的, 如果这台服务器出现硬盘故障等问题, 也会导致数据丢失, 为了避免单点故障, 通常的做法是将数据库复制多个副本以部署在不同的服务器上, 这样即使有一台服务器出现故障, 其他服务器依然可以继续提供服务。

## 集群的必须性

* 从结构上, 单个 Redis 服务器会发生单点故障, 同时一台服务器需要承受所有的请求负载, 这就需要为数据生成多个副本并分配在不同的服务器上。

* 从容量上, 单个 Redis 服务器的内存非常容易成为存储瓶颈, 所以需要进行数据分片。

## 配置

* 在复制的概念中, 数据库分为两类, 一类是主数据库(master), 另一类是从数据库(slave). 主数据库可以进行读写操作, 当写操作导致数据变化时会自动将数据同步给从数据库. 而从数据库一般是只读的, 并接受主数据库同步过来的数据. 一个主数据库可以拥有多个从数据库, 而一个从数据库只能拥有一个主数据库

* Redis中使用复制功能非常容易, 只需要在从数据库的配置文件中加入 `slaveof 数据库地址主数据库端口` , 主数据库无需进行任何配置

  ```html
  1. redis-server   先开启一个redis实例, 这个实例默认端口是6379
  2. redis-server --port 6380 --slaveof 127.0.0.1 6379
  3. redis-cli -p 6379
  4. redis-cli -p 6380
  
  使用 info replication   命令查看连接信息
    主的连接信息
      [root@localhost ~]# redis-cli -p 6379
      127.0.0.1:6379> info replication
      # Replication
      role:master            这里表示当前连接的是主数据库
      connected_slaves:1     连接到当前Redis数据库的个数
      slave0:ip=127.0.0.1,port=6380,state=online,offset=211,lag=0
      master_repl_offset:211
      repl_backlog_active:1
      repl_backlog_size:1048576
      repl_backlog_first_byte_offset:2
      repl_backlog_histlen:210
    从的连接信息
     	# Replication
      role:slave
      master_host:127.0.0.1
      master_port:6379
      master_link_status:up
      master_last_io_seconds_ago:8
      master_sync_in_progress:0
      slave_repl_offset:169
      slave_priority:100
      slave_read_only:1          
      connected_slaves:0
      master_repl_offset:0
      repl_backlog_active:0
      repl_backlog_size:1048576
      repl_backlog_first_byte_offset:0
      repl_backlog_histlen:0
  ```

* __注意__ 默认情况下从数据库是只读的, 如果直接修改从数据的数据会出现错误，也可以通过修改配置赋予从数据写入数据的权限，但是从数据库的任何修改都不会同步给任何其他数据, 并且一旦主数据库中更新了对应的数据就会覆盖从数据库中的改动, 所以通常的场景下不应该设置从数据可写, 以免导致易被忽略的潜在应用逻辑错误。

  ```html
  127.0.0.1:6380> set foo1 maslave
  (error) READONLY You can't write against a read only slave.
  
  数据库的配置
  301 slave-read-only yes  只需要将这里的yes修改为no就可以做写入操作了
  ```

* 配置多台从数据库的方法也一样, 在所有的从数据的配置文件中都加上 slaveof 参数指向同一个数据库即可

* 除了同过配置文件或命令行参数设置 slaveof 参数, 还可以在运行时使用 slaveof 命令修改，如果该数据库已经是其他主数据库的从数据库了, slaveof 命令会停止和原来数据库的同步转而和新数据库同步。

  ```html
  1. 这是配置文件中的修改
  	265 # slaveof <masterip> <masterport>
  2. 命令行修改
      slaveof 127.0.0.1 6379
        
  对于从数据库来说的命令  slaveof no one 可以使当前数据库停止接受其他数据库的同步并转换成为主数据库. 
  ```

## 复制原理

1. 当一个从数据库启动后, 会向主数据库发送sync命令, 同时主数据库接受到sync命令后会开始在后台保存快照( 即RDB持久化的过程)
2. 将保存快照期间接受收到的命令缓存起来
3. 当快照完成后, Redis会将快照文件和所有缓存的命令发送给从数据库.
4. 从数据库收到后, 会载入快照文件并执行收到的缓存的命令（__这四步称为复制初始化__）
5. 复制初始化结束后, 主数据库每当收到写命令时就会将命令同步给从数据库, 从而保证主从数据库数据一致

## 从协议角度详细介绍复制初始化的过程

1. telnet 安装

   ```html
   Linux下安装telnet
     用root用户安装
       1：查询命令
       rpm -q telnet
       rpm -qa | grep telnet
       2：安装命令
       yum install xinetd
       yum install telnet
       yum install telnet-server
   	3.可再搜索遍看是否已经写入环境中
   	rpm -qa | grep telnet 
   ```

### 步骤

1. telnet 127.0.0.1 6379

2. 作为从数据库, 要先发送ping命令确认主数据库是否可以连接  主服务器回复 `+PONG` 

3. 向主数据库发送replconf 命令说明自己的端口号

4. `sync` 命令

   ```html
   [root@localhost ~]# telnet 127.0.0.1 6379
   Trying 127.0.0.1...
   Connected to 127.0.0.1.
   Escape character is '^]'.
   ping
   +PONG
   replconf listening-port 6381
   +OK
   sync
   ```

5. 从数据库会将接收到的内容写入到硬盘上的临时文件中, 当写入完成后从数据库会用该临时文件替换 RDB 快照文件,  在同步数据的过程中从数据库并不会阻塞, 而是可以继续处理客户端发来的命令. 默认情况下, 从数据库会用同步前的数据对命令进行响应. 

   ```html
   285 slave-serve-stale-data yes  
    将参数改为no来使从数据库在同步完成前对所有命令(除了info和slaceof)都回复错误 `sync with master in progress`
   ```

## Redis 采用乐观复制的复制策略

1. __概念__ 容许在一定时间内主从数据库的内容是不同的, 但是两者的数据会最终同步

   __解释__ Redis在主从数据库之间复制数据的过程本身是异步的, 这意味着, 主数据库执行完客户端的执行结果返回给客户端, 并异步地将命令同步给从数据, 而不会等待从数据库接收到该命令后再返回给客户端, 这一特性保证了启用复制后主数据库的性能不会受到影响, __但是__ 另一方面也会产生一个主从数据库数据不一致的时间窗口, 主数据库执行了一条写命令后, 主数据库的数据已经发生了变动.

   主数据库将该命令传送给从数据库之前, 如果两个数据库之间的网络连接断开了, 此时二者之间的数据就会是不一致的. 从这个角度来看, 主数据库是无法得知某个命令最终同步给了多少个从数据库的, 不过Redis提供了两个配置项来限制只有当数据至少同步给指定数量的从数据库时, 主数据库才是可写的.

   ```html
   430 # min-slaves-to-write 3   
   	只有当3个或者3个以上的从数据库连接到主数据库时, 主数据库才是可写的,否则返回错误
   431 # min-slaves-max-lag 10s
       表示允许从数据库失去连接的时间, (即发送了replconf命令)
   ```

## 图结构

- 从数据库不仅可以接收主数据库的同步数据, 自己也可以同时作为主数据库存在, 形成如下这样的格式

  ```html
        							 主 A
  		  					从B        从C
                           从D       从E
  ```

- 读写分离与一致性

  通过复制可以实现读写分离, 以提高服务器的负载能力, 读的频率大于写, 当单机的 Redis 无法应付大量的读请求时, (尤其是较耗资源的请求, 如sort命令等) 可以通过复制功能建立多个从数据库节点, 主数据库只进行写操作, 而从数据库负责读操作, 这种一主多从的结构很适合读多写少的场景, 而当单个的主数据库不能够满足需求时, 就需要Redis3.0推出的集群功能

## 从数据库的持久化

- __原因__ 另一个相对耗时的操作时持久化, 为了提高性能, 可以通过复制功能建立一个(或若干个) 从数据库, 并在从数据库中启用持久化, 同时在主数据禁用持久化.  当从数据库崩溃重启后主数据库会自动将数据同步过来, 所以无需担心数据丢失。
- 当主数据库崩溃时
  1. 在从数据库中使用`slaveof no one` 命令将从数据库提升成主数据库继续服务。
  2. 启动之前崩溃的主数据库, 然后使用 `slaveof`命令将其设置成新的主数据库的从数据库, 即可将数据同步回来。
- __注意__  当开启复制且主数据库关闭持久化功能时, 一定不要使用进程管理工具令主数据库崩溃后自动重启, 同样当主数据所在的服务器因故关闭时没, 也要避免直接重新启动, 这是因为当主数据重新启动后, 因为没有开启持久化功能, 所以数据库中所有数据都被清空, 这时从数据依然会从主数据中接受数据, 使得从数据库也被清空, 导致从数据的持久化失去意义。

## 增量复制

- __原因__ 当主从数据连接断开后, 从数据会发送sync命令来重新进行一次完整复制操作, 这样即使断开期间数据库的变化很小(甚至没有), 也需要将数据中的所有数据重新快照并传送一次. Redis2.8版本后允许增量备份

- 增量备份是基于如下3点实现的

  1. 从数据库会存储主数据库的运行ID(run id) . 每个Redis运行实例均会拥有一个唯一的运行ID, 每当实例重启后, 就会自动生成一个新的运行ID
  2. 在复制同步阶段, 主数据库每将一个命令传送给从数据时, 都会同时把该命令存放到一个积压队列(backlog), 并记录当前积压队列中存放的命令的偏移范围
  3. 同时, 从数据库接受到主数据库传来的命令时, 会记录下该命令的偏移量.

- 当主从连接准备就绪后, 从数据库会发送一条sync命令来告诉主数据库可以开始把所有数据同步过来了, 而2.8后是通过psync命令, 执行增量备份

  1. 主数据库会判断从数据库传送来的运行ID是否和自己的运行ID相同. 这一步骤的意义在于确保从数据库之前确实是和自己同步的, 以免从数据拿到错误的数据

  2. 然后判断从数据最后同步成功的命令偏移量是否在挤压队列中, 如果在则可以执行增量复制, 并将积压队列中相应的命令发送给从数据库

     __如果 此次重连不满足增量复制的条件, 主数据库会进行一次全部同步__ 

- 积压队列的大小

  ```html
  388 # The backlog is only allocated once there is at least a slave connected.
  389 #
  390 # repl-backlog-size 1m
  
  397 # A value of 0 means to never release the backlog.
  398 #
  399 # repl-backlog-ttl 3600
  当前所有从数据库与主数据库断开连接后, 经过多久时间可以释放积压队列的内存空间. 默认时间是1小时
  ```

  挤压队列在本质是是一个固定长度的循环队列, 默认情况下积压队列的大小为1MB, 可以通过配置文件的repl-backlog-size 选项来调整, 积压队列越大, 其允许的主从数据库断线的时间就越长, 根据主从数据库之间的网络状态, 设置一个合理的积压队列很重要, 因为积压队列存储的内容是命令本身

  __所以 估算积压队列的大小只需要估计主从数据断线的时间中主数据库可能执行的命令的大小即可__

## 哨兵

- 功能

  1. 监控主数据库和从数据库是否正常运行
  2. 主数据库出现故障时自动将从数据库转换为主数据库

- 在一个一主多从的Redis系统中, 可以使用多个哨兵进行监控任务以保证系统足够稳健

- 如何启动哨兵

  ```html
  1. 新建一个sentinel.conf  的配置文件,其中写入如下信息
  	sentinel monitor mymaster 127.0.0.1 6379 1
  2. 使用命令读取新建的配置文件 redis-sentinel ./sentinel.conf
  3. 看到如下信息
  	2763:X 17 Dec 15:41:31.039 # Sentinel ID is 8c13ef425f435a1b4a01064b41082c84a4e19138
      2763:X 17 Dec 15:41:31.039 # +monitor master mymaster 127.0.0.1 6379 quorum 1
      2763:X 17 Dec 15:41:31.045 * +slave slave 127.0.0.1:6380 127.0.0.1 6380 @ mymaster
  			127.0.0.1 6379
      2763:X 17 Dec 15:41:31.050 * +slave slave 127.0.0.1:6381 127.0.0.1 6381 @ mymaster
  			127.0.0.1 6379
  4. 停止掉主数据库的服务, 哨兵哪里的返回值
  	2763:X 17 Dec 16:03:39.456 # +sdown master mymaster 127.0.0.1 6379
      2763:X 17 Dec 16:03:39.456 # +odown master mymaster 127.0.0.1 6379 #quorum 1/1
  5. 开始故障恢复
  
  2763:X 17 Dec 16:03:39.456 # +new-epoch 1
  2763:X 17 Dec 16:03:39.456 # +try-failover master mymaster 127.0.0.1 6379
  2763:X 17 Dec 16:03:39.476 # +vote-for-leader 8c13ef425f435a1b4a01064b41082c84a4e19138 1
  2763:X 17 Dec 16:03:39.476 # +elected-leader master mymaster 127.0.0.1 6379
  2763:X 17 Dec 16:03:39.476 # +failover-state-select-slave master mymaster 127.0.0.1 6379
  2763:X 17 Dec 16:03:39.544 # +selected-slave slave 127.0.0.1:6381 127.0.0.1 6381 @ mymaster 127.0.0.1 6379
  2763:X 17 Dec 16:03:39.544 * +failover-state-send-slaveof-noone slave 127.0.0.1:6381 127.0.0.1 6381 @ mymaster 127.0.0.1 6379
  2763:X 17 Dec 16:03:39.599 * +failover-state-wait-promotion slave 127.0.0.1:6381 127.0.0.1 6381 @ mymaster 127.0.0.1 6379
  2763:X 17 Dec 16:03:40.166 # +promoted-slave slave 127.0.0.1:6381 127.0.0.1 6381 @ mymaster 127.0.0.1 6379
  2763:X 17 Dec 16:03:40.166 # +failover-state-reconf-slaves master mymaster 127.0.0.1 6379
  2763:X 17 Dec 16:03:40.247 * +slave-reconf-sent slave 127.0.0.1:6380 127.0.0.1 6380 @ mymaster 127.0.0.1 6379
  2763:X 17 Dec 16:03:41.218 * +slave-reconf-inprog slave 127.0.0.1:6380 127.0.0.1 6380 @ mymaster 127.0.0.1 6379
  2763:X 17 Dec 16:03:41.218 * +slave-reconf-done slave 127.0.0.1:6380 127.0.0.1 6380 @ mymaster 127.0.0.1 6379
  2763:X 17 Dec 16:03:41.319 # +failover-end master mymaster 127.0.0.1 6379
  
  2763:X 17 Dec 16:03:41.319 # +switch-master mymaster 127.0.0.1 6379 127.0.0.1 6381
  2763:X 17 Dec 16:03:41.320 * +slave slave 127.0.0.1:6380 127.0.0.1 6380 @ mymaster 127.0.0.1 6381
  2763:X 17 Dec 16:03:41.320 * +slave slave 127.0.0.1:6379 127.0.0.1 6379 @ mymaster 127.0.0.1 6381
  2763:X 17 Dec 16:04:11.333 # +sdown slave 127.0.0.1:6379 127.0.0.1 6379 @ mymaster 127.0.0.1 6381
  
  8. 重新启动6379端口的实例
  
  2763:X 17 Dec 16:07:46.904 # -sdown slave 127.0.0.1:6379 127.0.0.1 6379 @ mymaster 127.0.0.1 6381
  2763:X 17 Dec 16:07:56.888 * +convert-to-slave slave 127.0.0.1:6379 127.0.0.1 6379 @ mymaster 127.0.0.1 6381
  
    解释:
   	  1. mymaster 表示要监控的主数据库的名字, 可以自己定义一个, 后面的参数是要监控的主数据库的
           地址和端口
        2. 启动sentinel进程, 将配置文件的路径传递给哨兵
  	  3. +slave 表示新发现了从数据库,
        4. +sdown 表示哨兵主观认为主数据库停止服务了
        5. +odown 表示哨兵客观认为主数据库停止服务了, 此时哨兵开始执行难故障恢复
        6. +try-failover 表示哨兵开始进行故障恢复
  	  7. +failover-end 表示哨兵完成故障恢复
  	  8. +switch-master 表示主数据库从6379 迁移到6380端口,
        9. 两个+slave则列出了新的主数据库的两个从数据库,6379端口和6380端口,其中6379端口就是之前
  		 的停止服务的主数据库, 可见哨兵并没有彻底清除停止服务的实例的信息, 这是因为停止服务的实
  		 例有可能会在之后的某个时间恢复服务, 这时哨兵会让其重新加入进来,所以当实例停止服务后, 		   哨兵会更新该实例的信息, 使得当其重新加入后可以按照当前信息继续对外提供服务.
  	  10. -sdown 表示实例6379已经恢复服务了
  	  11. +convert-to-slav 表示将6379端口的实例设置为6380端口实例的从数据库.
  ```

- 实现原理

  1. 配置文件

     sentinel monitor master-name ip redis-port quorum

     quorum 用来表示执行故障恢复操作前至少需要几个哨兵节点同意

  2. 一个哨兵节点可以同时监控多个`Redis`主从系统, 只需要提供多个`sentinel monitor`  配置即可

     ```html
     sentinel monitor mymaster 127.0.0.1 6379 2
     sentinel monitor othermaster 192.168.1.3 6380 4
     ```

  3. 同时多个哨兵节点也可以同时监控同一个Redis主从系统, 从而形成网状结构

  4. 配置文件中还可以定义其他监控相关的参数, 每个配置选项都包含主数据库的名字使得监控不同主数据库时可以使用不同的配置参数

     ```html
     sentinel down-after-milliseconds mymaster 6000
     sentinel down-after-milliseconds othermaster 1000
     ```

  5. 哨兵启动后, 会与要监控的主数据库建立两条连接, 这两个连接建立方式与普通的redis客户端无异

     其中一条连接用来订阅该主数据的 `_sentinel_:hello` 频道以获取其他监控该数据库的哨兵节点的信息,另外哨兵也需要定期向主数据库发送info等命令来获取主数据库本身的信息, 

  6. 和主数据库的连接建立完成后,哨兵会定时执行下面3个操作

     - 每10秒哨兵会向主数据库和从数据库发送`info`命令

     - 每2秒哨兵会向主数据库和从数据的`_sentinel_:hello`频道发送自己的信息

     - 每1秒哨兵会向主数据库, 从数据库和其他哨节点发送`ping`命令

       __注意这3个操作贯穿哨兵进程的整个生命周期__ 

## Redis安全

- 只允许指定的ip连接

  ```html
  40 # By default, if no "bind" configuration directive is specified, Redis listens
  41 # for connections from all the network interfaces available on the server.
  42 # It is possible to listen to just one or multiple selected interfaces using
  43 # the "bind" configuration directive, followed by one or more IP addresses.
  44 #
  45 # Examples:
  46 #
  47 # bind 192.168.1.100 10.0.0.1
  48 # bind 127.0.0.1 ::1
  ```

- 数据库密码

  ```html
  1. 由于Redis的性能极高, 并且输入错误密码后Redis并不会进行主动延迟,所以攻击者可以通过穷举法破解
      Redis的密码
  
    # Warning: since Redis is pretty fast an outside user can try up to
     477 # 150k passwords per second against a good box. This means that you should
     478 # use a very strong password otherwise it will be very easy to break.
     479 #
     480 # requirepass foobared     通过这里可以配置密码 
  
  2. 在从数据库的配置文件中可以配置主数据库的密码验证
  
    267 # If the master is password protected (using the "requirepass" configuration
    268 # directive below) it is possible to tell the slave to authenticate before
    269 # starting the replication synchronization process, otherwise the master will
    270 # refuse the slave request.
    271 #
    272 # masterauth <master-password>
  ```

- 命令命名

  ```html
   484 # It is possible to change the name of dangerous commands in a shared
   485 # environment. For instance the CONFIG command may be renamed into something
   486 # hard to guess so that it will still be available for internal-use tools
   487 # but not available for general clients.
   488 #
   489 # Example:
   490 #
   491 # rename-command CONFIG b840fc02d524045429941cc15f59e41cb7be6c52
   492 #
   493 # It is also possible to completely kill a command by renaming it into
   494 # an empty string:
   495 #
   496 # rename-command CONFIG ""
  ```

## 管理工具

1. Redis-cli

   > 1. 参看耗时命令日志 ( __使用`slowlog get`命令来获得当前的耗时命令日志__  )
   >
   >    当一条命令执行时间超过限制时, Redis会将该命令的执行时间等信息加入耗时命令的执行时间等信息加入耗时命令日志(slow log)以供开发者查看. 通过配置文件的slowlog-log-slower-than 参数配置,这里的单位是微秒(1000000微秒等于1秒), 通过配置文件slowlog-max-len参数来限制记录的条数

   ```html
   配置文件
   833 # The following time is expressed in microseconds, so 1000000 is equivalent
   834 # to one second. Note that a negative number disables the slow log, while
   835 # a value of zero forces the logging of every command.
   836 slowlog-log-slower-than 10000  
   837 
   838 # There is no limit to this length. Just be aware that it will consume memory.
   839 # You can reclaim memory used by the slow log with SLOWLOG RESET.
   840 slowlog-max-len 128
   执行命令
    每条日志都有4部分组成
    1. 该日志唯一ID
    2. 该命令执行的Unix时间
    3. 该命令的耗时时间, 单位是微秒
    4. 命令及其参数
   
   将slowlog-log-slower-than 参数设置为负数则会关闭耗时命令日志
   ```

2. 命令监控

   `monitor` 命令非常影响Redis的性能, 一个客户端使用monitor命令会降低Redis将近一半的负载能力. 所以monitor命令只适用于调试和纠错

   ```html
   127.0.0.1:6379> monitor
   OK
   1513506011.770865 [0 127.0.0.1:57224] "set" "keyd" "vlauemachunyu"
   启动一个redis-cli 执行monitor
   再启动一个redis-cli客户端, 执行的命令都会在上面那个客户端中打印出来
   ```

3. Rdbtools 是一个Redis的快照文件解析器, 可以根据快照文件导出json数据文件, 分析Redis中每个键的占用空间情况。

4. Redis 中的 [Info 命令](https://redis.io/commands/INFO)说明。