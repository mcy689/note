# redis杂项

## Redis事务

1. 实践使用redis来存储微博中的用户关系

   ```html
   			关注    user:用户ID:followers 
   每一个用户	
   			被关注  user:用户ID:following  

   def follow($currentUser, $targetUser)
       sadd user:$currentUser:following,$currentUser
       sadd user:$targetUser:followers,$targetUser
   ```

2. Redis中的事务是一组命令的集合, 事务同命令一样都是Redis的最小执行单位

   ```html
   multi
   sadd "user1:following" 2
   sadd "user2:followers" 1
   exec
   ```

3. Redis使用事务的作用

   * Redis保证了一个事务中的所有命令要么都执行,要么都不执行
   * Redis的事务还能保证一个事务内的命令一次执行而不被其他命令插入,

4. 事务执行时错误的处理

   * 遇到语法错误, 语法错误指命令不存在或者命令参数的个数不对, 这种错误在2.6.5之前的版本会执行部分正确的命令
   * 运行错误. 运行错误指在命令执行时出现的错误, 比如使用散列类型的命令操作集合类型的键, 这样的错误时会被`redis` 接受并执行的

5. `watch` 命令

   > 这个命令可以监控一个或多个键, 一旦其中有一个键被修改(或删除) , 之后的事务就不会执行. 监控一直持续到exec命令(事务中的命令是在exec之后才执行的, 所以在multi命令后可以修改watch监控的键值)
   >
   > 127.0.0.1:6379> set key 1
   > OK
   > 127.0.0.1:6379> watch key 
   > OK
   > 127.0.0.1:6379> set key 2
   > OK
   > 127.0.0.1:6379> multi
   > OK
   > 127.0.0.1:6379> set key 3
   > QUEUED
   > 127.0.0.1:6379> exec
   > (nil)
   > 127.0.0.1:6379> get key
   > "2"
   > 127.0.0.1:6379> 

   ```python
   #实践 为了避免竞态
   def incr($key)
      watch $key
      $value = get $key
      if not $value
        $value = 0
      $value = $value + 1
      multi
      set $key,$value
      result = exec
      return result[0]
   ```

   __注意__ 

     由于watch命令的作用只是当被监控的键值被修改后阻止之后一个事务的执行, 而不能保证其他客户端不修改这个一键值, 所以我们需要在exec执行失败后重新执行整个函数. 

6. `unwatch` 命令来取消监控

   ```python
   #实现hsetnx
   def hsetxx($key,$field,$value)
       watch $key
       $isFieldExists = hexists $key,$field
       if $isFieldExists is 1
       multi
       hset $key,$field,$value
       exec
       else
       unwatch
       return $isFieldExists
   #在代码中会判断要赋值的字段是否存在, 如果字段不存在的话就不执行事务中的命令, 但需要使用unwatch命令来保证下一个事务的执行不会受到影响
   ```

---

## 过期时间

1. 实际的开发中, 如限时优惠活动, 缓存或验证码

2. 命令

   ```html
   expire   key  seconds    表示键的过期时间, 单位秒,必须是整秒
   
   ttl  key    查询键的剩余时间, 当键不存在时会返回-2
               建立一个键后的默认情况(无过期时间)会返回-1
   清除过期时间
   	persist  可以取消过期时间设置(将键回复成永久的) 
            	 过期时间被成功清除这返回1, 否则返回0(因为键不存在或者本来就是永久的)
   
       除了使用上一个命令为,使用set或者getset命令为键服务值也会同时清除键的过期时间
   
   	pexpipe key 1     时间单位是毫秒
   	pttl    key       命令以毫秒为单位返回键的剩余时间
   
   expireat  key timestamp   第二个参数使用unix时间作为第二个参数表示键的过期时刻
   pexpireat key timestamp   这个单位是毫秒
   ```

   __注意__ 

    如果使用watch命令检测了一个拥有过期时间的键, 该键时间到期自动删除并不会被watch命令认为该键被改变

3. 访问频率限制

   * 限制每分钟每个用户最多只能访问100个页面, 

     ```python
     $isKeyExists = exists rate.limiting:$ip
     if $isKeyExists is 1
     	$times = incr rate.limiting:$ip
     if $times > 100
     	print 访问频率超过了限制, 请稍后再试
     	exit
     else
     	multi
     	incr rate.limiting:$ip
         expipe $keyName, 60
         exec
     ```

4. 实现缓存

   * 为了提高网站的负载能力, 常常需要将一些访问频率较高但是对`cpu` 或 `IO`资源消耗较大的操作的结果缓存起来,并希望让这些缓存过一段时间自动过期

     ```python
     $rank = GET cache:rank
     if not $rank
        $rank = 计算排名...
         multi
         set  cache:rank,$rank
         expire cache:rank,7200
         exec
     ```

   * 当服务器内存有限时, 如果大量地使用缓存且过期时间设置得过长就会导致`redis` 占满内存; 另一方面如果为了防止`Redis` 占用内存过大而将缓存键的过期时间设置得太短, 就可能导致缓存命中率过低并且大量内存白白地闲置

   * 为了最大限度的使用redis, 并让redis按照一定的规则淘汰不需要的缓存键

     >1. 修改配置文件  `maxmemory`  参数限制`Redis` 最大可用内存大小(单位是字节), 当超出了这个限制时, redis 会依据`maxmemory-policy`参数指定的 策略来删除不需要的键直到redis占用的内存小于指定内存
     >
     >2. Redis支持的淘汰键的规则 (maxmemory-policy的参数设定)
     >
     >   |       规则        |            说明             |
     >   | :-------------: | :-----------------------: |
     >   |  volatile-lru   | 使用lru算法删除一个键(只对设置了过期时间的键) |
     >   |   allkeys-lru   |       使用lru算法删除一个键        |
     >   | volatile-random |   随机删除一个键(只对设置了过期时间的键)    |
     >   | allkeys-random  |          随机删除一个键          |
     >   |  volatile-ttl   |       删除过期时间最近的一个键        |
     >   |   noeviction    |       不删除键, 只是返回错误        |

5. 排序

   * 有序集合的集合操作,  有序集合常见的使用场景是大数据排序, 例如游戏的玩家排行榜

   * sort命令

     * 这个命令可以对列表类型, 集合类型和有序集合类型键进行排序.

     * 在对有序集合类型排序时会忽略元素的分数, 只针对元素自身的值进行排序

       ```html
       127.0.0.1:6379> sadd tag:rub:posts 6 12 26
       (integer) 3
       127.0.0.1:6379> sort tag:rub:posts 
       1) "2"
       2) "6"
       3) "12"
       4) "26"
       127.0.0.1:6379> lpush mylist 4 2 8 9 1 2
       (integer) 6
       127.0.0.1:6379> sort mylist
       1) "1"
       2) "2"
       3) "2"
       4) "4"
       5) "8"
       6) "9"
       127.0.0.1:6379> zadd myzset 40 2 36 89 60 4 
       (integer) 3
       127.0.0.1:6379> sort myzset
       1) "2"
       2) "4"
       3) "89"
       ```

     * sort命令还款可以通过alpha参数实现按照字典顺序排序非数字元素

       ```html
       127.0.0.1:6379> lpush mylistalpha a c e d B C A
       (integer) 7
       127.0.0.1:6379> sort mylistalpha
       (error) ERR One or more scores can't be converted into double
       127.0.0.1:6379> sort mylistalpha alpha
       1) "a"
       2) "A"
       3) "B"
       4) "c"
       5) "C"
       6) "d"
       7) "e"
       如果没有加alpha参数, sort命令会尝试将所有元素转换成双精度浮点数来比较,如果无法转换则会报错
       ```

     * sort默认是按照从小到大的顺序排序, 分页

       ```html
       sort tag:ruby:posts desc
       sort tag:ruby:posts desc limit 1 2     组合使用
       ```

     * sort排序的高级用法

       ```html
       127.0.0.1:6379> hset d-7 field 5
       (integer) 1
       127.0.0.1:6379> hset d-15 field 1
       (integer) 1
       127.0.0.1:6379> hset d-23 field 9
       (integer) 1
       127.0.0.1:6379> hset d-110 field 3
       (integer) 1
       127.0.0.1:6379> lpush testsort 7 23 110 15
       (integer) 4
       127.0.0.1:6379> sort testsort by d-*->field get d-*->field
       1) "1"
       2) "3"
       3) "5"
       4) "9"
       127.0.0.1:6379> sort testsort by d-*->field
       1) "15"
       2) "110"
       3) "7"
       4) "23"
       ```

   * by参数的语法为by参考键，其中参考键可以是__字符串类型键__ 或者是__散列类型键__ 的某个字段

     ```html
     sort tag:ruby:posts by post:*-> time desc
                            键名 -> 字段名

     127.0.0.1:6379> lpush sortbylist 2 1 3
     (integer) 3
     127.0.0.1:6379> set itemscore:1 50
     OK
     127.0.0.1:6379> set itemscore:2 100
     OK
     127.0.0.1:6379> set itemscore:3 -10
     OK
     127.0.0.1:6379> sort sortbylist by itemscore:*
     1) "3"
     2) "1"
     3) "2"
     127.0.0.1:6379> sort sortbylist by itemscore:* desc
     1) "2"
     2) "1"
     3) "3"
     127.0.0.1:6379> sort sortbylist by itemscore:* asc
     1) "3"
     2) "1"
     3) "2"
     ```

   * 当参考键名不包含 “×”时， 即常量键名， 与元素值无关，sort命令将不会执行排序操作

   * 如果几个元素的__参考键值相同__ ，则sort命令会再比较__元素本身的值__ 来决定元素的顺序

   * 当参考键不存在时, 会默认参考键的值为0

   * get参数

     1. get参数不影响排序, 它的作用是使sort命令的返回结果不再是元素自身的值, 而是get参数中指定的键值

     2. get参数支持__字符串类型__ 和__散列类型的键__ , 并使用 `"*"` 作为占位符

        ```html
        要实现在排序后直接返回ID对应的文章标题
        sort tag:ruby:posts by post:* -> time desc get post:* -> title
        ```

     3. 在一个sort命令中可以有多个get参数(而by参数只能有一个)

     4. 在多个get查询中, 如果需要返回文章ID, 则使用 `get#` , 这个操作会返回元素本身的值

        ```html
        sort tag:ruby:posts by post:*->time desc get post:*->title get post:*->time get #
        ```

   * store参数

     1. sort会直接返回排序结果, 如果希望保存排序结果, 可以使用store参数, 如希望把结果保存到`sort.result`键中 (这个键名的意思类似于php中sortResult )

        ```html
        sort tag:ruby:posts by post:*->time desc get post:*->title get post:*->time get # store sort.result
        ```

     2. 保存后的键的类型为列表类型, 如果键已经存在则会覆盖它

     3. 加上store参数后sort命令的返回值为结果的个数

     4. store参数常用来结合expire命令__缓存排序结果__ 

        ```python
        #判断是否存在之前排序结果的缓存
        $isCacheExists = exists cache:sort
            if $isCacheExists is 1
            #如果存在则直接返回
            return lrange cache.sort,0 -1
        else 
          #如果不存在, 则使用sort命令排序并将结果存入cache.sort键中作为缓存
            $sortResult = sort some.list store cache.sort
          #设置缓存的过期时间为10分钟
            expire cache.sort,600
          # 返回排序结果
         return $sortResult
        ```

6. 性能优化

   * 开发中使用sort命令时需要注意
     * 尽可能减少待排序键中元素的数量(使N尽可能小)
     * 使用limit参数获取需要的数据
     * 如果要排序的数据量较大, 尽可能使用store参数将结果缓存

7. 任务队列

   * 当页面需要进行如发送邮件、复杂数据运算等耗时较长的操作时会阻塞页面的渲染。为了避免用户等待太久，应该使用独立的线程来完成这类操作。这个实现就可以通过任务队列来实现，

   * 优点

     * 松耦合

       生产者和消费者无需知道彼此的实现细节,只需要约定好任务的描述格式。这使得生产者和消费可以由不同的团队使用不容的编程语言编写

     * 易于扩展

       消费者可以有多个，而且可以分布在不同的服务器中，借此可以轻易地降低单台服务器的负载

8. 使用redis实现任务队

   1. brpop 命令和rpop命令相似,唯一区别是当列表中没有元素时brpop命令会一直阻塞连接,直到有新元素加入
   2. brpop命令接受两个参数, 第一个是键名，第二个是超时间,单位是i秒。当超过了此时间仍然没有获取新元素的话就会返回nil。 超时间为0，表示不限制等待的时间，即如果没有新元素加入列表就会永远阻塞下去

9. 优先级队列

   1. 应用场景

      当发布新文章后通知订阅用户, 注册发送通知邮件两种任务同时存在时,优先执行前者, 

   2. `brpop` 命令可以同时接受多个键, 其完整的命令格式为`brpop key [key] timeout` 

      * 如果所有键都没有元素则阻塞
      * 如果其中有一个键有元素则会从该键中弹出元素
      * 如果多个键都有元素则按照从左到右的顺序取第一个键中的一个元素. 

10. 发布和订阅模式

  * `publish channel message`    publish命令的返回值表示接收到这条消息的订阅数量, 发出去的消息不会被持久化, 也就是说当有客户端订阅 channel.1后只能收到后续发布到该频道的消息, 之前发送的就收不到了,

  * 订阅频道的命令是subscribe channel [channel....] 。 

  * 执行subcribe 命令后客户端会进入订阅状态, 处于此状态下客户端不能使用除`subcribe` `unsubcribe` `psubscribe` `punsubscribe` 这四个属于"发布/订阅"模式的命令之外的命令, 否则会报错

  * 执行`subscribe` 

    ```html
    127.0.0.1:6379> subscribe channel.1
    Reading messages... (press Ctrl-C to quit)
    1) "subscribe"
    2) "channel.1"
    3) (integer) 1
    1) "message"
    2) "channel.1"
    3) "machunyu"
    1) "message"
    2) "channel.1"
    3) "buhaowan"

    subscribe  表示订阅成功的反馈信息, 第二个值是订阅成功的频道名称, 第三个值是当前客户端订阅的频道数量
    messsage   表示接收到的消息. 第二个值表示产生消息的频道名称, 第三个值是消息的内容
    ```

  * 使用`psubscribe` 命令订阅指定的规则   `psubscribe channel.?*`  这个规则可以channel.1 到channel.10

    ```html
    127.0.0.1:6379> psubscribe channel.?*
    Reading messages... (press Ctrl-C to quit)
    1) "psubscribe"
    2) "channel.?*"
    3) (integer) 1
    1) "pmessage"
    2) "channel.?*"
    3) "channel.1"
    4) "ceshi"

    第一个值表示这条消息是通过psubscribe命令订阅频道而受到的,第二个表示订阅时使用的通配符, 第三个值表示实际收到消息的通配符, 第四个值则是消息内容
    ```

11. 管道

    * 通过管道可以一次性发送多条命令并在执行完成后一次性将结果返回

    * 客户端和redis使用tcp协议连接. 不论是客户端向redis发送命令还是redis向客户端返回命令的执行结果,都需要经过网络传输, 这两个部分的总消耗时为__往返时延__ 

    * 应用场景

        当一组命令中每条命令都不依赖于之前命令的执行结果时就可以将这组命令一起通过管道发出, 管道通过减少客户端与redis的通信次数来实现降低往返时延累计值的目的

---

## 处理系统故障

1. Redis [官方地址](https://redis.io/topics/persistence)。

## Redis的性能测试程序

```html
redis-benchmark [-h <host>] [-p <port>] [-c <clients>] [-n <requests]> [-k <boolean>]
  
 -h <hostname>      Server hostname (default 127.0.0.1)
 -p <port>          Server port (default 6379)
 -s <socket>        Server socket (overrides host and port)
 -a <password>      Password for Redis Auth
 -c <clients>       Number of parallel connections (default 50)
 -n <requests>      Total number of requests (default 100000)
 -d <size>          Data size of SET/GET value in bytes (default 2)
```

