#### Nginx的启动, 停止, 平滑重启

1. Nginx 的命令

   ```html
   nginx -s reload|reopen|stop|quit  #重新加载配置|重启|停止|退出 nginx
   nginx -t   #测试配置是否有语法错误

   nginx [-?hvVtq] [-s signal] [-c filename] [-p prefix] [-g directives]

   -?,-h           : 打开帮助信息
   -v              : 显示版本信息并退出
   -V              : 显示版本和配置选项信息，然后退出
   -t              : 检测配置文件是否有语法错误，然后退出
   -q              : 在检测配置文件期间屏蔽非错误信息
   -s signal       : 给一个 nginx 主进程发送信号：stop（停止）, quit（退出）, reopen（重启）, reload（重新加载配置文件）
   -p prefix       : 设置前缀路径（默认是：/usr/local/Cellar/nginx/1.2.6/）
   -c filename     : 设置配置文件（默认是：/usr/local/etc/nginx/nginx.conf）
   -g directives   : 设置配置文件外的全局指令
   ```

   ​

2. Nginx 的启动

   ```shell
   nginx -c /usr/local/nginx/conf/nginx.conf
   ```

   * 说明 `-c` 指定了配置文件的路径, 如果不加 `-c` 参数, Nginx会默认加载其安装目录的conf子目录中的`nginx.conf` 文件

3. Nginx 的停止

   * 发送系统信号给Nginx主进停止Nginx

     > 1. `ps -ef | grep nginx`
     >
     >    ![运行时截图](./运行时截图.png)
     >
     > ​       其中 1 个Nginx 进程的备注信息为 `master process` 表示它为主进程
     >
     > 2. 如果配置文件中指定了pid文件存放的路径 `/usr/local/nginx/run/nginx.pid` ,如果没有指定, 则默认存放在logs目录下
     >
     >    ```shell
     >    #使得程序正常的退出s
     >    kill -QUIT 28615
     >    kill -QUIT `cat /usr/local/nginx/run/nginx.pid`
     >    # 快速停止nginx
     >    kill -TERM `cat /usr/local/nginx/run/nginx.pid`
     >    # 强制停止所有nginx进程
     >    pkill -9 nginx
     >    ```

4. Nginx 的平滑启动

   1. 通过发送系统信号给Nginx主进程的方式来进行

      ```shell
      nginx -t -c $nginxConf  #检测nginx配置文件是否正确
      ```

   2. 平滑启动

      ```shell
      kill -HUP 28615
      ```

      * 当Nginx 接受到HUP信号时, 它会尝试先解析配置文件, 如果成功, 就应用新的配置文件( 重新打开日志文件, 或者监听的套接字 ). 之后, Nginx 运行新的工作进程并关闭旧的工作进程. 通知工作进程关闭监听套接字, 但是继续为当前连接的客户提供服务. 所有客户端的服务完成后, 旧的工作进程被关闭

        __如果新的配置文件应用失败, Nginx将继续使用旧的配置进行工作__

5. Nginx 的信号控制

   1. TERM  快速关闭
   2. QUIT  从容关闭
   3. HUP 平滑启动, 重新加载配置文件
   4. USR1 重新打开日志文件, 在日志切割的时候用途较大
   5. USR2 平滑升级可执行程序
   6. WINCH 从容关闭工作进程

   ​