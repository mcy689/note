## Mongodb(版本 3.6)

### 概要

1. 索引通常能够极大的提高查询的效率，如果没有索引，MongoDB在读取数据时必须扫描集合中的每个文件并选取那些符合查询条件的记录。
2. 这种扫描全集合的查询效率是非常低的，特别在处理大量的数据时，查询可以要花费几十秒甚至几分钟，这对网站的性能是非常致命的。
3. 索引是特殊的数据结构，索引存储在一个易于遍历读取的数据集合中，索引是对数据库表中一列或多列的值进行排序的一种结构

### 创建索引

1. 测试没加索引的性能

   ```javascript
   //准备数据
       for (var i = 0; i < 20000;i++) {
           db.books.insert({number:i,name:i+"book"});
       }
   //计算查询需要的时间
       var start = new Date();
       db.books.find({number:165871});
       var end = new Date();
       end - start   //150
   ```

2. MongoDB使用 createIndex() 方法来创建索引。

   > 注意在 3.0.0 版本前创建索引方法为 db.collection.ensureIndex()，之后的版本使用了 db.collection.createIndex() 方法，ensureIndex() 还能用，但只是 createIndex() 的别名。

   * 语法

     createIndex()方法基本语法格式如下所示：

     ```javascript
     >db.collection.createIndex(keys, options)
     ```

     __语法中 Key 值为你要创建的索引字段，1 为指定按升序创建索引，如果你想按降序来创建索引指定为 -1 即可。__ 

   * 实例

     ```javascript
     >db.col.createIndex({"title":1})
     ```

   * createIndex() 方法中你也可以设置使用多个字段创建索引（关系型数据库中称作复合索引）。

     ```javascript
     db.collection.createIndex({"title":1,"description":-1},{name:"title_desc"})
     ```

3. 索引的使用需要注意的地方

   * 创建索引的时候注意 1 是正序创建索引 -1 是倒序创建索引

   * 索引的创建在提高查询性能的同时会影响插入的性能

     对于经常查询少插入的文档可以考虑用索引

   * 复合索引要注意索引的先后顺序

   * 在做排序查询的时候也可以加上索引

### 索引的名称

```javascript
db.collection.createIndex({"title":1,"description":-1},{name:"title_desc"})
```

### 创建唯一索引

```javascript
db.collection.createIndex({"number":1},{"unique":true});
```

### 强制指定 使用那个索引

```javascript
db.books.find({"name":"0book"}).hint(name:-1);
```

### 参考网址

`http://pythonfans.lofter.com/post/3dd906_154c5bd`

