#nginx日志切割脚本
#设置日志文件存放目录
logs_path="/alidata/log/nginx/access/"
#设置pid文件
pid_path="/alidata/server/nginx/logs/nginx.pid"
#重命名日志文件
mv ${logs_path}default.log ${logs_path}default_$(date -d "yesterday" +"%Y%m%d").log
#向nginx主进程发信号重新打开日志
kill -USR1 `cat ${pid_path}`
