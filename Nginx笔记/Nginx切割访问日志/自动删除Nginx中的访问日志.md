1. 自动删除Nginx访问日志 autodellogs.sh
    ```sh
    #!/bin/bash
    find /alidata/log/nginx/access/ -mtime +7 -type f -name *.log | xargs rm -f
    ```
    *说明`find`命令
    ```html
    find命令的格式：find [-path……] -options [-print -exec -ok]
    
    path：要查找的目录路径。 
          ~ 表示$HOME目录
           . 表示当前目录
           / 表示根目录 
           
    exec：对匹配的文件执行该参数所给出的shell命令。 
          形式为command {} \;，注意{}与\;之间有空格 
    
    options常用的有下选项： 
          -name：按照名字查找 
          -type：按照文件类型查找    f 表示普通文件 
          -mtime n 按照文件的更改时间来找文件，n为整数。
          
    #find ... -exec rm {} \; 
    #find ... | xargs rm -rf
    两者都可以把find命令查找到的结果删除，其区别简单的说是前者是把find发现的结果一次性传给exec选项，
    这样当文件数量较多的时候，就可能会出现“参数太多”之类的错误，相比较而言，后者就可以避免这个错误，
    因为xargs命令会分批次的处理结果。这样看来，“find ... | xargs rm -rf”是更通用的方法，推荐使用！
    ```
    
2. 切割日志脚本 nginx_log.sh
    ```sh
    #nginx日志切割脚本
    #设置日志文件存放目录
    logs_path="/alidata/log/nginx/access/"
    #设置pid文件
    pid_path="/alidata/server/nginx/logs/nginx.pid"
    #重命名日志文件
    mv ${logs_path}default.log ${logs_path}default_$(date -d "yesterday" +"%Y%m%d").log
    #向nginx主进程发信号重新打开日志
    kill -USR1 `cat ${pid_path}`
    ```
3. cron是一个linux下 的定时执行工具，可以在无需人工干预的情况下运行作业。
    ```
    service crond start //启动服务
    service crond stop     //关闭服务
    service crond restart  //重启服务
    service crond reload   //重新载入配置
    service crond status   //查看服务状态 
    ```