# 积硅步, 致千里

## 基本概念 

1. 主键

   数据库表中对储存数据对象予以唯一和完整标识的数据列或属性的组合。一个数据列只能有一个主键，且主键的取值不能缺失，即不能为空值（Null）。 ------ 维基百科

2. 外键

   * 在关系数据库中，每个数据表都是由关系来连系彼此的关系，父数据表（Parent Entity）的主键（primary key）会放在另一个数据表，当做属性以创建彼此的关系，而这个属性就是外键。 ------ 维基百科
   * 外键主要用来保证数据的完整性和一致性 。 

## 使用外键的条件

1. **两个表必须是`InnoDB`表，`MyISAM`表暂时不支持外键。**
2. 外键列必须建立了索引，MySQL 4.1.2以后的版本在建立外键时会自动创建索引，但如果在较早的版本则需要显示建立；
3. 外键关系的两个表的列必须是数据类型相似，也就是可以相互转换类型的列，比如`int`和`tinyint`可以，而`int`和`char`则不可以

## 创建外键约束

1. 外键的定义语法：

   ```mysql
   [CONSTRAINT symbol] FOREIGN KEY [id] (index_col_name, ...)
   REFERENCES tbl_name (index_col_name, ...)
   [ON DELETE {RESTRICT | CASCADE | SET NULL | NO ACTION | SET DEFAULT}]
   [ON UPDATE {RESTRICT | CASCADE | SET NULL | NO ACTION | SET DEFAULT}]
   
   该语法可以在 CREATE TABLE 和 ALTER TABLE 时使用，如果不指定CONSTRAINT symbol，MYSQL会自动生成一个名字。
   ON DELETE、ON UPDATE表示事件触发限制，可设参数：
   ① RESTRICT（限制外表中的外键改动，默认值）
   ② CASCADE（跟随外键改动）
   ③ SET NULL（设空值）
   ④ SET DEFAULT（设默认值）
   ⑤ NO ACTION（无动作，默认的）
   ```

2. 测试

   ```mysql
   表一
   create table repo_table(
   	repo_id char(13) not null primary key,
   	repo_name char(14) not null
   )
   表二
   create table reply_table(
   	reply_id char(13) not null primary key,
   	reply_com char(13) not null,
   	repo_id char(13) not null,
   	foreign key(repo_id) references repo_table(repo_id)
   )
   
   insert into repo_table values("13","ceshi13");   //success
   insert into reply_table values("1003","test foreign", "13")  //success
   insert into reply_table values("1003","test foreign", "14")   //failed
   ```

## 使用外键约束的场景

1. 外键约束使用最多的两种情况无外乎：
   * 父表更新时子表也更新，父表删除时如果子表有匹配的项，删除失败； 在外键定义中，我们使用`ON UPDATE CASCADE ON DELETE RESTRICT；`
   * 父表更新时子表也更新，父表删除时子表匹配的项也删除。 使用`ON UPDATE CASCADE ON DELETE CASCADE`。

## 外键约束的优缺点

* 优点
  1. 由数据库自身保证数据一致性，完整性，更可靠，因为程序很难100％保证数据的完整性，而用外键即使在数据库服务器当机或者出现其他问题的时候，也能够最大限度的保证数据的一致性和完整性。 
  2. 外键在一定程度上说明的业务逻辑，会使设计周到具体全面。 
* 缺点
  1. 过分强调或者说使用主键／外键会平添开发难度，导致表过多等问题 
  2. 不用外键时数据管理简单，操作方便，性能高（导入导出等操作，在insert,   update,   delete   数据的时候更快） 
* 总结
  1. 在大型系统中（性能要求不高，安全要求高），使用外键；在大型系统中（性能要求高，安全自己控制），不用外键；
  2. 用外键要适当，不能过分追求 