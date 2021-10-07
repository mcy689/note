## 配置 php-fpm socket 方式运行

1. `php-fpm` 配置文件修改

   ```html
   [www]
   user = nginx
   group = nginx
   ;listen = 127.0.0.1:9000
   listen=/tmp/php-fpm.sock;
   listen.owner=nginx
   listen.group=nginx
   ```

2. `nginx` 配置修改

   ```html
   fastcgi_pass   unix:/tmp/php-fpm.sock;
   ```

## 传送门

[php-fpm配置文件官网](https://www.php.net/manual/zh/install.fpm.configuration.php)

