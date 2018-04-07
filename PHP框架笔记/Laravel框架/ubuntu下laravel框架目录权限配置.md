1. 设置用户和用户组所属
```
sudo chown -R www-data:www-data /path/to/your/root/directory
```

2. 设置文件夹权限
```
sudo find /var/www/xiaohigh -type f -exec chmod 644 {} \;   
```

3. 设置文件权限
```
sudo find /var/www/xiaohigh -type d -exec chmod 755 {} \
```

4. 设置缓存目录的权限
```
sudo chmod -R ug+rwx storage bootstrap/cache
```