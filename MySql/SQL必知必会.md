1. 模式（schema）关于数据库合表的布局及特性的信息。

2. `order by` 子句的位置，应保证它是 select 语句中最后一条子句。

3. where 子句都可以用 having 来替代。唯一的差别是 where 过滤行，而 having 过滤分组。

   * where 在数据分组前过滤，having 在数据分组后进行过滤。

4. select 子句及其顺序

   * select
   * from
   * where
   * group by
   * having
   * order by

5. 将一个表的内容复制到一个全新的表。

   ```sql
   -- 将数据复制到新创建的表中
   create table yoshop_order_test as select * from yoshop_order;
   
   -- 将一个表的内容复制到一个表中。
   insert INTO yoshop_order_test SELECT * FROM yoshop_order
   ```


## 视图

```sql
-- 创建视图
CREATE VIEW test_order AS SELECT
  o.order_no,
  o.channel_id,
  s.`name` 
FROM
	yoshop_order_test o,
	yoshop_shop s 
WHERE
	o.shop_id = s.shop_id
	
-- 删除视图
drop view test_order
```

## 复制一个表

```sql
-- 此种方式在将表B复制到A时候回将表B完整的字段结构和索引复制到表A中来。
	create table A like B
```



