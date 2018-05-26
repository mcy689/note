#### MongoDB

1. MongoDB 启动配置说明

   ```html
   mongodb的参数说明：  
   --dbpath 数据库路径(数据文件)  
   --logpath 日志文件路径  
   --master 指定为主机器  
   --slave 指定为从机器  
   --source 指定主机器的IP地址  
   --pologSize 指定日志文件大小不超过64M.因为resync是非常操作量大且耗时，最好通过设置一个足够大的oplogSize来避免resync(默认的 oplog大小是空闲磁盘大小的5%)。  
   --logappend 日志文件末尾添加  
   --port 启用端口号  
   --fork 在后台运行  
   --only 指定只复制哪一个数据库  
   --slavedelay 指从复制检测的时间间隔  
   --auth 是否需要验证权限登录(用户名和密码)
   ```

2. 启动MongoDB

   ```html
   mongod --dbpath D:\MongoDBDATA\data\db
   ```

3. MongoDB客户端

   