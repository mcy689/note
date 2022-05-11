## OPcache

### 简介

OPcache 通过将 PHP 脚本预编译的字节码存储到共享内存中来提升 PHP 的性能，存储预编译字节码的好处就是省去了每次加载和解析 PHP 脚本的开销。

### 配置

[OPcache官方php.ini配置](https://www.php.net/manual/zh/opcache.configuration.php)

[相关参考文档](https://segmentfault.com/a/1190000005844450)

查看所有 php 文件，`find . -type f -print | grep php | wc -l` 。

### 开发推荐配置

```html
opcache.enable = 1 									//启用操作码缓存。
opcache.enable_cli=1 								//仅针对 CLI 版本的 PHP 启用操作码缓存。 通常被用来测试和调试。
opcache.revalidate_freq=0						//检查脚本时间戳是否有更新的周期，以秒为单位。 设置为 0 会导致针对每个请求检查脚本更新。
opcache.validate_timestamps=1				//
opcache.max_accelerated_files=3000	//哈希表中可存储的脚本文件数量上限
opcache.memory_consumption=192			//OPcache 的共享内存大小，以兆字节为单位。
opcache.interned_strings_buffer=16	//用来存储预留字符串的内存大小，以兆字节为单位
opcache.fast_shutdown=1
```

### 维护更新阶段

```html
opcache.enable = 1 //启用操作码缓存。
opcache.enable_cli=1 //仅针对 CLI 版本的 PHP 启用操作码缓存。 通常被用来测试和调试。
opcache.revalidate_freq=300
opcache.validate_timestamps=1
opcache.max_accelerated_files=7963
opcache.memory_consumption=192
opcache.interned_strings_buffer=16
opcache.fast_shutdown=1
```

### 稳定项目推荐

```html
opcache.enable = 1 //启用操作码缓存。
opcache.enable_cli=1 //仅针对 CLI 版本的 PHP 启用操作码缓存。 通常被用来测试和调试。
opcache.revalidate_freq=0
opcache.validate_timestamps=0
opcache.max_accelerated_files=7963
opcache.memory_consumption=192
opcache.interned_strings_buffer=16
opcache.fast_shutdown=1
```

## php-fpm

FPM（FASTCGI Process Manager）是一个 FastCGI 进程管理器。从 PHP5.3.3 开始，PHP集成了 PHP-FPM。PHP—FPM 提供了更好的PHP进程管理方式，可以有效控制内存和进程，支持平滑重启 PHP 及重载 PHP 配置。

PHP-FPM 是多进程的服务，其中一个 master 进程（做管理工作） 和多个 worker 进程（处理数据请求）。

### 配置文件

[php-fpm.conf](https://www.php.net/manual/zh/install.fpm.configuration.php)

