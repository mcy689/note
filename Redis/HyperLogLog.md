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



