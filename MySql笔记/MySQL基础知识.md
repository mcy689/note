# MySQL数据库基础入门

## 基础的入门

1. `drop database 数据库名`        删除数据库

2. `drop table 表名`                     删除表

   __注意：__

   ```txt
   1. 千万注意不要创建表、数据库、字段的时候用特殊字符，带上反引号。
   ```

3. `select database() `                 查看当前选择的数据库

4. `show create table 表名`         查看建表语句

5. 添加数据：`insert into 表名(字段名1,字段名2....) values (值1,值2....)`

6. 修改数据    `update 表名 set 字段名1=值1,字段名2=值2.... where 条件`

7. 删除数据    `delete  from 表名 where 条件`

   __注意：__

   ```
   1. 删除和修改的时候一定要加上where条件如果不加将会全部删除或全部修改。	
   $dsn='mysql:host=localhost;dbname=otshop2ek';  
   $username='otshop2ek';
   $passwd='6jJFPNtKxJPNAyWp';  
   $pdo=new PDO($dsn,$username,$passwd);  
   var_dump($pdo);die;
   ```

8. 导出数据库 

   1. `mysqldump -u 用户名 -p 数据库名 > 导出的文件名`   直接导出数据库
   2. `mysqldump -u 用户名 -p 数据库名 表名 > 导出的文件名` 导出指定数据库中的某个表。

9. 有启迪的网址

   1. 一个关于用户表的设计相关表述,  `http://wpceo.com/user-database-table-design/`
   2. MySQL索引原理及慢查询优化 `https://tech.meituan.com/mysql-index.html`
      - 美团技术团队
   3. `https://www.cnblogs.com/kerrycode/p/6042396.html`

10. 查看缓存

   1. `show variables like "%query_cache%";` 
   2. `show status like "%Qcache%"`

## 数据库的索引 

### 常规索引（index） 

```mysql
# 添加 
create table 表名 (
  .......
,index 索引名(字段名)  
)

# 删除
drop index 索引名 on 表名

# 追加索引
create index 索引名 on 表名(字段名)
```

### 唯一索引 （unique ）

```mysql
# 添加
create table 表名 (
  ........
  ,unique 索引名(字段)
);

# 删除
drop index 索引名 on 表名

# 追加
create unique index 索引名 on 表名(字段名)
```

### 主键索引 (primary key)

```mysql
# 添加
create table 表名(
  id int unsigned auto_increment primary key,
  .....
);

# 删除
alter table 表名 change 旧字段 新字段  类型
alter table 表名 drop primary key 
```

### `unique` 和 `primary key`  区别

1. `unique 唯一索引`
   * `unique`  约束的字段中不能包含重复值 ,  可以为一个或者多个字段定义`unique` 约束
   * `unique` 可以在字段级也可以在表级定义 
   * `unique` 约束的字段上可以包含空值
2. `primary key`  主键
   * 数据库会自动为具有`primary key` 约束的字段建立一个唯一索引和一个not null 约束,
   * 相当于 primary key  =  unique + not null
3.  __区别__ 
   1. 唯一性约束所在的列允许为空值 ,但是主键约束所在的列不允许空值
   2. 可以把唯一性约束放在一个或多个列上,但是唯一约束所在的列并不是表的主键列
   3. 一个表中最多只有一个主键,但是可以有很多唯一键.

__注意__ :  如果删除primary key 的列那么首先确定是否有`auto_increment ` 如果有那么要先将`auto_increment ` 去掉然后才能删除 `primary key` 

## 存储引擎  

```mysql
# 查看默认存储引擎
show variables like 'default_storage_engine';
```

__注意：__

1. MyISAM表不支持事务，他的优势就是访问速度快，对事务的完整性没有要求或你的程序以select、insert为主的话通常使用MyISAM存储引擎。表锁。
2. InnoDB：提供事务处理。如果应用程序对事务的完整性要求比较高，并在除了插入和查询外还包括很多的更新、删除操作。就可以使用InnoDB。
3. 创建表指定存储引擎    `create table 表名 (.......)engine=引擎名;` 

## 字符集的查看

### 字符集级别

1. 服务器级：  数据库级： 数据表级： 字段名级：
2. 这四种级有一个特点就是__本级__ 如果没有指定字符集那么将会使用__上级别__的字符集。

### 数据库级

```mysql
查看当前数据库的字符集      show variables like 'character_set_database';
查看系统中可用的字符集		 show character set;
创建数据库的时候设置字符集   create database 179lamp default character set utf8;
```

### 数据表级

```mysql
查看数据表的字符集   show create table 表名
设置字符集 			create table 表名 (.........)charset=字符集
```

## 修改表 

格式   `alter table 表名 动作` 

1. 添加字段     ` alter table 表名 add 字段名 字段类型 约束条件 [first|after 字段名];` 
2. 修改字段     `alter table 表名 modify 字段名 新的类型 约束条件` 
3. 修改字段名修改字段类型、约束类型   `alter table 表名 change 旧字段名 新字段名  新字段类型 新字段约束条件;` 
4. 删除字段    `alter table 表名 drop 要删除的字段名` 
5. 修改表名    ` alter table 旧表名 rename as 新表名` 

## 表中数据的增、删、改

### 插入数据

1. 方法1：插入指定字段：

   `insert into 表名(字段1,字段名2.....) values (值1,值2.....);`

   __注意：__使用该方法如果包含空字段、非空但是有默认值、自增字段。这个时候可以不在insert后面的字段中显示出来。

   ```mysql
   MariaDB [lamp179]> desc user;
   +-------+------------------+------+-----+---------+----------------+
   | Field | Type             | Null | Key | Default | Extra          |
   +-------+------------------+------+-----+---------+----------------+
   | id    | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
   | name  | char(32)         | YES  |     | NULL    |                |
   | sex   | enum('n','v')    | YES  |     | NULL    |                |
   | age   | tinyint(4)       | YES  |     | NULL    |                |
   +-------+------------------+------+-----+---------+----------------+
   4 rows in set (0.01 sec)
   
   MariaDB [lamp179]> insert into user(name,sex,age) value ('咖啡','v',18);
   Query OK, 1 row affected (0.00 sec)
   
   MariaDB [lamp179]> select * from user;
   +----+------+------+------+
   | id | name | sex  | age  |
   +----+------+------+------+
   |  1 | 咖啡 | v    |   18 |
   +----+------+------+------+
   1 row in set (0.00 sec)
   ```

2. 插入所有字段

   `insert into 表名 values (值1,值2,.....)`

   __注意：__不指定列名的情况下values后面的顺序应该和字段的排列顺序一致。并且所有的字段都需要写上。

   ```mysql
   MariaDB [lamp179]> insert into user values(null,'haha','n',19);
   Query OK, 1 row affected (0.00 sec)
   
   MariaDB [lamp179]> select * from user;
   +----+------+------+------+
   | id | name | sex  | age  |
   +----+------+------+------+
   |  1 | 咖啡 | v    |   18 |
   |  2 | haha | n    |   19 |
   +----+------+------+------+
   2 rows in set (0.00 sec)
   ```

3. 插入多条数据

   `insert into 表名(字段名1,字段名2....) values (1值1,1值2....),(2值1,2值2...);`

   ```mysql
   MariaDB [lamp179]> insert into user (name,sex,age) values ('a','n',19),('b','v',
   20);
   Query OK, 2 rows affected (0.00 sec)
   Records: 2  Duplicates: 0  Warnings: 0
   
   MariaDB [lamp179]> select * from user;
   +----+------+------+------+
   | id | name | sex  | age  |
   +----+------+------+------+
   |  1 | 咖啡 | v    |   18 |
   |  2 | haha | n    |   19 |
   |  3 | a    | n    |   19 |
   |  4 | b    | v    |   20 |
   +----+------+------+------+
   4 rows in set (0.01 sec)
   ```

4. 插入单条数据

   `insert into 表名 set 字段名1=值1,字段名2=值2.....`

   ```mysql
   MariaDB [lamp179]> insert into user set name='yanhaijing',sex='n',age=18;
   Query OK, 1 row affected (0.01 sec)
   
   MariaDB [lamp179]> select * from user;
   +----+------------+------+------+
   | id | name       | sex  | age  |
   +----+------------+------+------+
   |  1 | 咖啡       | v    |   18 |
   |  2 | haha       | n    |   19 |
   |  3 | a          | n    |   19 |
   |  4 | b          | v    |   20 |
   |  5 | yanhaijing | n    |   18 |
   +----+------------+------+------+
   5 rows in set (0.01 sec)
   ```

5. 插入某些查询的结果。将某些查询的结果插入到表中。

   `insert into 表名 (字段1,字段名2...) select .....`

   ```mysql
   MariaDB [lamp179]> insert into user (name,sex,age) select name,sex,age from user
   ;
   Query OK, 5 rows affected (0.01 sec)
   Records: 5  Duplicates: 0  Warnings: 0
   ```

### 修改数据

`update 表名 set 字段名1=值1,字段名2=值2.....where 条件`

### 删除记录

`delete from 表名 where 条件`

__注意__ 

1. 如果不加where条件会把表中所有的记录删除掉。
2. 如果使用delete删除的时候有where条件最好先用select将内容查询出来看一下查出来的内容是否就是要删除的内容。
3. 如果要删除所有的数据使用`truncate table 表名`这种会比delete快很多很多。而且很干净

## MySQL中的运算符

### 算术运算符

```mysql
# + - * / %
select 0.1+0.33,0.1-0.33,0.1*0.33,1/2,1%2;
select MOD(3,2)
```

### 比较运算符

1. 比较结果为真，则返回1，为假则返回0，比较结果不确定则返回NULL。

2. 数字作为浮点数比较，而字符串以不区分大小写的方式进行比较。

3. `=` 运算符，null 不能用于 `=` 比较

   ```mysql
   mysql> select 1=0,1=1,null=null;
   +-----+-----+-----------+
   | 1=0 | 1=1 | null=null |
   +-----+-----+-----------+
   |   0 |   1 |      NULL |
   +-----+-----+-----------+
   ```

4. `<>` 运算符，和`=`相反，如果两侧操作数不等，则值为1，否则为0，null不能用于`<>` 比较。

   ```mysql
   mysql> select 1<>0,1<>1,null<>null;
   +------+------+------------+
   | 1<>0 | 1<>1 | null<>null |
   +------+------+------------+
   |    1 |    0 |       NULL |
   +------+------+------------+
   ```

5. `<=>` 运算符，和`=`类似，在操作数相等时值为1，不同之处在于即使操作在值为null也可以正确比较。

   ```mysql
   mysql> select 1<=>0,1<=>1,null<=>null;
   +-------+-------+-------------+
   | 1<=>0 | 1<=>1 | null<=>null |
   +-------+-------+-------------+
   |     0 |     1 |           1 |
   +-------+-------+-------------+
   ```

6. between 运算符，`a between min and max` ,当a大于等于min并且小于等于max。相当于 ( a >= min and a<=max)。

7. `regexp` 运算符 

   ```mysql
   mysql> select 'abcdef' regexp 'de';
   +----------------------+
   | 'abcdef' regexp 'de' |
   +----------------------+
   |                    1 |
   +----------------------+
   1 row in set (0.00 sec)
   ```

## 常用函数

1. concat ( ) 函数，把传入的参数连接成为一个字符串。任何字符串与 null 进行的结果都是 null 。

   ```mysql
   mysql> select concat('a','b','c');
   +---------------------+
   | concat('a','b','c') |
   +---------------------+
   | abc                 |
   +---------------------+
   1 row in set (0.00 sec)
   ```

2. insert（str,x,y,instr）函数，将字符串 str 从第 x 位置开始，y个字符长的子串替换为字符串 instr。

   ```mysql
   mysql> select insert('beijing2008',12,3,'me');
   +---------------------------------+
   | insert('beijing2008',12,3,'me') |
   +---------------------------------+
   | beijing2008me                   |
   +---------------------------------+
   1 row in set (0.00 sec)
   ```

3. lower( str ) 和 upper( str ) 把字符串转换成小写或者大写。

   ```mysql
   select lower('DDDD');
   select upper('dddd');
   ```

4. left( str,x) 和 right(str,x) 函数，分别返回字符串最左边的x个字符和最后边的x个字符。

   ```mysql
   mysql> select left('beijing2008',7);
   +-----------------------+
   | left('beijing2008',7) |
   +-----------------------+
   | beijing               |
   +-----------------------+.
   ```

## 常用的概念

### 读写锁

> 某个客户正在读取邮箱, 同时另外一个用户试图删除编号为25的邮件, 会产生什么结果? 结论是不确定, 读的客户可能会报错退出, 也可能读取到不一致的邮箱数据. 
>
> 解决这类经典问题的方法就是并发控制,在处理并发读或者写时, 可以通过实现一个由两种类型的锁组成的锁系统来解决问题.  这两种类型的锁通常被称为__共享锁( shared lock ) 和 排他锁 ( exclusive lock ), 也叫读锁 (read lock) 和 写锁 (write lock)__ 

1. __读锁__ 是共享的, 或者说是互相不阻塞的, 多个客户在同一时刻可以同时读取同一个资源, 而互不干扰
2. __写锁__ 是排他的, 也就是说一个写锁会阻塞其他的写锁和读锁

### 锁粒度

> 一种提高共享资源并发性的方式就是让锁定对象更有选择性. 尽量只锁定需要修改的部分数据, 而不是所有的资源. 更理想的方式是, 只对会修改的数据片进行精确的锁定, 任何时候, 在给定的资源上, 锁定的数据量越少, 则系统的并发程度越高, 只要相互之间不发生冲突即可
>
> 
>
> 锁的操作 : 获得锁, 检查锁是否已经解除, 释放锁等
>
> 
>
> 锁策略, 就是在锁的开销和数据的安全性之间寻求平衡

1. 表锁 ( table lock)
   - 表锁是MySQL中最基本的锁策略, 并且是开销最小的策略, 它会锁定整张表, 一个用户在对表进行写操作( 插入, 删除, 更新 ) 前, 需要先获得写锁, 这会阻塞其他用户对该表的所有读写操作, 只有没有写锁是, 其他读取的用户才能获得读锁, __读锁之间是不相互阻塞的__ 
   - 写锁比读锁有更高的优先级, 因此一个写锁请求可能会被插入到读锁队列的前面
2. 行级锁 ( row lock )
   - 行级锁可以最大程度地支持并发处理 ( 同时也带来了最大的锁开销 )
   - 行级锁只在存储引擎实现

### 事务

1. __事务__ 就是一组原子性的SQL查询, 事务内的语句, 要么全部执行成功, 要么全部执行失败

2. __ACID测试__  : 原子性( atomicity)， 一致性( consistency )， 隔离性 ( isolation ) 和 持久性 ( durability)。 一个运行良好的事务处理系统， 必须具备这些标准特征。

   - 原子性

     一个事务必须被视为一个不可分割的最小工作单元, 整个事务中的所有操作要么全部提交成功, 要么全部失败回滚, 对于一个事务来说, 不可能只执行其中的一部分操作

   - 一致性

     数据库总是从一个一致性的状态转换到另外一个一致性的状态。在事务的执行过程中系统出现崩溃， 数据库中的数据也不会损失，因为事务最终没有提交，所以事务中所做的修改也不会保存到数据库中

   - 隔离性

     一个事务所做的修改在最终提交以前，对其他事务时不可见的。

   - 持久性

     一旦事务提交， 则其所做的修改就会永久保存在数据库中，此时即使系统崩溃，修改的数据也不会丢失。持久性是个优点模糊的概念， 实际上持久性分很多不同的级别。

3. 用户可以根据业务是否需要事务处理， 来选择合适的存储引擎。对于一些不需要食物的查询类应用， 选择一个非事务型的存储引擎，可以活得更高的性能。

### 事务的隔离级别

1. read uncommitted (未提交读)

   事务中的修改，即使没有提交，对其他事务也都是可见的。事务可以读取未提交的数据，这也被称为__脏读（Dirty Read）__ 。在实际应用中一般很少使用

2. read committed ( 提交读 )

   __大多数数据系统的默认隔离级别都是这个级别（但是MySQL不是）__。read committed 满足： 一个事务从开始直到提交之前，所做的任何修改对其他事务不可见的，这个级别有时候也叫做不可重复读，因为两次执行同样的查询，可能会得到不一样的结果。

3. repeatable read （可重复读）

   - REPEATABLE READ 解决了脏读的问题， 该级别保证了在同一个事务中多次读取同样记录的结果是一致的。但是理论上，可重复读隔离级别还是无法解决另外一个幻读（Phantom Read）的问题。

   - __所谓幻读__ 幻读是指当事务不是独立执行时发生的一种现象，例如第一个事务对一个表中的数据进行了修改，比如这种修改涉及到表中的“全部数据行”。同时，第二个事务也修改这个表中的数据，这种修改是向表中插入“一行新数据”。那么，以后就会发生操作第一个事务的用户发现表中还存在没有修改的数据行，就好象发生了幻觉一样 

     __指得是当某个事物在读取某个范围内的记录时， 会产生幻行（Phantom Row）。InnoDB存储引擎解决了幻读的问题__ 

4. serializable （可串行化）

   SERIALIZABLE 是最高的隔离级别。它通过强制事务串行执行，避免了前面说的幻读的问题。简单的说，SERIALIZABLE会在读取的每一行数据上都加锁， 所以可能导致大量的超时和锁争用的问题。实际应用中也很少用到这个隔离级别

   | 隔离级别         | 脏读可能性 | 不可重复读可能性 | 幻读可能性 | 加锁读 |
   | ---------------- | ---------- | ---------------- | ---------- | ------ |
   | READ UNCOMMITTED | yes        | yes              | yes        | no     |
   | READ COMMITTED   | no         | yes              | yes        | no     |
   | REPEATABLE READ  | no         | no               | yes        | no     |
   | SERIALIZABLE     | no         | no               | no         | yes    |

### 死锁

死锁是指两个或者多个事务在同一资源上相互占用， 并请求锁定对方占用的资源， 从而导致恶性循环的现象，当多个的事务视图以不同的顺序锁定资源时，就可能会产生死锁。多个事务同时锁定同一个资源时，也会产生死锁。

> 事务1
>
> ​	START TRANSACTION;
>
> ​	update stockprice set close = 45.50 where stock_id = 4 and date = '2018-5-1';
>
> ​	update stockprice set close = 45.50 where stock_id = 3 and date = '2018-5-1';
>
> ​	COMMIT;
>
> 事务2
>
> ​	START TRANSACTION;
>
> ​	update stockprice set close = 45.50 where stock_id = 3 and date = '2018-5-1';
>
> ​	update stockprice set close = 45.50 where stock_id = 4 and date = '2018-5-1';
>
> ​	COMMIT;
>
> 如果凑巧，两个事务都执行了第一条UPDATE语句，更新了一行数据，同时也锁定了该行数据，接着每个事务都尝试去执行第二条UPDATE语句，却发现该行已经被对方锁定，然后两个事务都等待对方释放锁，同时又持有对方需要的锁，这陷入死循环。除非有外部因素介入才可能解除死锁。

### 事务日志

事务日志可以帮助提高事务的效率。使用事务日志，存储引擎在修改表的数据时只需要修改其内存拷贝，再把该修改行为记录到持久在硬盘上的事务日志中，而不用每次都将修改的数据本身持久到磁盘。事务日志采用的是追加的方式，因此写日志的操作是磁盘上一小块区域内的顺序 I/O，而不像随机 I/O需要在磁盘的说个地方移动磁头。所以采用事务日志的方式相对来说要快得多。

### MySQL中的事务

MySQL默认采用自动提交（autocommit）模式。也就是说，如果不是显示地开始一个事务，则每个查询都被当作一个事务执行提交操作。

```mysql
mysql> show variables like 'autocommit';
+---------------+-------+
| Variable_name | Value |
+---------------+-------+
| autocommit    | ON    |
+---------------+-------+
1 row in set (0.00 sec)

set session transaction isolation level read committed;
```

### 隐式和显式锁定

InnoDB采用的是两阶段锁定协议。在事务执行过程中，随时都可以执行锁定，锁只有在执行COMMIT或者ROLLBACK的时候才会释放，并且所有的锁是在同一时刻被释放。__隐式锁定__ 

InnoDB还支持通过特定的语句进行__显式锁定__

```mysql
select ... lock in share mode
select ... for update
```

__应用已经将表从MyISAM转换到InnnoDB，但还是显式地使用LOCK TABLES语句。这是没有必要，还会严重影响性能，实际上InnoDB的行级锁工作得更好__

### 乐观锁

1. 乐观锁不是数据库自带的，需要我们自己去实现。乐观锁是指操作数据库时(更新操作)，想法很乐观，认为这次的操作不会导致冲突，在操作数据时，并不进行任何其他的特殊处理（也就是不加锁），而在进行更新后，再去判断是否有冲突了。

2. 通常实现是这样的：在表中的数据进行操作时(更新)，先给数据表加一个版本(version)字段，每操作一次，将那条记录的版本号加1。也就是先查询出那条记录，获取出version字段,如果要对那条记录进行操作(更新),则先判断此刻version的值是否与刚刚查询出来时的version的值相等，如果相等，则说明这段期间，没有其他程序对其进行操作，则可以执行更新，将version字段的值加1；如果更新时发现此刻的version值与刚刚获取出来的version的值不相等，则说明这段期间已经有其他程序对其进行操作了，则不进行更新操作。

   ```php
   function dummy_business_5() {
       for ($i = 0; $i < 1000; $i++) {
           $connection=Yii::app()->db;
           $transaction = $connection->beginTransaction();
           try {
               $model = Test::model()->findByPk(1);
               $num = $model->num + 1;
               $version = $model->version;
   
               $sql = 'UPDATE {{test}} SET num = '.$num.' + 1, version = version + 1 WHERE id = 1 AND version = '.$version;
               $ok = $connection->createCommand($sql)->execute();
               if(!$ok) {
                   $transaction->rollback(); //如果操作失败, 数据回滚
                   $i--;
                   continue;
               } else {
                   $transaction->commit(); //提交事务会真正的执行数据库操作
               }
           } catch (Exception $e) {
               $transaction->rollback(); //如果操作失败, 数据回滚
           }
       }
   }
   ```

### 悲观锁

1. 与乐观锁相对应的就是悲观锁了。悲观锁就是在操作数据时，认为此操作会出现数据冲突，所以在进行每次操作时都要通过获取锁才能进行对相同数据的操作，所以悲观锁需要耗费较多的时间。另外与乐观锁相对应的，悲观锁是由数据库自己实现了的，要用的时候，我们直接调用数据库的相关语句就可以了。
2. 悲观锁涉及到的另外两个锁概念就出来了，它们就是共享锁与排它锁。共享锁和排它锁是悲观锁的不同的实现，它俩都属于悲观锁的范畴。

## 数据库练习

```mysql
1. 查询Student表中的所有记录的Sname、Ssex和Class列。
   select Sname,Ssex,Class from student
2. 查询教师所有的单位即不重复的Depart列
   select depart from teacher group by depart;
3. 查询Student表的所有记录。
   select * from student;
4. 以Class降序查询Student表的所有记录
   select * from student order by class desc;
5. 查询Student表中“95031”班或性别为“女”的同学记录。
   select * from Score where class = '95031' or Ssex = '女';
6. 查询Score表中成绩为85，86或88的记录。
   select * from Score where Degree in(85,86,88);
7. 查询Score表中成绩在60到80之间的所有记录。
   select *  from Score where Degree between 60 and 80
8. 以Cno升序、Degree降序查询Score表的所有记录。
   select * from Score order by Cno asc,Degree desc;
9. 查询“95031”班的学生人数。
   select count(*) from student where class = "95031"
1o. 查询Score表中的最高分的学生学号和课程号。（子查询或者排序)
    select sno,cno from Score order by degree desc limit 1;
	select sno,cno from Score where degree = (select max(degree) from Score);
11. 查询每门课的平均成绩。
	select Con,avg(degree) from Score group by Cno;
12. 查询Score表中至少有5名学生选修的并以3开头的课程的平均分数
    select avg(degree) from score where Cno like '3%' and Cno in ( select Cno from score group by Cno having count(*) >=5);   用in 不用= 是因为可能会有多个
13. 查询分数大于70，小于90的Sno列。
    select Sno from Score where degree>70 and degree<90
14、查询所有学生的Sname、Cno和Degree列。
    select  Sname, Cno,Degree from Score , student where Score.Sno=student.Sno
15. 查询所有学生的Sno、Cname和Degree列。
    select  Sno,Cname,Degree from Score , Course where Score.Cno=Course.Cno
16. 查询“95033”班学生的平均分。
    select avg(degree) as 'class=95033' from Score where Sno in (select Sno from     Student where Class='95033' )
17. 假设使用如下命令建立了一个grade表：
    create table grade(low  int(3),upp  int(3),rank  char(1))
    insert into grade values(90,100,’A’)
    insert into grade values(80,89,’B’)
    insert into grade values(70,79,’C’)
    insert into grade values(60,69,’D’)
    insert into grade values(0,59,’E’)
    现查询所有同学的Sno、Cno和rank列。
    select Sno,Cno,rank from Score,grade where degree between low and upp
18. 查询成绩高于学号为“109”、课程号为“3-105”的成绩的所有记录。
    Select * from score where degree > (select max(degree) from Score where Sno='109' and Cno='3-105' );
19. 查询和学号为108、101的同学同年出生的所有学生的Sno、Sname和Sbirthday列。
    select sno,sname,sbirthday  from student where year(sbirthday) = (select    year(sbirthday) from student where  sno='108')
20. 查询“张旭“教师任课的学生成绩。
   (1) select sno, degree from score ,course where score.cno = course.cno and course.Tno = (select Tno from Teacher where Tname = '张旭');
   (2)select Tname from Teacher where tno=(select Tno from Course where cno = (select Cno from Score group by cno having count(*) > 5));
21. 查询95033班和95031班全体学生的记录。
    select * from  student where  class in ('95033','95031')
22. 查询出“计算机系“教师所教课程的成绩表。
	 select sno,score.cno,score.degree from score,course where score.cno = course.cno and course.tno in (select tno from teacher where depart = '计算机系');
23. 查询所有教师和同学的name、sex和birthday
  select tname ,tsex,tbirthday from teacher where tsex='女' union select sname ,ssex, sbirthday from student ssex='女';
24. 查询所有未讲课的教师的Tname和Depart.
   select Tname,Depart from Teacher where Tno not in (select Tno from Course where cno in (select cno from score))
25. 查询至少有2名男生的班号。
   select class from student where ssex='男' group by class having count(*)>1
26. 查询Student表中不姓“王”的同学记录。
   select * from Student where Sname not  like '王%%'
27. 查询Student表中每个学生的姓名和年龄。
    select Sname, year(now())-year(sbirthday)  from Student
28. 查询Student表中最大和最小的Sbirthday日期值。
    select Max(Sbirthday ),Min(Sbirthday ) from Student
29.、以班号和年龄从大到小的顺序查询Student表中的全部记录。
    select * from Student  order by  class desc, Sbirthda asc
30. 查询“男”教师及其所上的课程。
    select Tname,Cname from course,teacher where course.tno= teacher.tno and teacher.Tsex='男'
31. 查询最高分同学的Sno、Cno和Degree列。
   select Sno,Cno,Degree from score where degree=(select max(degree) from score)
  排序写入的
    select sno,cno,degree from score order by degree desc limit 0,1;
32. 查询和“李军”同性别并同班的同学Sname.
    select Sname from Student where Ssex = (select Ssex from Student where Sname='李军' ) and class=( select class from student where Sname='李军')
33. 查询所有选修“计算机导论”课程的“男”同学的成绩表。
    select  Sno,Cno,degree from score where Cno=( select Cno from course where Cname='计算机导论') and Sno in (select Sno from student where Ssex='男')
```

