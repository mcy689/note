## 积硅步, 致千里

### 文档

1. 文档的键不能含 \0 ( 空字符 )，这个字符用于表示键的结尾。

2. MongDB区分大小写和区分类型

   ```mongodb 
   {"Foo":3} 和 {"foo":3}   不是相同的
   {"foo":3} 和 {"foo":"3"} 不是相同的
   ```

3. MongoDB不能有重复的键

   ```mongodb
   {"greeting":"hello world","greeting":"hello MongoDB"} 错误
   ```

### 集合

>组织集合的一种惯例是使用 `.` 分隔不同命名空间的子集合。例如，一个具有博客功能的应用可能包含两个集合，分别是 `blog.posts` 和 `blog.authors` 。这个为了使组织结构更清晰，这里的blog的集合（这个集合甚至不需要存在）跟它的自己和没有任何关系。

### 数据库

>数据库最终会变成系统里的文件，而数据库名就是相应的文件名

1. admin

   >这个数据库就是 “ root ” 数据库。如果将一个用户添加到admin数据库，这个用户将自动获取所有数据库的权限。一些特定的服务器端命令也是只能从admin数据运行，列出所有数据库或者关闭服务器。

2. local

   > 这个数据库永远不可以复制，且一台服务器的所有本地集合都可以存储在这个数据中。

3. config

   >MongoDB用于分片设置时。

### MongoDB的启动

>1. mongodb 在没有参数的情况下会使用默认的数据目录 `/data/db` 。如果数据目录不存在或者不可写，服务器会启动失败。因此 MongoDB 启动前，先创建数据目录，以确保对该目录有写权限。
>
>2. 启动时，服务器会打印版本和系统信息，然后等待连接。默认清空下，MongoDB监听27017端口。
>3. 通过db命令可以查看当前使用的数据库。

### 数据类型

1. null 用来表示空值或者不存在的字段

   ```javascript
   {"key":null}
   ```

2. Boolean 有两个值 true 和 false

   ```javascript
   {"key":true}
   ```

3. 正则表达式： 查询时，使用正则表达式作为限定条件，语法也与 Javascript 的正则表达式相同

   ```javascript
   {"key":/foobar/i}
   ```

4. 数组

   ```javascript
   {"key":["a","b","c"]
   ```

5. 内嵌文档 ： 文档可嵌套其他文档，被嵌套的文档作为父文档的值

   ```javascript
   {"key":{"foo":"bar"}}
   ```

6. 对象id ： 是文档的唯一标识

   MongoDB中存储的文档必须有一个 `_id` 键。这个键的值可以是任何类型的，默认是个ObjectId对象。在一个集合里面，每个文档都有一个唯一的`_id` 

   ```javascript
   { "_id" : ObjectId("5b378afa8f979b000c259dba")}
   ```

7. 二进制数据

   二进制数据是一个任意字节的字符串。它不能直接在shell中使用。如果要将非UTF-8字符保存到数据库中，二进制数据是唯一的方式。

### ObjectId详情

>1. ObjectId 不是其他比较常规的做法（比如自增加的主键），因为在多个服务器上同步自动增加主键值即费力又费时。MongoDB设计的初衷就是用作分布式数据库，所以能够在分片的环境中生成唯一的标识符非常重要。
>2. ObjectId使用12字节的存储空间，是一个由24个十六进制数字组成的哦字符串（每个字节可以存储两个十六进制数字）。
>3. 0|1|2|3|4|5|6|7|8|9|10|11|  （5b378afa8f979b000c259dba）
>   * ObjectId 的前4个字节是从标准纪元开始的时间戳，单位秒。时间戳和随后的5个字节组合起来，提供了秒级别的唯一性。
>   * 接下来的 3 个字节是所在主机的唯一标识符。通常是机器主机的散列值（hash）。这样就可以确保不同主机生成不同的 ObjectId 。
>   * 接下来的2个字节来自产生 ObjectId 的进程的进程标识符（PID）。
>   * 最后是一个自动增加的计数器。确保相同进程同一秒产生的ObjectId 是唯一的。一秒钟最多允许每个进程拥有__256的3次方__ 个不同的 ObjectId。
>4. 自动生成 `_id` , 如果插入文档时没有 `_id` 键，系统会自动帮你创建一个。

### 使用MongoDB Shell

1. 连接数据库

   * mongo some-host:prot/myDB

     ```javascript
     mongo 127.0.0.1/admin
     ```

   * 启动 mongo shell 时不连接到任何 mongodb 的方式连接到数据库

     ```javascript
     mongo --nodb  //此时没有连接任何数据库
     conn = new Mongo("127.0.0.1:27017"); 
     db = conn.getDB("admin"); //执行完这条命令就可以正常操作shell
     ```

2. 使用 shell 执行脚本

   1. 在本地执行 js 脚本

      ```javascript
      //mongo shell 会依次执行传入的脚本
      mongo D:/MongoDB3.6/1.js D:/MongoDB3.6/2.js
      ```

   2. 在远程执行 js 脚本

      ```javascript
      //前提是能连接上
      mongo --quiet server-1:30000/foo scrip t1.js
      ```

   3. 可以在脚本中使用 print() 函数将内容输出，可以在shell 中使用管道命令。

   4. 可以使用 load() 函数，从交互式 shell 中运行脚本。填写路径的时候尽量填写绝对路径，注意 load() 函数无法理解 `~ `这个代表家目录的符号。

      ```javascript
      > load("D:/MongoDB3.6/1.js")
      dddd
      true
      ```

   5. shell 中辅助函数对应的 javascript 函数

      ```html
        shell 中               js 中
      use foo               db.getSisterDB('foo')
      show dbs              db.getMongo().getDBs()
      show collections      db.getCollectonNames()
      ```

### 帮助

1. 数据库级别的几个函数 （db.help()）

   ```javascript
   db.hostInfo();   //get details about the server's host
   db.isMaster();   //check replica primary status
   db.version();    //current version of the server
   db.stats();      //查看当前数据库的信息
   db.logout()
   ```

2. 集合级别的帮助 （ db.collections.help()）

3. 如果想知道一个函数是做什么用的。可以直接查看源码如何是实现的。

   ```javascript
   db.users.update  
   ```

