# ubuntu下配置laravel开发环境

1. **安装apache**
    ```
    sudo apt-get install apache2
    ```

2. **安装mysql**
    ```
    sudo apt-get install mysql-server mysql-client
    ```
    
3. **安装php以及相关都扩展**
    ```
    sudo apt-get install php7.0 curl openssl php-curl php-pdo php-mbstring php-dom php-zip php7.0-mcrypt
    ```

4. **安装apache的php7模块**
    ```
    sudo apt-get install libapache2-mod-php7.0
    ```

5. **安装composer**
    ```
    curl -sS https://getcomposer.org/installer | php
    ```
    
6. **移动文件到命令目录**
    ```
    sudo mv composer.phar /usr/local/bin/composer
    ```
7. **composer命令**
    ```
    //检测是否安装成功
    composer -v 
    //配置国内镜像
    composer config -g repo.packagist composer https://packagist.phpcomposer.com
    ```
    
8. **安装laravel框架 (版本为5.1)**
    ```
    composer create-project laravel/laravel project --prefer-dist "5.1.*"
    ```
    
9. **设置目录权限(vendor, storage有可写权限)**
    ```
    //简单方式
    sudo chmod 0777 project -R
    ```

10. **开启重写模块**
    ```
    sudo a2enmod rewrite
    ```
11. **配置虚拟主机 (==文件后缀一定要conf==)**
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
    
13. **重启apache**
    ```
    /etc/init.d/apache2 restart
    ```

14. 大功告成

---


## 常见问题
1. ==远程ssh连接服务器失败==
    
    ```
    ssh: connect to host 172.16.32.165 port 22: Connection refused
    ```
    解决方法:
    ```
    sudo apt-get install ssh
    ```
2. ==zip扩展没有开启==
    ```
    Failed to download laravel/laravel from dist: The zip extension and unzip command are both missing, skipping.
    ```
    解决方法:
    ```
    sudo apt-get install php-zip
    ```

3. ==不建议使用root用户运行composer==
    ```
    Do not run Composer as root/super user!
    ```
    解决方法:
    ```
    在linux下创建用户,并创建家目录, 然后使用该用户运行composer命令
    ```
4. 没有证书访问 
```
PHP Warning:  file_get_contents(): SSL operation failed with code 1. OpenSSL Error messages:
```
    解决方法
```
wget http://curl.haxx.se/ca/cacert.pem
curl -sS https://getcomposer.org/installer | php -- --cafile=cacert.pem
```
