## 知识点

1. Datetime 和 timesamp 列都可以存储仙童类型的数据：时间和日期，精确到秒。然而 Timesamp 只使用 datetime 一半的存储空间，并且会根据时区变化，具有特殊的自动更新能力。另一方面，Timestamp 允许的时间范围要小。

## 架构

1. 共享锁（shared lock）和排他锁（exclusive lock），也叫读锁（read lock）和写锁（write lock）。
2. alter 修改表字段信息的时候增加表锁。

## 数据类型的优化

1. 更小的数据类型通常更快。因为它们占用更少的磁盘、内存和 CPU 缓存。
2. MySQL 可以为整数类型指定宽度，例如INT（11），对大多数应用这是没有意义的：它不会限制值的合法范围，只是规定了 MySQL 的一些交互工具用来显示字符的个数。对于存储和计算来说，INT(1) 和 INT(20) 是相同的。

###  使用枚举（enum）代替字符串类型

1. MySQL 在存储枚举时非常紧凑，回根据列表值的数量压缩到一个或者两个字节中。

2. MySQL 在内部回将每个值在列表中的位置保存为整数。并且在表的 `.frm` 文件中保存 `数字 - 字符串` 映射关系。

   ```mysql
   create table enum_tesst(
   	e enum('fish','apple','dog') not null
   );
   insert into enum_test(e) values('fish','dog','apple')
   
   select e + 0 = from enum_test 
   ```

## 计数器表

### 作用

用这种表缓冲一个用户的朋友数、文件下载次数等。

### 优化思路

```mysql
#1. 创建
    create table hit_counter(
        cnt int unsigned not null
    ) engine=innodb
#2. 网站的每次点击都会导致对计数器进行更新
	update hit_counter set cnt = cnt+1;
#3. 问题在于，对于任何想要更新这一行的事务来说，这条记录上都有一个全局的互斥锁，这会使得这一行只能串行执行。要获得更高的并发更新性能，也可以将计数器保存在多行中，每次随机选择一行进行更新。

#4. 优化后的表
    create table hit_counter(
        slot tinyint unsigned not null primary key,
        cnt int unsigned not null
    ) engine=innodb

#5. 优化后的更新
    # 随机选择一个 slot 进行更新
    update hit_counter set cnt = cnt + 1 where slot = 1

#6. 获取统计结果，需要使用下面这样的聚合查询
	select sum(cnt) from hit_counter;
```

## 创建高性能的索引

