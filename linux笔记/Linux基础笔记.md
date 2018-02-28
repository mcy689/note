1. Linux一般将档案可存取权限

   ```html
   三种身份 	owner/group/others
   三种身份分别对应三种权限  read/write/execute  4/2/1
   ```

2. 目录配置( __linux常见目录 __)

   ```html
   /		根目录
   /root	超级用户的家目录
   /home	普通用户的家目录
   /bin		命令保存目录（普通用户就可以读取的命令）
   /boot	启动目录，启动相关文件
   /dev		设备文件保存目录
   /etc		配置文件保存目录
   /mnt		系统挂载目录
   /media		挂载目录
   /tmp		临时目录
   /sbin	命令保存目录（超级用户才能使用的目录）
   /proc	直接写入内存		
   /usr		系统软件资源目录
   /var		系统相关文档内容
   /var/log/		系统日志位置
   ```

3. Linux 常用配置文件总汇

   ```html
   1. 用户信息文件   		/etc/passwd
   2. 个人的密码     		/etc/shadow
   3. 群组名称(组文件)      /etc/group
   ```

4. 文件的类型和权限

   ```html
   lrwxrwxrwx.  1 root root    7 Oct 15 23:19 bin -> usr/bin
   dr-xr-xr-x.  5 root root 4.0K Oct 15 23:24 boot
   -rw-r--r--   1 root root    0 Oct 15 15:22 .autorelabel
   第一个字元
   	d 表示 目录
   	- 表示 文件
   	l 表示 连接档 类似于windows的快捷方式
   九个字元 
   	每三个为一组分别表示 owner/group/others 身份的权限
   	如果没有则为 -

   实例一
   	drwxr-xr--  2 root     root     4.0K Feb 28 22:30 test
   	这样一个文件, other的权限中有读的权限,但是由于没有执行的权限,因此other的使用者无法进入此目录
   ```

5. 修改文件的权限

   ```html
   chgrp 改变档案的所属群组  change group的缩写
   chown 改变档案拥有者      change owner的缩写
   	chown user file       修改拥有者
   	chown user.group file 同时修改拥有者和群组
   chmod 改变档案的权限      

   -R 进行递归的持续变更 , 亦即连同次目录下的所有档案

   1. 修改文件权限 u g o 分别表示三种权限
   	+(加入) -(除去)  =(设定)
   	chmod u=rwx,g=rx,o=r test.php 设置 754 权限
       chmod u-x test.php  去掉所有者的执行权限
   	chmod a-r test.php  去掉全部人的读权限
   2. 数字修改
   	二进制 111
   	字母   rwx 
   	数字   421
   ```

   __注意一__  要修改的群组名称必须要在 `/etc/group`档案内存在才行,否则显示错误

   __注意二__  要修改的使用者必须是已经存在系统中的账号, `/etc/passwd` 档案中

   >说明:
   >
   >​	r ( read ) 可读取此一档案的实际内容, 如读取文字档的文字内容
   >
   >​	w ( write ) 可以编辑, 新增或者修改该档案的内容( 但不含删除该档案 )
   >
   >​	x ( execute ) 该档案具有可以被系统执行的权限
   >
   >__对于目录__ 
   >
   >​	r ( read contents in directory ) 表示具有读取目录结构清单的权限, 所有当具有读取( r ) 一个目录权限时, 表示可以查询该目录下的档名资料
   >
   >​	w ( modify contents of directory ) 
   >
   >​		建立新的档案与目录
   >
   >​		__删除已经存在的档案与目录( 不论该档案的权限为何! )__ 
   >
   >​	x 表示使用者能否进入该目录
   >
   >​		如果你在目录下不具有x的权限, 那么你就无法切换到该目录下, 也就无法执行该目录下的任何指令

   ​

   ​