#### redis之路开始 2017-11-23

1. 开启和停止

   ```html
   1. 直接运行 redis-server
   2. redis-server /usr/local/redis/redis.conf
   3. redis-server -port 6380                 可以修改启动的端口

   4. redis-cli -p 端口号 shutdown  
       当redis收到命令后, 会先断开所有的客户端连接, 然后根据配置执行持久化,最后完成退出
      redis 可以识别 kill redis 进程pid ,这样也是可以正常的结束redis,效果与上面的那个命令是一样的
   ```

2. 需要配置文件

   ```html
   参数                值
   daemonize         yes                                使redis以守护进程模式运行
   pidfile           /var/run/redis/6379.pid            设置redis的pid文件位置
   port              端口号                              设置redis监听的端口号
   dir              /var/redis/端口号                    设置持久化文件存放位置
   ```

3. 可以在redis运行时通过 `redis set` 命令在不重新启动redis的情况下动态修改部分redis配置

    config set loglevel warning    
    	config get loglevel              

4. redis 开发工具的使用

   * connection 连接配置项是     

     ```html
     HOST       localhost 
     port       6379
     ```

   * ssh通道连接配置对应的账户信息

5. redis数据库的几点区别

   * redis不支持为每一个数据库设置不同的访问密码
   * 多个数据库之间并没有完全的隔离, 比如 `flushall ` 就可以清空一个redis实例中所有数据库中的数据
   * 不同的应用应该使用不同的redis实例存储数据, 
   * 一个redis实例占用的内存只有1M左右

6. 获取符合规则的键名列表

   keys  pattern

   * `? `   匹配一个字符
   * `*` 匹配任意个(包括0个) 字符
   * `[]` 匹配括号间的任一字符, 可以使用  `-` 符号表示一个范围
   * \?  匹配字符X, 用于转义字符  , 这样可以匹配 ?


7. 键的操作命令

   ```html
   set   key    设置一个键名
   get   key    获取一个键名
   exists  key  判断一个键名是否存在
   del   key    删除一个键名
   type  key    获取键名的类型
   redis-cli keys "user:*"   可以删除所有以user:开头的键名
   ```

------

#### 字符串类型 (redis明确区分字符串, 整数和浮点数)

1. 字符串类型是redis 中最基本的数据类型,它存储任何形式的字符串, 包括二进制的数据, 可以使用其存储用户的邮箱、json化的对象,甚至是一张图片, 一个字符串类型键允许存储的数据的最大容量是512MB.

2. 字符串可以存储一下数据的值

   - 字节串
   - 整数
   - 浮点数

3. 字符串操作命令

   ```html
   set key value
   get key
   incr key               让当前的键值递增,并返回递增后的值
   decr key 	           将键存储的值减去1
   incrby key value       将建存储的值加上一个整数
   decrby key value       将键存储的值减去一个整数
   incrbyfloat key value  将将存储的值加上浮点数value
   ```

4. 实际的应用中

   * redis的键名最佳的实践是   对象类型:对象ID:对象属性

   * 存储文章数据
        可以使用序列化(php serialize 和 javascript)将他们转化成一个字符,由于字符串类型的键可以存储
        二进制数据,所以可以是用  https://msgpack.org/ 中的messagePack 进行序列化,速度快,占用空间更
        小

   * 生成自增的id  

     ```html
     对于每一类的对象使用名为对象类型   user:count   来存储当前类型对象的数量
     每增加一次使用incr命令增加
     ```

   * 存储文章类型

     ```html
     首先获得新文章的id
       $postID = incr post:count    
     将博客文章的诸多元素序列化成字符串
       $serializedPost = serialize($title,$content,$authorm,$time);
     把序列化后的字符串存一个字符串类型的键名中
       set  post:$postID:data, $serializePost
     从redis中读取数据
       get  post:56:data
     执行反序列化成文章的各个元素
     $title,$content,$authorm,$time = unserialize($serializedPost)
     获取并递增文章的访问量
     $count = incr post:56:page.view
     ```

5. 字符串的其他命令

   * getrange   key  start  end  获取一个有偏移量start至偏移量end范围内所有字符组成的子串, 包括start和end在内

     ```html
     127.0.0.1:6379> getrange new 2 -1
     "chunyubuhaowan"
     ```

   * setrange key offset  value  将从start偏移量__开始的子串设置为给定的值__ 返回值为字符串当前的长度

     ```html
     127.0.0.1:6379> setrange new 9 MACHUN
     (integer) 16
     127.0.0.1:6379> get new
     "MachunyubMACHUNn"
     ```

   * 向尾部追加值 append key value

     这个命令的作用是向键名的尾部追加value。如果键名不存在则将该建的值设置为value， 相当于set key 		value。返回值是追加后字符串的总长度

   * 获取字符串长度   strlen  key

   * 可以同时设置多个键的键值

------

#### 散列类型

1. 散列类型适合存储对象, 使用对象类别和ID构成键名

2. hset 命令 的方便之处在于不区分插入和更新操作, 这意味着修改数据时不用实现判断字段是否存在来决定要执行的是插入操作还是更新操作,不同的是当执行插入操作时hset命令会返回1, 当执行更新操作时会返回0

3. 实践(存储文章的数据)

   ```html
   post:42:title   第一篇日志
   post:42:author   小白
   post:42:time     2012年9月21日
   post:42:content   这是日志内容
   ```

4. 命令

   ```html
   hset
   hget
   hmset key field1 value1 field2 value2    设置多个字段
   gmset key field1 fields2                 获取多个字段
   hexists key field                        判断字段是否存在
   hsetnx 							 如果设置的字段已经存在, hsetnx将会不执行任何操作
   hincrby key field increment      返回值是增值后的字段值
   hincrbyfloat key field increment 将键key存储的值加上浮点数
   hkeys key 						 获取散列包含的所有键
   hvaks key 						 获取散列包含的所有值
   hgetall key  					 获取散列包含的所有键值对
   ```

5. 尽管有了`hgetall` 存在, 但是`hkeys和hvals`命令也是非常有用的, 如果散列包含的值非常大, 那么用户可以先使用hkeys取出散列包含的所有键, 然后再使用hget一个接一个地取出

------

### 列表类型

1. 列表类型可以存储一个有序的字符串列表, 常用的操作时向列表两端添加元素,列表的内部是使用双向链表实现的

   >双向链表也叫双链表，是链表的一种，它的每个数据结点中都有两个指针，分别指向__直接后继__ 和__直接前驱__ 。所以，从双向链表中的任意一个结点开始，都可以很方便地访问它的前驱结点和后继结点。一般我们都构造双向循环链表。

2. 链表的优点 

   就是获取越接近两端的元素速度就越快, 这意味着即使是一个有几千万个元素的列表,获取头部和尾部的记录也是极快的

   缺点

   通过索引访问元素比较慢.

3. 一个列表类型键最多能容纳2^32-1个元素。

4. 命令

   ```html
   向列表两端增加元素
   	lpush  key value  用来向列表左边增加元素,返回值表示增加元素后列表的长度
       rpush  key value  
   从列表两端弹出元素
   	lpop   key   从列左边弹出一个元素, 返回被移除的元素值
   	rpop   key
   获取列表中元素的个数
      llen  key
   获取列表片段
   	lrange key start stop  这个命令不会删除列表中的数据,
       lrange 命令支持负索引,表示从右边开始计算序数.
       lrange 0 -1 可以获取列表中所有元素
       1. 如果start的索引位置比stop的索引位置靠后,将返回空列表
       2. 如果stop大于实际的索引范围,这会返回到列表最右边的元素
   阻塞式的列表弹出命令以及在列表之间移动元素的命令
       1. blpop 
   	2. brpop
   	3. brpoplpush
   删除列表中指定的值
      lrem key count value     删除列表中前count个值为value的元素, 返回值是实际删除的元素个数
      1. 当count > 0 时, lrem命令会从列表左边开始删除前count个值为value的元素
      2. 当count < 0 时, lrem名利会从列表的右边开始删除前count个值为value的元素
      3. 当count = 0 时,lrem命令会删除所有值为value的元素
   获取/设置指定索引的元素值     
        lindex key index           用来返回指定索引的元素,索引从0开始
        lset key index value       将索引是index的元素赋值为value
   只保留列表指定片段
        ltrim  key start end       删除从左边开始指定索引范围之外的所有元素
        这个命令可以和lpush命令一起来使用限制列表中元素的数量, 比如记录日志,我们只希望保留100条记录
    向列表中插入元素
        linsert key before|after  pivot value 
        命令会先在列表中从左到右查找值为pivot的元素, 
        然后根 据第二个参数有的before 开决定将value的值插入到该元素的前面还是后面, 
        返回值是插入后列表的个数
    将元素从一个列表转到另一个列表中
        rpoplpush source destination   
        会先将source列表中类型键的右边弹出一个元素,然后将其加入到destination列表的左边,并返回这个
        元素的值
        伪代码实现的
        def repoplpush($source, $destination)
            $value = rpop $source
            lpush $destination, $value
            return $value;
        这个命令可以很直观的在多个对列中传递数据,而且还可以在程序执行的过程中仍然可以不断的向列表中加入新的内容
   ```

------

### 集合类型

1. 一个集合类型键可以存储至少2^32-1个

2. 集合和列表的区别是集合没有顺序

3. 命令

   ```html
   增加/删除元素
     sadd  key member    向集合中增加一个或者多个元素, 如果键不存在就会自动创建,如果加入的元素存在于
   					  集合中就会忽略这个元素,返回值是成功加入的元素数量.
     srem  key member    从集合中删除一个或多个元素, 并返回删除成功的个数

   获取集合中的所有元素
     smembers key       命令会返回集合中的所有元素

   判断元素是否存在于集合中  
     sismember key member  但存在时返回1, 不存在时返回0

   集合间运算
     sdiff  key   用来对多个集合执行差集运算, 计算集合A和集合B的差集  sdiff setA setB 
                  也支持多个集合的差集运算 sdiff setA setB setC
     sinter key   sunion 命令用来对集合执行并集运算的, 表示属于A和属于B的元素的构成的集合
                  sunion 命令同样支持同时传入多个键

   获取集合中元素个数
     scard  key   命令用来获取集合中的元素个数

   进行集合运算并将结果存储
   sdiffstore  destination key   这个命令和sdiff命令功能是一样的, 唯一的区别是前者不会直接返回运算
                                 结果, 而是将结果存储在destination键中
   sunionstore                   这个命令与上面这个命令类似

   随机获取集合中的元素
     srandmember  key  [count]   命令用来随机的从集合中获取一个元素, 
                        count    可以传递参数来一次随机获取多个元素
                        当count为负数时, srandmember会随机从集合里获取|count|,这些元素有可能重复
                        当count为正数时, 会随机的从集合中获取count个不重复的元素
   将集合中的元素移动到另一个集合中
      smove  source-key dest-key item  如果集合source-key 包含元素item, 那么从集合source-key里面移除元素item, 并将元素item添加到集合dest-key中,成功返回1, 否则返回0; 
   从集合中弹出一个元素
       spop  key  由于集合类型的元素是无序的, 所以spop命令会从集合中随机选择一个元素弹出
   ```

4. `srandmember` 这个命令返回的数据似乎并不是非常的随机这是由于集合类型采用存储结构(散列表)造成的. 散列表使用散列函数将元素映射到不同的存储位置上以实现O(1)时间复杂度的元素查找.

   __示例的解释__ 

    当使用散列函数存储元素b时, 使用散列函数计算出b的散列值是0, 下次查找b是就可以用同样的散列函数再次计算b的散列值, 并直接到相应的桶中找到b, 当两个不同的元素的散列子相同时会出现冲突, Redis使用拉链发来解决冲突,即将散列子冲突的元素以链表的形式存入同一桶中, 查找元素是先找到元素对应的桶, 然后再从同中的链表中找到对应的元素. 使用`srandmember` 命令从集合中或的一个随机元素时, Redis首先会从所有桶中选择一个桶, 然后再从桶中的所有元素中随机选择一个元素,所以元素所在的桶中的元素数量越少, 其被随机选中的可能性就越大.

5. 实践

   * 存储文章标签

     文章的标签是互不相同的, 而且展示时对这些标签的排序顺序并没有要求, 我们可以使用集合类型键存储文章标签

     每篇文章使用的键名为` post:文章ID:tags` 的键存储该篇文章的标签

     __但是__ 当需要用户直接设置所有标签后一起上传修改, 程序直接覆盖了原来的标签, 这个时候也可以使用字符串解决

   * 通过标签搜索文章

     可以用标签的名  tag:mysql:posts    将标记了mysql的文章id找出来

     如果需要实现找到同时属于java, mysyql , redis 的3个标签的文章, 只需要将`tag:java:posts` 

     `tag:mysql:posts`  和 `tag:Mysql:posts` 这三个键取交集


------

### 有序集合

1. 有序结合类型在某些方面和列表类型相似

   * 二者是有序的
   * 都可以 获得某一范围的元素

2. 二者的区别

   * 列表类型是通过链表实现的, 获取靠近两端的数据速度极快, 而当元素增多后, 访问中间数据的速度会较慢

     所以他更加适合实现如 "新鲜事" 或 "日志" 这样很少访问中间元素的应用中

   * 有序集合类型是使用散列表和跳跃表实现的, 所以即使读取位于中间部分的数据速度也很快

   * 列表中不能简单地调整某个元素的位置, 但是有序集合就可以(通过更改这个元素的分数)

   * 有序集合要比列表类型更消耗内存

3. 命令

   ```html
   增加元素
     zadd key score member
        向有序集合中加入一个元素和该元素的分数, 如果该元素已经存在则会用新的分数替换原有的分数, 
        zadd命令的返回值是新加入到集合中的元素个数(不包含之前已经存在的元素)
        zadd scoreboard 89 Tom 67 Peter 100 David   向有序集合中插入三条记录

   获取元素的分数
     zscore key member
    
   获取排名在某个范围的元素列表  从小到大 
       zrange  key start stop
       如果想获取元素的分数的话可以在zrange 命令尾部加上 withscores
       127.0.0.1:6379> zrange scoreboard 0 -1 withscores
       1) "Peter"
       2) "76"
       3) "Tom"
       4) "89"
       5) "David"
       6) "100"

   获取从大到小的顺序
     zrevrange scoreboard 0 -1 withscores

   获取指定分数范围的元素 -inf和+inf 分别表示负无穷和正无穷
     zrangebyscore  key min max [withscores] [limit offset count]
     如果希望分数范围不包含端点值, 可以在分数前加上 "("符号, 
         127.0.0.1:6379> zrangebyscore scoreboard 80 (100
         1) "Tom"
         127.0.0.1:6379> zrangebyscore scoreboard 80 100
         1) "Tom"
         2) "David"
         127.0.0.1:6379>
     这个的用法 limit  offset count
         127.0.0.1:6379> zrangebyscore scoreboard 80 100 
         1) "Tom"
         2) "David"
         127.0.0.1:6379> zrangebyscore scoreboard 80 100 limit 0 2
         1) "Tom"
         2) "David"
         127.0.0.1:6379> zrangebyscore scoreboard 80 100 limit 1 2
         1) "David"
     获取获得分数高于60分的从第二个人开始的3个人
       zrangebyscore scoreboard 60 +inf limit 1 3
     获取分数低于或等于100分的前3个人
      zrevrangebyscore scoreboard 100 0 limit 0 3
   增加某个元素的分数
   zincrby key increment member    zincrby scoreboard 20 Tom 返回值是更改后的分数
   ```

4. 如果两个元素的分数相同, redis会按照字典顺序(即 0 < 9 , a <  z, A < Z这样的顺序排序)

   如果元素是中文的话, 取决于中文的编码方式

5. 实践

   *  实现按照点击量排序

     额外使用一个有序集合类型的键来实现, 这个键中以文章的ID作为元素, 以该文章的点击量作为该元素的分

     数,以该文章的点击量作为该元素的分数.   键名  post:page.view

     ```html
     代码实现
      $postsPerPage = 10
      $start = ($currentPage - 1)* $postsPerPage
      $end = $currentPage * $postsPerPage - 1
      $postsID = zrevrange post:page.view, $start, $end
      for each $id in $postsID
      $postData = hgetall post:$id
      print 文章标题: $postData.title
     ```

   * 改进时间排序

     前面的思路是每次发布新文章时都将文章的ID加入到名为posts:list的列表类型键中来获得按照时间顺序

     排序的文章列表,但是由于列表类型更改元素的顺序比较麻烦,

     为了能够自由的更文章发布时间, 可以采用有序集合类型代替列表类型,此时元素的分数就是文章发布的时间, 通过修改元素对应的分数就可以达到更改时间的目的, 加上` zrevrangebyscore` 

6. 命令拾遗

   ```html
   获得集合中元素的数量
      zcard  key
   获得指定分数范围内的元素个数
      zcount key min max   zcount scoreboard 90 100
   删除一个或多个元素
      zrem key member  返回值是成功删除的元素数量(不包含本来就不存在的元素)
   按照排名范围删除元素   
      zremrangebyrank  key start stop 
      按照从小到大的顺序删除(指得是索引)处在指定排名范围内的所有元素, 并返回删除的元素数量
   按照分数范围删除元素
      zremrangebyscore key min max
   获取元素的排名
      zrank key member  zrank命令会按照元素分数从小到大的顺序获得指定的元素的排名(从0开始)
      zrevrank key member  这个命令与上一个命令相反, 分数最大的元素索引为(从0开始)
   计算有序集合的交集
      zinterstore destination numkeys key [weights weight] aggregate SUM|MIN|MAX
      当aggregate键中元素的分数是由aggregate参数决定的
         aggregate 是sum时就是默认值, destination键中元素的分数是每个参与计算的集合中该元素分数的和
   		zadd sortedSets 1 a 2 b
   		zadd sortedSets1 10 a 20 b
           zinterstore sortedSetsResult 2 sortedSets sortedSets1 aggregate sum
      当aggregate是min时destination键中元素的分数是每个参与计算的集合中该元素的最小值
          zinterstore sortedSetsResult 2 sortedSets sortedSets1 aggregate min 
          将两个集合中对应的索引最小的元素组成了一个新的集合
      当aggregate是max时destination键中元素的分数每个参与计算的集合中该元素的最大值
   ```

   ​

------



#### 注释补充

1. 竞态条件是指一个系统或者进程的输出, 依赖于不受控制的时间的出现顺序或者出现时机
2. 原子操作取原子的不可拆分的意思, 原子操作是最小的执行单位, 不会在执行的过程中被其他命令插入打断
3. `http:://twitter.github.com/bootstrap`
4. Redis中, 多个命令原子地执行指的是, 在这些命令正在读取或者修改数据的时候, 其他客户端不能读取或者修改相同的数据
5. 集合和列表最大的区别是集合只能存放不相同的数据, 而列表可以存放相同的数据
