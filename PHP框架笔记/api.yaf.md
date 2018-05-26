### yaf的安装

>tar zxvf yaf-3.0.5.tgz      		解压安装包
> cd yaf-3.0.5                                   进入目录
> phpize                                           执行命令
> ./configure --with-php-config=/usr/local/php/bin/php-config 环境的检测 
> make                                             安装                         
> make test
> find ./ -name 'yaf.so'                    查找扩展文件位置
> make install                                    将扩展安装到php上
> ls /usr/local/php/lib/php/extensions/no-debug-non-zts-20151012/   
>
>​    显示扩展文件的路径
>
>/usr/local/php/bin/php-config    显示php的相关信息列出来
>
>netstat -tpnlu  显示linux端口的列表
