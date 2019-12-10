# HyperLogLog

每个 HyerLogLog 只需要使用 12k 字节内存，以及几个字节的内存来存储键本身。

1. `pfadd` 将任意数量的元素添加到指定的 HyperLogLog 里面。

   ```redis
   #pfadd key element [element ...]
   
   pfadd database 'redis' 'MongoDB' 'MySQL'
   ```

2. `pfcount` 当命令作用于单个 HyperLogLog 时，复制度为O(1)。

   ```redis
   127.0.0.1:6379> pfadd database mysql mongodb redis pgsql
   (integer) 1
   127.0.0.1:6379> pfcount database
   (integer) 4
   127.0.0.1:6379> 
   ```

3. `pfmerge` 将多个 HyperLogLog 合并（merge）为一个 HyperLogLog ， 合并后的 HyperLogLog 的基数接近于所有输入 HyperLogLog 的可见集合（observed set）的并集。

   ```redis
   127.0.0.1:6379> pfadd nosql "Redis" "MongoDB" "Memcached"
   (integer) 1
   127.0.0.1:6379> pfadd REBMS "MySQL" "PostgreSQL"
   (integer) 1
   127.0.0.1:6379> pfmerge database nosql RDBMS
   OK
   127.0.0.1:6379> pfcount database
   (integer) 5
   ```

## 布隆过滤器

1. 插件地址：`https://github.com/RedisLabsModules/rebloom#building-and-loading-rebloom`

2. 当布隆过滤器说某个值存在时，这个值可能不存在；当它说不存在时，那就肯定不存在。

3. [命令](https://github.com/RedisLabsModules/rebloom/blob/master/docs/Bloom_Commands.md)

   ```html
   添加
   bf.add codehole user1
   判断是否存在
   bf.exists codehole user1
   批量添加
   bf.madd codehole user2 user3
   批量判断是否存在
   bf.mexists codehole user1 user2
   ```

4. 应用场景

   - 在爬虫系统中，我们需要对 URL 进行去重，已经爬过的网页就可以不用爬了。
   - 布隆过滤器可以显著的降低数据库的 IO 请求数量。当用户来查询某个 row 时，通过布隆过滤器可以显著降低数据库的 IO 请求数量。

