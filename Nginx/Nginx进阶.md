#### Nginx基本配置和优化

1. 配置文件详解

   ```html
   #使用的用户和组
   user  nginx nginx;
   #指定工作衍生进程数, ( 一般等于CPU的总核数或者总核数的两倍, 例如两个四核CPU 总核数为8)
   worker_processes  1;
   #指定错误日志存放的位置, 错误日志记录级别( debug | info | notice | warn | error | crit )
   #error_log  logs/error.log;
   #error_log  logs/error.log  notice;
   #error_log  logs/error.log  info;
   #指定pid存放路径
   #pid        logs/nginx.pid;

   events {
   	#使用网络I/O模型, linux系统推荐使用epoll
   	use epoll;
   	#允许连接的数
       worker_connections  1024;
   }

   http {
       include       mime.types;
       default_type  application/octet-stream;
   	#设置字符集, 如果一个网站有不同的字符集, 让后端程序员设置
   	#charset gb2312
   	#设置客户端能够上传的文件大小
   	client_max_body_size 8m;

       #log_format  main  '$remote_addr - $remote_user [$time_local] "$request" '
       #                  '$status $body_bytes_sent "$http_referer" '
       #                  '"$http_user_agent" "$http_x_forwarded_for"';

       #access_log  logs/access.log  main;

       sendfile        on;
       #tcp_nopush     on;

       #keepalive_timeout  0;
       keepalive_timeout  65;

       #gzip  on;

       server {
           listen       8011;
           server_name  mcy689.top;

           #charset koi8-r;

           #access_log  logs/host.access.log  main;

           location / {
               root   /www;
               index  index.php index.html index.htm;
           }

           #error_page  404              /404.html;

           # redirect server error pages to the static page /50x.html
           #
           error_page   500 502 503 504  /50x.html;
           location = /50x.html {
               root   /www;
           }

           # proxy the PHP scripts to Apache listening on 127.0.0.1:80
           #
           #location ~ \.php$ {
           #    proxy_pass   http://127.0.0.1;
           #}

           # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
           #
           location ~ \.php$ {
              root           /www;
              fastcgi_pass   127.0.0.1:9000;
               fastcgi_index  index.php;
               fastcgi_param  SCRIPT_FILENAME  /$document_root$fastcgi_script_name;
               include        fastcgi_params;
           }

           # deny access to .htaccess files, if Apache's document root
           # concurs with nginx's one
           #
           #location ~ /\.ht {
           #    deny  all;
           #}
       }
          # another virtual host using mix of IP-, name-, and port-based configuration
          #
          #server {
          #    listen       8000;
          #    listen       somename:8080;
          #    server_name  somename  alias  another.alias;

          #    location / {
          #        root   html;
          #        index  index.html index.htm;
          #    }
          #}
          # HTTPS server
          #
          #server {
          #    listen       443 ssl;
          #    server_name  localhost;

          #    ssl_certificate      cert.pem;
          #    ssl_certificate_key  cert.key;

          #    ssl_session_cache    shared:SSL:1m;
          #    ssl_session_timeout  5m;

          #    ssl_ciphers  HIGH:!aNULL:!MD5;
          #    ssl_prefer_server_ciphers  on;

          #    location / {
          #        root   html;
          #        index  index.html index.htm;
          #    }
          #}
    }  
   ```

2. Nginx 虚拟主机的配置 ( 基于域名的配置 )

   ```html
   server {
         #配置监听端口
         listen       80;
         server_name  yii.domain.com;
   	  #访问日志文件存放路径
   	  access_log  /logs/yii.domain.com.log combined;
         location / {
             #网站文件存放的目录
             root   /www/yii; 
             index  index.html index.htm index.php;
         }
   }
   ```

3. Nginx的自动列表目录配置

   ![图片1](./图片1.png)

4. Nginx 的浏览器本地缓存设置

   ```html
   #在本地缓存30天
   location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$
   {
       expires 30d;
   }
   #在本地缓存一个小时
   location ~ .*\.(js|css)?$
   {
       expires 1h;
   }
   ```

   ​

5. 在不停止Nginx服务的情况下平滑变更Nginx配置

   ```shell
   #先检查配置文件是否正确
   nginx -t
   #查看Nginx主进程号
   ps -ef | grep nginx
   #执行平滑变更
   kill -HUP 主进程号
   ```

6. Nginx的Rewrite规则与实例

   * Nginx 规则相关指令有if, rewrite, set, return, break

   * break指令 该指令的作用是完成当前的规则集, 不再处理rewrite指令

   * if指令, 该指令用于检查一个条件是否符合, 如果条件符合, 则执行大括号内的语句. if 指令不支持嵌套, 不支持多个条件`&&`或者`||` , 以下信息可以被指定为条件

     1. 变量名
     2. 变量比较可以使用 `=` ( 表示等于 ) 和 `!=`  ( 表示不等于 ) 运算符;
     3. 正则表达式模式匹配可以使用 `~` ( 表示区分大小写 )和 `~*` ( 表示不区分大小写 )
     4. `!~` 和 `!~*` 表示不匹配
     5. `-f` 和 `!-f` 用来判断文件是否存在
     6. `-d` 和 `!-d` 用来判断目录是否存在
     7. `-e` 和 `!-e` 用来判断文件或者目录存在
     8. `-x` 和 `!-x` 用来判断文件是否可执行 

   * 实战

     ```shell
     #url中以 .sh 和 .bash 结尾的返回状态码 403
     location ~ .*\.(sh|bash)?$ {
         return 403;
     }
     #文件和目录不存在时, 重定向到某个PHP文件
     if (!-e $request_filename) {
         rewrite ^/(.*)$ /index.php last;
     }
     #设置某些类型文件的浏览器缓存时间 在本地缓存30天
     location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$
     {
         expires 30d;
     }
     #在本地缓存一个小时
     location ~ .*\.(js|css)?$
     {
         expires 1h;
     }
     ```

     ​

     ​