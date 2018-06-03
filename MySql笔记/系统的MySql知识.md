#####如何选择合适的数据类型

1. char 和 varchar

   * char 属于固定长度的字符串类型, 而varchar属于可变长度的字符串类型

     ```mysql
     #区别对比
       insert into test1(username,username1) values('abc ','abc ');
       SELECT  concat(username, '+'),concat(username1, '+') FROM test1

       +-----------------------+------------------------+
       | concat(username, '+') | concat(username1, '+') |
       +-----------------------+------------------------+
       | +                     |    +                   |
       | +                     |    +                   |
       | +                     |     +                  |
       | abcd+                 | abcd+                  |
       | abc+                  | abc +                  |
       +-----------------------+------------------------+

       mysql> show create table test1 \G
       *************************** 1. row ***************************
              Table: test1
       Create Table: CREATE TABLE `test1` (
         `username` char(4) DEFAULT NULL,
         `username1` varchar(4) DEFAULT NULL
       ) ENGINE=MyISAM DEFAULT CHARSET=utf8
       1 row in set (0.00 sec)
       
       concat(); 这个函数是连接多个表达式
     ```

   * 检索时从char列删除了尾部的空格

   * 对于那些长度变化不大并且对查询速度有较高要求的数据可以考虑使用char类型来存储

   * char存储需求是固定长度的

   * 根据不同的存储引擎如下

     * MyISAM 存储引擎 ： 建议使用固定长度的数据列代表可变长度的数据列
     * InnoDB 存储引擎：建议使用varchar类型

2. TEXT 和BLOB

   * 二者的主要差别是BLOB能用来保存二进制数据, 比如照片

     ```mysql
     TEXT 
     	TEXT
     	MEDIUMTEXT
     	LONGTEXT
     BLOB
     	BLOB
     	MEDIUMBLOB
     	LONGBLOB
     ```

   * 使用这两个类型常见问题

     * BLOB 和 TEXT 值会引起一些性能问题, 特别是在执行了大量的删除操作时, 删除操作会在数据表中留下很大的_空洞_ ,以后在填入这些空洞的记录在插入的性能上会有影响, 为了提高性能, 建议定期使用 `OPTIMIZE TABLE` 对这类表进行碎片整理, 避免因为空洞导致的性能问题

       ```mysql
       insert into t values(6,repeat('haha',100))
       insert into t select * from t
       delete from t where id = 6
       定期的执行这个命令
           mysql> optimize table t;
           +---------+----------+----------+----------+
           | Table   | Op       | Msg_type | Msg_text |
           +---------+----------+----------+----------+
           | ceshi.t | optimize | status   | OK       |
           +---------+----------+----------+----------+
       ```

       ​

3. 视图

   * 视图 是一种虚拟存在的表, 对于使用视图的用户来说基本上是透明的, 视图并不在数据库中实际存在, 行和列数据来自定义视图的查询中使用的表, 并且是在使用视图时动态生成的. 
   * 作用
     * 简单: 使用视图的用户完全不需要关心后面对应的表的结构, 关联条件和筛选条件对用户来说已经是过滤好的复合条件的结果集
     * 安全: 使用视图的用户只能访问他们被允许查询的结果集, 对表的权限管理并不能限制到某个行某个列, 但是通过视图就可以简单地实现
     * 数据独立: 一旦视图的结构确定了, 可以屏蔽表结构变化对用户的影响, 源表增加列对视图没有影响, 源表修改列名, 则可以通过修改视图来解决, 不会造成对访问者的影响

4. 存储过程和函数

   * 存储过程和函数是事先经过编译并存储在数据库中的一段SQL语句的集合, 调用存储过程和函数可以简化应用开发人员的很多工作, 减少数据库和应用服务器之间的传输, 对于提高数据处理的效率 是有好处的. 

   * mysql 的存储过程和函数中允许包含DDl语句, 也允许在存储过程中执行__提交__, 或者__回滚__ , 但是存储过程和函数中不允许执行load data infile 语句, 存储过程和函数__可以调用其他的过程或者函数__ 

   * 具体操作

     ```mysql
     create procedure 存储过程名(参数)
     create function  存储过程名(参数)
     #存储过程创建的格式
     mysql> DELIMITER //  
     mysql> CREATE PROCEDURE proc1(OUT s int)  
         -> BEGIN 
         -> SELECT COUNT(*) INTO s FROM user;  
         -> END 
         -> //  
     mysql> DELIMITER ;

     #这个函数是用来检查film_id 和store_id 对应的inventory是否满足要求, 并且返回满足要求的inventory_id 以及满足要求的记录数
         delimiter //
         create procedure film_in_stock(in p_film_id int, in p_store_id int, out p_film_count int)
         reads sql data
         begin
           select inventory_id
           from inventory
           where film_id = p_film_id
           and store_id = p_store_id
           and invetory_int_stock(inventory_id);
           select found_rows() into p_film_count;
         end //
         delimiter ;
     #调用
     	call film_in_stock(2,2,@a);
     ```

     >（1）这里需要注意的是DELIMITER//和DELIMITER;两句，DELIMITER是分割符的意思，因为MySQL默认以”;”为分隔 符，如果我们没有声明分割符，那么编译器会把存储过程当成SQL语句进行处理，则存储过程的编译过程会报错，所以要事先用DELIMITER关键字申明当 前段分隔符，这样MySQL才会将”;”当做存储过程中的代码，不会执行这些代码，用完了之后要把分隔符还原。 
     >（2）存储过程根据需要可能会有输入、输出、输入输出参数，这里有一个输出参数s，类型是int型，如果有多个参数用”,”分割开。 
     >（3）过程体的开始与结束使用BEGIN与END进行标识。 

   * 查看存储过程或者函数

     ```mysql
     #查看存储过程或者函数的状态
       mysql> show procedure status like 'demo' \G
       *************************** 1. row ***************
                         Db: ceshi
                       Name: demo
                       Type: PROCEDURE
                    Definer: root@localhost
                   Modified: 2018-01-14 20:17:29
                    Created: 2018-01-14 20:17:29
              Security_type: DEFINER
                    Comment:
       character_set_client: gbk
       collation_connection: gbk_chinese_ci
         Database Collation: utf8_general_ci
       1 row in set (0.07 sec)
      #查看存储过程或者函数的定义
       mysql> show create procedure demo \G
       *************************** 1. row ***************************
                  Procedure: demo
                   sql_mode: NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION
           Create Procedure: CREATE DEFINER=`root`@`localhost` PROCEDURE `demo`(in p_in int)
       begin
       select p_in;
       set p_in=2;
       select p_in;
       end
       character_set_client: gbk
       collation_connection: gbk_chinese_ci
         Database Collation: utf8_general_ci
       1 row in set (0.00 sec)
      #通过查看information_schema.Routines 了解存储过程和函数的信息
     MySQL [information_schema]>select * from routines where routine_name = 'demo' \G;
     *************************** 1. row ***************************
                SPECIFIC_NAME: demo
              ROUTINE_CATALOG: def
               ROUTINE_SCHEMA: ceshi
                 ROUTINE_NAME: demo
                 ROUTINE_TYPE: PROCEDURE
                    DATA_TYPE:
     CHARACTER_MAXIMUM_LENGTH: NULL
       CHARACTER_OCTET_LENGTH: NULL
            NUMERIC_PRECISION: NULL
                NUMERIC_SCALE: NULL
           CHARACTER_SET_NAME: NULL
               COLLATION_NAME: NULL
               DTD_IDENTIFIER: NULL
                 ROUTINE_BODY: SQL
           ROUTINE_DEFINITION: begin
     select p_in;
     set p_in=2;
     select p_in;
     end
                EXTERNAL_NAME: NULL
            EXTERNAL_LANGUAGE: NULL
              PARAMETER_STYLE: SQL
             IS_DETERMINISTIC: NO
              SQL_DATA_ACCESS: CONTAINS SQL
                     SQL_PATH: NULL
                SECURITY_TYPE: DEFINER
                      CREATED: 2018-01-14 20:17:29
                 LAST_ALTERED: 2018-01-14 20:17:29
                     SQL_MODE: NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION
              ROUTINE_COMMENT:
                      DEFINER: root@localhost
         CHARACTER_SET_CLIENT: gbk
         COLLATION_CONNECTION: gbk_chinese_ci
           DATABASE_COLLATION: utf8_general_ci
     1 row in set (0.01 sec)
     ```

5. 变量的使用

   * 变量的定义

     通过`declare`可以定义一个局部变量, 该变量的作用范围只能在`begin ...... end` 块中, 可以用在嵌套的块中, 变量的定义必须写在符合语句的开头, 并且在任何其他语句的前面. 可以一次声明多个相同类型的变量, 可以使用default赋值默认值

     ```mysql
     DECLARE variable_name [,variable_name...] datatype [DEFAULT value];
     #datatype 为MySQL的数据类型 如 int, float, varchar
     declare L_int int unsigned default 5000;
     ```

   * 变量的赋值( 变量的赋值可以直接赋值, 或者通过查询赋值 )

     1. 直接复制使用`set` ,可以赋值常量或者赋表达式`set var_name = expr`

     2. 通过查询将结果赋给变量, 这要求查询返回的结果必须只有一行

        ```mysql
        create function get_customer_balance(p_customer_id int, p_effective_date datetime) returns decimal(5,2) deterministic reads sql data begin
            -> declare v_payments decimal(5,2);
            -> select ifnull(sum(payment.amount),0) into v_payments from payment where payment.payment_data <= p_effective_data and payment.customer_id
            -> = p_customer_id;
            -> reture v_rentfees = v_overfees - v_payments;
            -> end//
        ```

   * ​

   ​