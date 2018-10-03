# 持久化

## 概念

__持久化__ Redis 将内存中的数据状态保存到磁盘里面，避免数据意外丢失

## 持久化方式

Redis支持两种方式的持久化 (实际使用的时候是将下面的二者结合使用)

- RDB 持久化通过保存数据库中的键值对来记录数据状态。二进制的文件。
- AOF 持久化是通过保存 Redis 服务器所执行的写命令来记录数据库的状态的。
- redis允许同时开启AOF和RDB , 即保证了数据安全又使得进行备份等操作十分容易.__如果重新启动 Redis 后Redis会使用AOF文件来恢复数据, 因为AOF方式持久化可能丢失的数据更少__ 

## RDB快照

1. RDB方式的持久化是通过快照(snapshotting)完成的, 当符合一定条件时Redis会自动将内存中的所有数据生成一份副本并存储在硬盘上, 这个过程即为快照.

   - 根据配置规则进行自动快照
   - 用户自行save或者 bgsave命令
   - 执行flushall命令
   - 执行复制时

2. __根据配置规则进行自动快照__

   - 由两个参数构成: 时间窗口M和改动的键的个数N,

     参数说明:  __每当时间M内被更改的键的个数大于N时,即符合自动快照条件 __

     ```html
     202 save 900 1            #在900秒(15分钟)之后，如果至少有1个key发生变化，则dump内存快照。
     203 save 300 10           #在300秒(5分钟)之后，如果至少有10个key发生变化，则dump内存快照。
     204 save 60 10000         #在60秒(1分钟)之后，如果至少有10000个key发生变化，则dump内存快照。
     
     每条快照条件占一行, 并且以save参数开头. 同时可以存在多个条件, 条件之间是 "或" 的关系.       
     ```

3. __用户自行save或bgsave命令__ 

   * 应用场景  当进行服务重启, 手动迁移以及备份时我们也会需要手动执行快照操作

   - save命令

     当执行save命令时, Redis同步地进行快照操作, 在快照执行的过程中__会阻塞__ 所有来自客户端的请求. 当数据库中的数据比较多时, 这一过程会导致redis较长时间不响应. __避免在生产环境中使用这个命令__ 

   - bgsave命令

     `bgsave` 命令可以在后台异步地进行快照操作, 快照的同时服务器还可以继续响应来自客户端的请求. 执行`bgsave` 后Redis会立即返回ok表示开始执行快照操作, 

     __查看最后一次成功执行快照的时间__  执行命令 `lastsave`  返回结果是一个unix时间戳

     ```html
     127.0.0.1:6379> lastsave
     (integer) 1493948055
     ```

   - 执行自动快照时 Redis采用的策略即是异步快照

4. __执行flushall__

   当执行`flushall` 命令时, Redis会清除数据库中的所有数据. __注意 __ 不论清空数据库的过程是否触发了自动快照条件, 只要__自动快照条件不为空__ , Redis就会执行一次快照操作, 

5. __执行复制时__ 

   当设置了主从模式时, Redis会在复制初始化时进行自动快照

### RDB 快照方式的原理

1.  快照文件存放位置, Redis 默认会将快照文件存储在 Redis 当前进程的工作目录中的 dump.rdb 文件中, 可以通过配置`dir` 和 dbfilename 两个参数分别执行快照文件的存储路径和文件名

   ```html
   236 # The filename where to dump the DB
   237 dbfilename dump.rdb
   
   246 # Note that you must specify a directory here, not a file name.
   247 dir /var/redis/6379
   ```

2. 快照过程
   * Redis使用fork函数复制一份当前进程(父进程) 的副本 (子进程).
   * 父进程继续接收并处理客户端发来的命令, 而子进程开始将内存中的数据写入硬盘中的临时文件
   * 当子进程写入完所有数据后会用该临时文件__替换__旧的RDB文件, 至此一次快照操作完成

### RDB注意的事项

1. 在执行fork的时候操作系统(类unix操作系统) 会使用写时复制策略. 即fork函数发生的一刻父子进程共享同一内存数据, 当父进程要更改其中某片数据时, 操作系统会将该片数据复制一份以保证子进程的数据不受影响, 所以__新的RDB文件存储的是执行fork一刻的内存数据__ 

2. 写时复制策略也保证了在fork的时刻虽然看上去生产了两份内存副本, 但实际上内存的占用量并不会增加一倍

3. 为了确保linux系统允许应用程序申请超过可用内存(物理内存和交换分区)的空间, 

   ```html
   在 /etc/sysctl.conf 文件中加入
   vm.overcommit_memory = 1   然后重启系统或者执行sysctl vm.overcommit_memory = 1
   ```

4. 当进行快照的过程中, 如果写入操作较多, 造成fork前后数据差异较大, 是会使得内存使用量显著超过实际数据大小的, 因为内存中不仅保存了当前的数据数据, 而且还保存着fork时刻的内存数据。

5. Redis在进行快照的过程中不会修改RDB文件, 只有按照结束后才会将旧的文件替换成新的, 也就是说任何时候RDB文件都是完整的. 这使得我们可以通过定时备份RDB文件来实现Redis数据库备份. 

   ```html
   RDB文件是经过压缩的(可以配置rdbcompression 参数以禁用压缩节省CPU占用)二进制格式, 所以占用的空间会小于内存中的数据大小, 更加利于传输
   
   221 # Compress string objects using LZF when dump .rdb databases?
   222 # For default that's set to 'yes' as it's almost always a win.
   223 # If you want to save some CPU in the saving child set it to 'no' but
   224 # the dataset will likely be bigger if you have compressible values or keys.
   225 rdbcompression yes
   ```

6. RDB 文件的载入工作是在服务器启动时自动执行的，所以 Redis 并没有专门用于载入 RDB 文件的命令，只要Redis 服务器在启动时检测到RDB文件存在，它就会自动载入 RDB 文件。

7. 如果服务开启了AOF 文件持久化功能，那么服务器会优先使用AOF文件来还原数据库状态。只有在AOF持久化功能处于关闭的时，服务器才会使用RDB文件来还原数据库状态。

## AOF方式

### 配置

1. 默认Redis没有开启AOF (append only file) 方式的持久化, 可以通过appendonly 参数启用

   ```html
   593 appendonly no                     将这里的no 修改成  yes
   594 
   595 # The name of the append only file (default: "appendonly.aof")
   596 
   597 appendfilename "appendonly.aof"
   ```

2. 开启了AOF持久化后每执行一条会更改Redis中的数据的命令, Redis就会将该命令写入硬盘中的AOF文件. AOF文件的保存位置和RDB文件的位置相同, 都是通过`dir` 参数设置的

### AOF 文件的重写

- 如下：这时Redis记录了前3条命令, 然而这时有一个问题是前2条命令,其实都是冗余的, 因为这两条的执行结果,会被第三条命令覆盖. 随着执行的命令越来越多, aof文件的大小也会越来大, 即使内存中实际的数据可能并没有多少

  ```html
  set foo 1
  set foo 2
  set foo 3
  ```

- Redis会自动优化aof文件, 就上面的例子而言, 就是将前两条无用的记录删除, Redis正是这样做的, 每当达到一定条件时Redis就会自动重写AOF文件, 这个文件可以在配置文件中设置

  ```html
  664 auto-aof-rewrite-percentage 100
  665 auto-aof-rewrite-min-size 64mb
  
  auto-aof-rewrite-percentage  参数的意义是当目前的AOF文件大小超过上一次重写时的AOF文件大小的百分之多少时会再次进行重写, 如果之前没有重写过, 则以启动时的aof文件大小为依据
  auto-aof-rewrite-min-size 参数限制了允许重写的最小aof文件大小, 通常在aof文件很小的情况下即使其中有很多冗余的命令我们也能接受
  ```

- 手动执行AOF重写

  ```html
  bgrewriteaof  命令手动执行aof重写
  ```

### 同步硬盘数据

虽然每次执行更改数据库内容的操作时, AOF 都会将命令记录在 AOF 文件中, 但是事实上, 由于操作系统的缓存机制, 数据并没有真正地写入硬盘中, 而是进入了系统的硬盘缓存.  在默认情况下系统每30秒会执行一次同步操作, 以便将硬盘缓存中的内容真正地写入硬盘, 在这30秒的过程中如果系统异常退出则会导致硬盘缓存中的数据丢失, 一般来讲启动 aof 持久化的应用都无法容忍这样的损失, 这就需要 Redis 在写入 aof 文件后自动要求系统将缓存内容同步到硬盘中. 在 Redis 中我们可以通过 appendfsync 参数设置同步的时机.

```html
622 # appendfsync always    每执行写入都执行同步
623 appendfsync everysec    每秒执行一次同步操作   兼顾了性能又保证了安全
624 # appendfsync no        完全交由操作系统来做(即30秒一次)
```

###  注意事项

固态硬盘 和 appendfsync always 使用固态硬盘的用户请谨慎 appendfsync  always 选项，因为这个选项让 Redis 每次只写入一个命令，而不是像其他 appendfsync 选项那样一次写入多个命令，这种不断地写入少量数据的做法有可能引发严重的写入放大（write amplification）问题，在某些情况下甚至会将固态硬盘的寿命从原来的几年降低为几个月。

