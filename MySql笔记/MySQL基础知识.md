## 数据库笔记整理

### 入门 

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

        1. 删除和修改的时候一定要加上where条件如果不加将会全部删除或全部修改。	
        $dsn='mysql:host=localhost;dbname=otshop2ek';  
        $username='otshop2ek';
        $passwd='6jJFPNtKxJPNAyWp';  
        $pdo=new PDO($dsn,$username,$passwd);  
        var_dump($pdo);die;

8. 导出数据库 

   1.  `mysqldump -u 用户名 -p 数据库名 > 导出的文件名`   直接导出数据库
   2.  `mysqldump -u 用户名 -p 数据库名 表名 > 导出的文件名` 导出指定数据库中的某个表。

### 提高部分 

1. 数据库的索引 

   1. 常规索引 

      * 添加  

        ```mysql
        create table 表名 (
          .......
        ,index 索引名(字段名)  
        )
        ```

      * 删除

        ```mysql
        drop index 索引名 on 表名
        ```

      * 追加

        ```mysql
        create index 索引名 on 表名(字段名)
        ```

   2. unique 唯一索引 ,与普通索引相似 , 但是索引的值必须是唯一的

      * 添加

        ```mysql
        create table 表名 (
          ........
          ,unique 索引名(字段)
          );
        ```

      * 删除

        ```mysql
        drop index 索引名 on 表名
        ```

      * 追加

        ```mysql
        create unique index 索引名 on 表名(字段名)
        ```

   3. 主键索引 (primary key) 整个表中只能有一个列为主键列

      * 添加

        ```mysql
        create table 表名(
          id int unsigned auto_increment primary key,
          .....
          );
        ```

      * 删除

        1. ```mysql
           alter table 表名 change 旧字段 新字段  类型
           ```

        2. ```mysql
           alter table 表名 drop primary key 
           ```

        __注意__ :  如果删除primary key 的列那么首先确定是否有`auto_increment ` 如果有那么要先将`auto_increment ` 去掉然后才能删除 `primary key` 

   __数据库索引的解释__

   `unique`  唯一索引( 限定字段值的唯一性 )

   1. `unique`  约束的字段中不能包含重复值 ,  可以为一个或者多个字段定义`unique` 约束
   2. `unique` 可以在字段级也可以在表级定义 
   3. `unique` 约束的字段上可以包含空值

      应用场景 :  当需要限定某个表字段每个值都是唯一的

   `primary key`  主键

   1. 数据库会自动为具有`primary key` 约束的字段建立一个唯一索引和一个not null 约束,

      相当于 primary key  =  unique + not null

    __区别__ 

   1. 唯一性约束所在的列允许为空值 ,但是主键约束所在的列不允许空值
   2. 可以把唯一性约束放在一个或多个列上,但是唯一约束所在的列并不是表的主键列
   3. 建立主键的目的是为了让外键来引用
   4. 一个表中最多只有一个主键,但是可以有很多唯一键.

2. 查看当前的默认存储引擎  

   ```mysql
   show variables like 'default_storage_engine';
   ```

   __注意：__

   1. MyISAM表不支持事务，他的优势就是访问速度快，对事务的完整性没有要求或你的程序以select、insert为主的话通常使用MyISAM存储引擎。表锁。
   2. InnoDB：提供事务处理。如果应用程序对事务的完整性要求比较高，并在除了插入和查询外还包括很多的更新、删除操作。就可以使用InnoDB。
   3. 创建表指定存储引擎    `create table 表名 (.......)engine=引擎名;` 

3. 字符集的查看

   __数据库级__

   ```mysql
   查看当前数据库的字符集      show variables like 'character_set_database';
   查看系统中可用的字符集		 show character set;

   创建数据库的时候设置字符集   create database 179lamp default character set utf8;
   ```

   __数据表级__ 

   ```mysql
   查看数据表的字符集   show create table 表名

   设置字符集 			create table 表名 (.........)charset=字符集
   ```

   __数据库的字符集的级别__   服务器级：  数据库级： 数据表级： 字段名级：

   __特点__:    这四种级有一个特点就是__本级__ 如果没有指定字符集那么将会使用__上级别__的字符集。

4. 修改表

   __表中字段操作__     格式  	  `alter table 表名 动作` 

   * 添加字段     ` alter table 表名 add 字段名 字段类型 约束条件 [first|after 字段名];` 

   * 修改字段     `alter table 表名 modify 字段名 新的类型 约束条件` 

   * 修改字段名修改字段类型、约束类型  

     ​		    `alter table 表名 change 旧字段名 新字段名  新字段类型 新字段约束条件;` 

   * 删除字段    `alter table 表名 drop 要删除的字段名` 

   * 修改表名    ` alter table 旧表名 rename as 新表名` 

   __表中数据的增、删、改__

   - 插入数据：

     - 方法1：插入指定字段：

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

     - 方法2：插入所有字段

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

     - 方法3：插入多条数据

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

     - 方法4：插入单条数据

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

     - 方法5：插入某些查询的结果。将某些查询的结果插入到表中。

       `insert into 表名 (字段1,字段名2...) select .....`

       ```mysql
       MariaDB [lamp179]> insert into user (name,sex,age) select name,sex,age from user
       ;
       Query OK, 5 rows affected (0.01 sec)
       Records: 5  Duplicates: 0  Warnings: 0
       ```

   - 修改数据

     `update 表名 set 字段名1=值1,字段名2=值2.....where 条件`

   - 删除记录

     `delete from 表名 where 条件`

     __注意：__

     1. 如果不加where条件会把表中所有的记录删除掉。
     2. 如果使用delete删除的时候有where条件最好先用select将内容查询出来看一下查出来的内容是否就是要删除的内容。
     3. 如果要删除所有的数据使用`truncate table 表名`这种会比delete快很多很多。而且很干净

   ```mysql
   select * from posts where title like '%测试%';
   select count(*),tid from posts group by tid  having tid !=4;
   select * from posts where id in(1,2);
   select * from posts limit 2;
   select * from posts limit 0,2;
   ```

   #### 练习

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

   ​

   ​