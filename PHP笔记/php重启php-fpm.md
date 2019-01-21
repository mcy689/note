# php 安装完扩展重启

1. 启动 php-fpm

   ```shell
   /usr/local/php/sbin/php-fpm
   ```

2. php 5.3.3 以后的php-fpm 不再支持 php-fpm 以前具有的 /usr/local/php/sbin/php-fpm (start|stop|reload)等命令，需要使用信号控制。

   ```shell
   INT, TERM 立刻终止
   QUIT 平滑终止
   USR1 重新打开日志文件
   USR2 平滑重载所有worker进程并重新载入配置和二进制模块
   ```

3. 重启php-fpm

   ```shell
   ps aux|grep php-fpm //找到master进程
   kill -USR2 42891 //重启对应的php进程
   ```
