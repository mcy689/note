## 积硅步, 致千里

### 分区表

1. 通过这个命令可以查看是否支持分区表 `show VARIABLES like "%partition%"`

   ```mysql
   #判断是否支持分区
   mysql> show variables like "%partition%";
   +-------------------+-------+
   | Variable_name     | Value |
   +-------------------+-------+
   | have_partitioning | YES   |
   +-------------------+-------+
   
   #判断是否安装了分区插件
   mysql> show plugins;
   | INNODB_BUFFER_POOL_STATS | ACTIVE   | INFORMATION SCHEMA | NULL    | GPL     |
   | PERFORMANCE_SCHEMA       | ACTIVE   | STORAGE ENGINE     | NULL    | GPL     |
   | partition                | ACTIVE   | STORAGE ENGINE     | NULL    | GPL     |
   +--------------------------+----------+--------------------+---------+---------+
   ```

2. 分区是指根据一定的规则，数据库把一个表分解成多个更小的、更容易管理的部分。就访问数据库的应用而言，逻辑上只有一个表或者一个索引，每个分区都是一个独立的对象。

3. 分区键 ： 用于根据某个区间值（或者范围值）、特定值列表或者 HASH 函数值执行数据的聚集，让数据根据规则分布在不同的分区中，让一个大对象编程一些小对象。

###分区表可用的类型

1. RANGE分区（范围分区）：是利用取值范围将数据分成分区，区间要连续并且不能互相重叠。

   ```mysql
   #雇员表 emp 中按照商店 ID store_id 进行 range 分区
   create table emp(
   	id int not null,
       ename varchar(30),
       hired date not null default '1970-01-01',
       separated date not null default '9999-12-31',
       job varchar(30) not null,
       store_id int not null
   )partition by range(store_id) (
       partition p0 values less than(10),
       partition p1 values less than(20),
       partition p2 values less than(30)
   )
   #方案 商店编号 1～9 工作的雇员 被保存在分区 p0中
   #     商店编号 10～19 工作的雇员 被保存在分区 p1中
   #     商店编号 20～29 工作的雇员 被保存在分区 p2中
   #设置对大范围
   alter table emp add partition (partition p3 values less than maxvalue)
   ```

2. LIST分区 : 是建立离散的值列表告诉数据库特定的值属于哪个分区，LIST分区在很多方面类似于 range 分区，区别在于list分区是从属于一个枚举列表的值的集合，range 分区是从属于一个连续区间值的集合。

   * MySQL 5.1 版本只支持整数类型，MySQL 5.5以后支持非整数分区

   ```mysql
   #MySQL 5.1
   create table expenses(
   	expense_date date not null,
       category int,
       amount decimal(10,3)
   )partition by list(category)(
   	partition p0 values in(3,5),
       partition p1 values in(1,10),
       partition p2 values in(4,9),
       partition p3 values in(2),
       partition p4 values in(6)
   );
   #MySQL 5.5
   create table expenses(
   	expense_date date not null,
       category varchar(30),
       amount decimal(10,3)
   )partition by list columns(category)(
   	partition p0 values in('lodging','food'),
       partition p1 values in('flights','ground transportation')
   );
   ```
   __注意 list 分区不存在类似于 values less than maxvalue 这样包含其他值在内的定义。将要匹配的任何值都必须在值列表中找得到。__

3. HASH分区

4. COLUMNS

5. KEY

6. 子分区

### 注意事项

1. 无论是哪种 MySQL 分区类型，要么分区表上没有主键 / 唯一键，要么分区表的主键 / 唯一键都必须包含分区键，也就是说不能使用主键 / 唯一键字段之外的其他字段分区。
2. 分区表的名字是不区分大小写的。
3. 可以设置分区的时候使用 `values less than maxvalue`
4. 