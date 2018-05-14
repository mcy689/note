# ubuntu下配置laravel开发环境(lnmp)

1. 安装php以及相关php扩展
```
    apt-get install php7.0 php-curl php7.0-mcrypt php-xml php-zip php-mbstring php7.0-pdo-mysql
```

2. 安装ngnix和php-fpm
```
    apt-get install nginx php7.0-fpm
```

3. 安装mysql
```
    apt-get install mysql-server mysql-client
```

4. 安装composer
```
//下载并安装composer
curl -sS https://getcomposer.org/installer | php
//移动文件
mv composer.phar /usr/local/bin/composer
//配置国内镜像
composer config -g repo.packagist composer https://packagist.phpcomposer.com
```

5. 安装laravel框架
```
composer create-project laravel/laravel project --prefer-dist "5.1.*"
```

6. 配置虚拟主机
```
cd /etc/nginx/sites-available
cp default xiaohigh.com
vim xiaohigh.com
```
==配置demo==
```
server {

    listen 80;
    
    index index.html index.php
    
    server_name xiaomi.com;
    
    root /var/www/html/project/public;
    
    # 优雅链接
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    #解析php后缀文件
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
    }
}
```

7. 启用配置
```
    ln -s /etc/nginx/sites-available/xiaohigh.com /etc/nginx/sites-enabled/xiaohigh.com
```

8. 重启nginx
```
/etc/init.d/nginx reload
```
