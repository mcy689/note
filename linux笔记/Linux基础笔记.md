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


------

#### 文件与目录管理

1. `pwd` 命令

   ```html
   pwd  显示目前所在的目录    print working directory
   	-P 显示出确实的路径, 而非使用连结(link)路径
   ```

2. mkdir ( 建立新目录 )

   ```html
   mkdir 建立新目录          make directory
   	-m 设定档案的权限
   	-p 帮助你直接将所需要的目录递归建立起来
   	mkdir -m 711 test 创建 drwx--x--x 权限的文件
   ```

3. 关于执行文件路径

   * 当我们在执行一个指令的时候, 系统会依照PATH的设定去每个PATH定义的目录下搜寻文件名为ls的可执行文件, 如果PATH定义的目录中包含有多个文件名为ls的可执行档, 那么先搜寻到同名指令先被执行

   * PATH这个目录是由一堆目录所组成的, 每个目录中间用冒号隔开, 这个目录是有顺序之分的

     ```html
     root 执行
     /usr/local/apache2/bin:/usr/local/mysql/bin:/usr/local/php/bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/root/bin

     machunyu
     /usr/local/apache2/bin:/usr/local/mysql/bin:/usr/local/php/bin:/usr/local/bin:/usr/bin:/usr/local/sbin:/usr/sbin:/home/machunyu/.local/bin:/home/machunyu/bin
     ```

   * PATH的增删改

     ```html
     echo $PATH  查看哪些目录被定义出来
     PATH="$PATH":/root  这个操作可以将 /root 加入到PATH当中 （关闭终端失效）
     添加环境变量
         export PATH=/usr/local/php/bin:$PATH
         echo $PATH
         #/usr/local/php/bin:/usr/local/php/bin:/usr/lib/qt-3.3/bin:/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin:/root/bin
     配置文件中加入环境变量
         vim /etc/profile
         在最后一行加上export PATH="/usr/local/php/bin:$PATH"
           export PATH="/usr/local/mysql/bin:$PATH"
     重新加载配置 source /etc/profile
     ```

4. 文件和目录的检视    ls 命令

    - `ls  -i`   列出inode( 索引节点 ) 号码

    - `ls  -F`   根据文件 , 目录等资讯 , 给予附加资料结构,

        ​     `*`  代表可执行档 ,  `/ ` 代表目录  ` = ` 代表socket文件  ` | ` 代表FIFO的文件

   - `ls  -n ` 列出UID 与 GID 而非使用者与群组的名称

   - `ls -l --full-time `  显示完整的时间格式

   - `ls --color=never`  不要依据文件特性给予颜色显示  --color=always 显示颜色 --color=auto 让系统自行依据设定来判断是否给予颜色

5. 复制, 删除 和 移动

   * `cp` 复制可以创建 `连结档` 就是快捷方式
     * `cp  -i`  若目标文件已经存在时, 覆盖时会先询问
     * `cp  -p`  连同档案的属性一起复制过去, 而非使用预设属性( __备份常用__  )
     * `cp  -r`  递归持续复制, 用于目录的复制
     * `cp  -s` 复制成为符号连结档, 亦即 快捷方式
     * `cp  -a` 可以将档案的所有特性都给复制过来

   * `cp `  一般来说复制别人的资料( 该文件必须有read权限 )时, 总是希望复制到的资料最后是我自己的, 所以在预设的条件中, `cp` 的来源档与目的档的权限是不同的, __目的档的拥有者通常会是指令操作者本身__ 

   * 删除命令

     ```html
     \rm -rf /tmp/etc   在指令前加上反斜线, 可以忽略掉alias的指定选项
     ```

6. 其他命令

   ```html
   basename /etc/sysconfig/network   network     取得最后一个目录或文件
   dirname /etc/sysconfig/network    /etc/sysconfig  取得的变成目录名
   ```

7. 查看文件内容

   ```html
   cat   由第一行开始显示文件内容
   tac   从最后一行开始显示
   nl    显示的时候, 顺道输出行号
   more  一页一页的显示文件内容
   less  相反和more
   head  只看头几行
   tail  只看末尾几行
   od    以二进位的方式读取文件内容
   ```

8. 查看linux预设的权限

   ```html
   umask -S     u=rwx,g=rx,o=rx
   umask		 0022

   创建一个文件预设的是没有执行权限最大的权限是 -rw-rw-rw
   创建一个文件夹预设的是(文件夹的执行权限与是否可以进入此目录相关)最大权限-rwx-rwx-rwx

   umask的分数指的是该预设值需要减掉的权限(其中第一位是特殊权限用的)

   修改预设值
   umask 002   新建的文件夹  -rwx-rwx-r-x
   ```

   * 通常root的`umask` 会拿掉比较多的属性, root的`umask` 预设是022, 这是基于安全的考量, 至于一般身份使用者, 通常`umask` 的设定为002

   * ` /etc/bashrc ` 这个文件中包含了相关的信息

   * 文件或者目录的隐藏属性

     ```html
     chattr [+-=][选项]
     	+ 增加某一个特殊参数, 其他原本的存在参数则不动
     	- 移除某一个特殊参数, 其他原本存在参数则不动
     	= 设定一定, 且仅有后面接的参数
     选项
     	A 当设置了A这个属性时, 若你有存取此文件(或者目录)时, 他的存取时间atime将不会被修改, 可避免 I/O 较慢的机器过度的存取磁盘
     	S 当加上这个属性时, 当你进行任何档案的修改, 该更动会同步写入磁盘中
     	a 当设定a之后, 这个档案将只能增加资料, 而不能删除也不能修改资料  root设定这个属性
     	c 这个属性设定之后, 将会自动的将此档案压缩, 在读取的时候将会自动解压缩, 但是在存储的时候, 将会先进性压缩后在存储(对于大档案有用);
     	d 当dump程序被执行的时候, 设定d属性将可以使用该档案或者目录不会被dump备份
     	i 可以让一个文件不能被删除, 改名
     	s 如果这个档案被删除, 他将会被完全的移除出这个硬盘空间,无法挽救
     	u 如果该文件被删除了, 资料内容其实还存在磁盘中, 可以救援该档案

     实例 root 登录
     	touch dddd.log
     	chattr +i dddd.log
     	rm -rf dddd.log         rm: cannot remove ‘ddd’: Operation not permitted
     	chattr -i dddd.log
     	rm -rf dddd.log
     ```

   * 文件或者目录特殊属性的查看

     ```html
     lsattr 显示文件隐藏属性
     	-a 将隐藏文件的属性也秀出来
     	-d 如果接的是目录, 仅列出目录本身的属性而非目录内的文件名
     	-R 连同子目录的子目录页一并列出来

     [root@iZ2zef0abwnnrdb06hlftgZ tmp]# lsattr -a ddddd 
     -------------e-- ddddd

     例子
     	[root@iZ2zef0abwnnrdb06hlftgZ /]# ls -lh /usr/bin/passwd 
     	-rwsr-xr-x. 1 root root 28K Jun 10  2014 /usr/bin/passwd

     ```

9. SUID SGID SBIT ( 需要在使用`chmod` 命令时在该权限数字的前面加上对应的权限

   ```html
   chmod u+s filename 设置SUID位
   chmod u-s filename 去掉SUID设置
   chmod g+s filename 设置SGID位
   chmod g-s filename 去掉SGID设置
   粘滞位权限都是针对其他用户（other）设置，使用chmod命令设置目录权限时，“o+t”、“o-t”权限模式
   ```

   ​

   - __当s这个标志出现在文件拥有者的x权限上时__ 就被称为 `Set UID ` 

     - SUID 权限__仅对二进制程序(命令)__  有效
     - 执行者对于该程序需要具有x的权限
     - 本权限仅在执行该程序的过程中有效
     - 执行者将具有该程序拥有者的权限

     ```html
     -rwsr-xr-x. 1 root root 28K Jun 10  2014 /usr/bin/passwd
     例子
     	我们的linux系统中, 所有账号的密码都记录在/etc/shadow 这个文件里面, 这个文件的仅有root可读且仅有root可以强制写入, 当普通账户当执行 passwd命令的时候, 由于passwd命令是SUID的, 所以修改密码
     ```

   - 当s在群组的x时则称为 `set GID` ( 目录或者命令)

     ```html
     -rwxr-sr-x.  1 root tty     20K Aug  4  2017 write

     程序执行着对于该程序来说, 需要具备x的权限, 执行者在执行的过程中将会获得该程序的群组支援
     ```

     - 为目录设`SGID`权限

       使用者若对于此目录具有r与x的权限时, 该使用者能够进入此目录, 使用者在此目录下的有效群组将会变成该目录的群组 

   - SBIT 只对目录有效

     ```html
     drwxrwxrwt.  8 root root 4.0K Mar 10 18:20 tmp

     本身 /tmp 目录的权限, 任何人都可以在/tmp目录内新增, 修改文件, 但是仅有该文件/目录建立者与root能够删除自己的文件或者目录
     ```

     * 当使用者对于此目录具有`w,x` 权限, 也就是说具有写入权限时, 当使用者在给目录下建立档案或者目录时, 仅有自己与root才有权力删除该文件

   ------

   ### 认识与学习BASH

   1. 命令别名的设定

      ```html
      命令行输入 alias  可以查看全部的别名

      ```

      ​