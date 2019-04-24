# Nginx 学习笔记

## 概述

1. 版本
   * 开源版本：`nginx.org`
   * 商业版本：`nginx.com`
   * 阿里版本：Tengine

##  配置语法

1. 配置文件由指定与指令快构成。
2. 每条指令以`;` 分号结尾，指令与参数间以空格符号分隔。
3. 指令块以`{}` 大括号将多条指令组织在一起。
4. `include` 语句允许组合多个配置文件以提升可维护性。
5. 使用 `#` 符号添加注释，提高可读性。
6. 使用 `$` 符号使用变量。
7. 部分指令的参数支持正则表达式。

## 用 nginx 搭建一个可用的静态资源 web 服务器

### gzip 模块

```nginx
gzip on						#开启（on）或关闭（off）gzip模块
gzip_buffers gzip_buffers
gzip_comp_level
gzip_min_length
gzip_http_version
gzip_proxied
gzip_types text/plain application/x-javascript text/css application/xml text/javascript application/x-httpd-php image/jpeg image/gif image/png;

```

## autoIndex

```nginx
autoindex on
```

### 限制传输的大小

```nginx
set $limit_rate 1k;
```

### 日志格式

    ```nginx
log_format compression  '$remote_addr - $remote_user [$time_local] '
​                        '"$request" $status $bytes_sent '
​                        '"$http_referer" "$http_user_agent" "$gzip_ratio"';
​    ```

## 用nginx 搭建一个具备缓存功能的反向代理服务

* ngx_http_proxy_module模块允许将请求传递给另一台服务器。

```nginx
upstream local {
	server 127.0.0.1:8012;
}
server {
	listen  8013;
	charset utf-8;
	server_name  your_server_name;
	access_log   logs/test-8013.access.log compression;
	location / {
		proxy_set_header Host $host;
		proxy_set_header X-Real-IP $remote_addr;
		proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
		proxy_pass http://local;
	}
}
```

##  goaccess基于终端的快速日志分析器

## OpenResty

