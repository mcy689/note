## MongoDB

### Count  统计数量

`db.users.find({"name":"robin"}).count();`

### Distinct  去重

`db.runCommand({"distinct":"users",key:"name"}).values;`

### 固定集合

1. MongoDB 固定集合（Capped Collections）是性能出色且有着固定大小的集合，对于大小固定，我们可以想象其就像一个环形队列，当集合空间用完后，再插入的元素就会覆盖最初始的头部的元素！ 

2. 适用场景     日志文件

3. 创建一个固定集合

   * 我们通过createCollection来创建一个固定集合，且capped选项设置为true：

   ```
   >db.createCollection("cappedLogCollection",{capped:true,size:10000})
   ```

   * 还可以指定文档个数,加上max:1000属性：

   ```
   >db.createCollection("cappedLogCollection",{capped:true,size:10000,max:1000})
   ```

   * 判断集合是否为固定集合:

   ```
   >db.cappedLogCollection.isCapped()
   ```

   * 如果需要将已存在的集合转换为固定集合可以使用以下命令：

   ```
   >db.runCommand({"convertToCapped":"posts",size:10000})
   ```

   以上代码将我们已存在的 posts 集合转换为固定集合。

4. 固定集合文档按照插入顺序储存的,默认情况下查询就是按照插入顺序返回的,也可以使用$natural调整返回顺序。

   ```
   >db.cappedLogCollection.find().sort({$natural:-1})
   ```


### 启动项

1. | --dbpath  | 指定数据库的目录                 |
   | --------- | -------------------------------- |
   | --port    | 指定服务器监听的端口 默认 27017  |
   | --fork    | 用守护进程的方式启动 mongoDB     |
   | --logpath | 指定日志的输出路径，默认是控制台 |
   | --config  | 指定启动项用文件的路径           |
   | --auth    | 用安全认证方式启动数据库         |

2. 参考网址

   `https://blog.csdn.net/zhu_tianwei/article/details/44261235`

### 关闭数据库

* 安全停止

  ```javascript
  > use admin;                     --使用管理员数据库
  > db.shutdownServer();
  ```


### 导出和导入数据

* 导出( 表 )

  ```javascript
  //备份 foobar 数据库中 persons 集合的数据
  //需要在不启动客户端的情况下执行如下命令
  mongoexport -d foobar -c users -o D:/users.json
  mongoexport -h IP --port 端口 -u 用户名 -p 密码 -d 数据库 -o 文件存在路径 
  ```

* 导入（表）

  ```javascript
  mongoimport --db foobar --collection users --file D:/users.json
  ```
  API  `https://docs.mongodb.com/v3.2/reference/program/mongoimport/`

* 运行时备份 (mongodump)

  API  `https://docs.mongodb.com/v3.2/reference/program/mongodump/`

  
