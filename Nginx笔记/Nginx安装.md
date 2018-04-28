#### Nginx的编译安装  ( 这个安装文件是在已经安装了apache以后的安装记录 )

1. 安装

   ```shell
   ./configure --prefix=/usr/local/nginx \
   --sbin-path=/usr/sbin/nginx \
   --pid-path=/usr/local/nginx/run/nginx.pid \
   --user=nginx \
   --group=nginx \
   --with-http_ssl_module \    #开启这个模块nginx支持https请求
   --with-http_flv_module \
   --with-http_gzip_static_module
     
   Configuration summary
     + using system PCRE library
     + using system OpenSSL library
     + using system zlib library

     nginx path prefix: "/usr/local/nginx"
     nginx binary file: "/usr/local/nginx/sbin/nginx"
     nginx modules path: "/usr/local/nginx/modules"
     nginx configuration prefix: "/usr/local/nginx/conf"
     nginx configuration file: "/usr/local/nginx/conf/nginx.conf"
     nginx pid file: "/usr/local/nginx/run/nginx.pid"
     nginx error log file: "/usr/local/nginx/logs/error.log"
     nginx http access log file: "/usr/local/nginx/logs/access.log"
     nginx http client request body temporary files: "client_body_temp"
     nginx http proxy temporary files: "proxy_temp"
     nginx http fastcgi temporary files: "fastcgi_temp"
     nginx http uwsgi temporary files: "uwsgi_temp"
     nginx http scgi temporary files: "scgi_temp
   ```

2. 创建用来运行nginx的用户及组

   * 创建一个新的用户和用户组来运行nginx, 但是，我们并不希望nginx成为一个真实的可以登陆到远程进行操作的用户，所以，我们并不给它创建家目录，在useradd的时候，用-M参数

     ```html
      groupadd nginx
      useradd -g nginx -M nginx
     ```


   * 如果启动`nginx`时报这个错说明没有创建`nginx` 用户以及对应的用户组

       `nginx: [emerg] getpwnam("nginx") failed` 

3. 编译安装

   * 下载最新的稳定版本

     ```html
     最新的下载地址 : http://nginx.org/download/nginx-1.14.0.tar.gz

     shell命令下载
       wget http://nginx.org/download/nginx-1.14.0.tar.gz
       tar -xzvf nginx-1.14.0.tar.gz         <!--解压-->
     ```

4. 启动`php-fpm` 

   * 在查看了php安装目录后发现没有`php-fpm` 这个程序

     > PHP5.3.3已经集成php-fpm了，不再是第三方的包了。PHP-FPM提供了更好的PHP进程管理方式，可以有效控制内存和进程、可以平滑重载PHP配置，比spawn-fcgi具有更多优点，所以被PHP官方收录了。在./configure的时候带 `–enable-fpm` 参数即可开启`PHP-FPM`。

   * 重新编译php, 出现php-fpm

5. 问题

   * `ERROR: No pool defined. at least one pool section must be specified in config file`

     > 大概意思就是说找不到 /usr/local/php/etc/php-fpm.d/目录下的配置文件。 进入里面的目录，会有一个`www.conf.default` 文件。执行下面命名复制一份，复制好之后，编辑该文件。


   * 访问php页面显示`file not found`

   * 查看error.log日志文件提示`"Primary script unknown" while reading response header from upstream"`

   * 解决

     * **nginx配置文件中配置的php文件指向的目录不对，导致找不到文件**

       ![nginx配置](./nginx配置.png)

     - 默认php-fpm的配置文件在你的php安装目录下 路径为`/usr/local/php/etc/php-fpm.d/www.conf`，vim打开可以看到里面有

       ```html
       user = nobody  <!--将这里修改为nginx的用户-->
       group = nobody  <!--将这里修改为nginx的组-->
       ```