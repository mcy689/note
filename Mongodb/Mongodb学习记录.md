### MongoDB

####MongoDB 启动配置说明

```html
mongod help 查看启动详细参数
mongodb的参数说明：  
--dbpath 数据库路径(数据文件)  
--logpath 日志文件路径  
--master 指定为主机器  
--slave 指定为从机器  
--source 指定主机器的IP地址  
--pologSize 指定日志文件大小不超过64M.因为resync是非常操作量大且耗时，最好通过设置一个足够大的oplogSize来避免resync(默认的 oplog大小是空闲磁盘大小的5%)。  
--logappend 日志文件末尾添加  
--port 启用端口号   默认 27017 端口
--fork 在后台运行  
--only 指定只复制哪一个数据库  
--slavedelay 指从复制检测的时间间隔  
--auth 是否需要验证权限登录(用户名和密码)
```
#### 启动MongoDB

```html
windows环境启动mongodb 数据库的
mongod.exe --dbpath D:\MongoDBDATA
```
* 看到这个说明数据库启动成功

  ![截图](./image/20180602230520.png)

 #### mongodb和关系型数据库的对比

|    对比项    |      mongoDB      |     mysql      |
| :----------: | :---------------: | :------------: |
|      表      | 集合(Collection ) |     table      |
| 表的一行数据 |   文档Document    | 一条记录record |
|    表字段    |      键 key       |   字段 field   |
|    字段值    |     值 value      |    值value     |
|    主外键    |        无         |     pk,fk      |
| 灵活度扩展性 |       极高        |       差       |

#### mongodb客户端

* 默认是用test用户启动mongodb客户端 , 使用超级管理员启动 `mongo 127.0.0.1/admin`

#### 创建数据库

