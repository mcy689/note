# 记录常用命令

1. 查看是否开启了查询缓存

   ```mysql
   show variables like "%query_cache%";
   ```

2. 查看查询缓存的空间

   ```mysql
   show status like "%Qcache%"
   
   +-------------------------+----------+
   | Variable_name           | Value    |
   +-------------------------+----------+
   | Qcache_free_blocks      | 1        |
   | Qcache_free_memory      | 16759696 |  显示缓存的空余时间
   | Qcache_hits             | 0        |
   | Qcache_inserts          | 0        |
   | Qcache_lowmem_prunes    | 0        |
   | Qcache_not_cached       | 1        |
   | Qcache_queries_in_cache | 0        |
   | Qcache_total_blocks     | 1        |
   +-------------------------+----------+
   
   ```

   