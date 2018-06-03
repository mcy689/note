### 提升数据库的性能

1. 查询缓存( Query Caching )

   * 查询缓存是MySql的一个重要特性, 它缓存了`select` 查询及其结果数据集, 当同一个的`select` 查询发生时, MySql从内存中直接取出结果, 这样就加快了查询的执行速度, 同时减小了数据库的压力

     ```mysql
     -- 查看MySql服务器是否开启查询缓存
     show variables like 'have_query_cache';
     ```

     ​