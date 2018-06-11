## 积硅步, 致千里

###explain 输出字段解释

|     Column     |            含义            |
| :------------: | :------------------------: |
|       id       |          查询序号          |
|  select_type   |          查询类型          |
|     table      |            表名            |
|   partitions   |         匹配的分区         |
|      type      |          join类型          |
| prossible_keys |      可能会选择的索引      |
|      key       |       实际选择的索引       |
|    key_len     |         索引的长度         |
|      ref       |      与索引作比较的列      |
|      rows      |   要检索的函数（估算值）   |
|    filtered    | 查询条件郭磊的行数的百分比 |
|     extra      |          额外信息          |

### id

select的标识符。整个查询语句中每个select的序列号。id越大的SELECT最先被执行，对于id相同的记录，顺序由上往下。若此行引用的是其他行UNION的结果，则id值为NULL。 

### select_type

| select_type          | 类型说明                                             |
| -------------------- | ---------------------------------------------------- |
| SIMPLE               | 简单SELECT(不使用UNION或子查询)                      |
| PRIMARY              | 最外层的SELECT                                       |
| UNION                | UNION中第二个或之后的SELECT语句                      |
| DEPENDENT UNION      | UNION中第二个或之后的SELECT语句取决于外面的查询      |
| UNION RESULT         | UNION的结果                                          |
| SUBQUERY             | 子查询中的第一个SELECT                               |
| DEPENDENT SUBQUERY   | 子查询中的第一个SELECT, 取决于外面的查询             |
| DERIVED              | 衍生表(FROM子句中的子查询)                           |
| MATERIALIZED         | 物化子查询                                           |
| UNCACHEABLE SUBQUERY | 结果集无法缓存的子查询，必须重新评估外部查询的每一行 |
| UNCACHEABLE UNION    | UNION中第二个或之后的SELECT，属于无法缓存的子查询    |

### partitions

此查询匹配到的分区。只有在PARTITIONS关键字被使用的时候此字段会显示。若表没有分区则值为NULL。

### type

联接类型，下面详细介绍各种join类型，顺序为从最优类型到最差类型：

1. #### const

   1. 最多只有一行记录匹配，它将在查询开始时被读取。由于仅有一行记录匹配，所以此条记录的列值可被优化器视为常数。因为只读取一次，所以const表很快。
   2. 当`联合主键或唯一索引的所有字段`跟常量值比较时，join类型为const。在下面的查询中，tlb_name可以被用作const表：

   ```mysql
   explain SELECT
   	a.uid
   FROM
     otcms_shop_user a                     //const
   JOIN otcms_user b ON b.id = a.uid       //const
   Join otcms_goods d on d.shop_id = a.id  //ref
   AND a.uid = 69;
   ```

2. #### eq_ref

   多表join时，对于来自前面表的每一行，在当前表中`只能找到一行`。这可能是除了system和const之外最好的类型。当主键或唯一非NULL索引的所有字段都被用作join联接时会使用此类型。

   ```mysql
   SELECT * FROM ref_table,other_table
     WHERE ref_table.key_column=other_table.column;
   
   SELECT * FROM ref_table,other_table
     WHERE ref_table.key_column_part1=other_table.column
     AND ref_table.key_column_part2=1;
   ```

3. #### ref

   对于来自前面表的每一行，在此表的索引中`可以匹配到多行`。若联接只用到索引的最左前缀或索引不是主键或唯一索引时，使用ref类型（也就是说，此联接能够匹配多行记录）。

   ```mysql
   SELECT * FROM ref_table WHERE key_column=expr;
   
   SELECT * FROM ref_table,other_table
     WHERE ref_table.key_column=other_table.column;
   
   SELECT * FROM ref_table,other_table
     WHERE ref_table.key_column_part1=other_table.column
     AND ref_table.key_column_part2=1;
   ```

4. #### fulltext

   在用到全文索引时会使用此类型。

5. #### ref_or_null

   除了MySQL会额外查询包含NULL值的行，此类型跟ref一样。此类型常用在解析子查询的时候。在如下的例子中，MySQL使用ref_or_null类型：

   ```mysql
   SELECT * FROM ref_table
     WHERE key_column=expr OR key_column IS NULL;
   ```

6. #### unique_subquery

   对于如下形式的IN子查询，使用此类型替换eq_ref类型

   ```mysql
   value IN (SELECT primary_key FROM single_table WHERE some_expr)
   ```

7. #### index_subquery

   跟unique_subquery类似。可以替代IN子查询，但是它适用于如下形式子查询中的非唯一索引： 

   ```mysql
   value IN (SELECT key_column FROM single_table WHERE some_expr)
   ```

8. #### range

   使用索引查询记录，只取回指定范围的行。key列显示使用了哪个索引，key_len列包含了使用了此索引的长度。此类型下的ref列值为NULL。

   当索引列使用=, <>, >, >=, <, <=, IS NULL, <=>, BETWEEN, IN()操作符与常量作比较时，会用到range类型。

   ```mysql
   SELECT * FROM tbl_name WHERE key_column = 10;
   SELECT * FROM tbl_name WHERE key_column BETWEEN 10 and 20;
   SELECT * FROM tbl_name WHERE key_column IN (10,20,30);
   SELECT * FROM tbl_name
     WHERE key_part1 = 10 AND key_part2 IN (10,20,30);
   ```

9. #### index

   除了扫描的是索引树，此类型跟ALL类型相同。__仅通过扫描索引树__ 就能得到查询所需的数据。这种情况下，Extra列会显示_Using index_。仅扫描索引树比ALL类型更快的原因：索引数据通常比表的数据小。

   ```mysql
   CREATE TABLE `store_location` (
     `store_id` int(11) NOT NULL DEFAULT '0',
     `store_name` varchar(30) DEFAULT NULL,
     `province_id` int(11) DEFAULT NULL,
     `city_id` int(11) DEFAULT NULL,
     `district_id` int(11) DEFAULT NULL,
     PRIMARY KEY (`store_id`),
     KEY `idx_location` (`province_id`,`city_id`,`district_id`)
   ) ENGINE=InnoDB DEFAULT CHARSET=latin1
   
   insert into store_location values(1,'adidas',110,230,560);
   insert into store_location values(2,'nike',111,231,561);
   insert into store_location values(3,'new banlace',112,232,562);
   insert into store_location values(4,'puma',113,233,563);
   
   mysql> explain select province_id,city_id,district_id from store_location where city_id > 231;
   +----+-------------+----------------+-------+---------------+--------------+---------+------+------+--------------------------+
   | id | select_type | table          | type  | possible_keys | key          | key_len | ref  | rows | Extra                    |
   +----+-------------+----------------+-------+---------------+--------------+---------+------+------+--------------------------+
   |  1 | SIMPLE      | store_location | index | NULL          | idx_location | 15      | NULL |    4 | Using where; Using index |
   +----+-------------+----------------+-------+---------------+--------------+---------+------+------+--------------------------+
   1 row in set (0.00 sec)
   ```

10. #### ALL

    即全表扫面，性能最差。可以通过适当添加索引来避免出现ALL。

### prossible_keys

1. 指出MySQL可以使用哪些索引从表中查询记录。这些索引没有优先级之分。
2. 如果该列为NULL，则没有相应的索引。在这种情况下，需要检查WHERE条件引用的字段是否加上了合适的索引。如果没有建立合适的索引，创建适当的索引并使用explain来评估此查询。
3. 可以通过 `SHOW INDEX FROM table_name` 查看表中的索引。

### key

MySQL实际使用的索引。key的取值也可能不在prossible_keys中。

### key_len

1. 被选中的索引的长度（单位：Byte）。若key列为NULL，则key列值为NULL。注意：通过key_len的值可以确定实际使用了联合索引的哪些部分。
2. 关于key_len值的计算，可以查看此文章：http://imysql.com/2015/10/20/mysql-faq-key-len-in-explain.shtml

### ref

使用哪些列或常量跟key的值作比较来查询记录。若值为func，那么此值使用的是函数的结果。在EXPLAIN EXTENDED 语句之后使用 SHOW WARNINGS 可以查看使用的是哪个函数。

### rows

执行查询时需要检查的行数。对于InnoDB表，这是一个估算值，结果可能并不准确。

