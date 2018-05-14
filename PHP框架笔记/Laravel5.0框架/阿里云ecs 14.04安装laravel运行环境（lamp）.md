1. 购买ecs服务器并使用ssh连接进入服务器
2. 安装apache
```
apt-get install apache2
```
3. 安装mysql
```
apt-get install mysql-server mysql-client
```
4. 安装php以及相关扩展
```
apt-get install php5 php5-curl php5-mysql php5-mcrypt
```

5. 安装git版本控制
```
apt-get install git
```

6. 安装curl
```
apt-get install curl
```

7. 安装composer
```
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

8. 执行安装
```
cd directory
composer install
```

9. 修改目录权限
```
chmod 0777 directory -R  #简单粗暴
```

10. 复制并修改.env文件

11. 执行数据库迁移命令
```
php artisan migrate
```
12. **配置虚拟主机 (==文件后缀一定要conf==)**
    ```
    //切换到虚拟主机目录
    cd /etc/apache2/sites-available/
    //复制demo文件
    cp 000-default.conf xiaohigh.com.conf
    //修改配置文件
    vim xiaohigh.com.conf
    ```
    配置demo
    ```
    <VirtualHost *:80>
        #域名
        ServerName xiaohigh.com
        #网站根目录
        DocumentRoot /var/www/html/laravel/public
        #日志配置
        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined
        #重写
        <Directory "/var/www/html/laravel/public">
            Options Indexes FollowSymLinks MultiViews
            AllowOverride All
            Order allow,deny
            allow from all
        </Directory>
    </VirtualHost>
    ```
12. **启用配置**
    ```
    a2ensite xiaohigh.com.conf
    ```
13. **启用url重写**
    ```
    a2enmod rewrite
    ```

14. 重启apache
    ```
    /etc/init.d/apache2 restart
    ```