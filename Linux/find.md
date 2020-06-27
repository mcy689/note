# find

## 参数说明

1. `-name` 按照文件名查找文件。
2. `-iname` 不区分大小写按照文件名查找文件。
3. `-perm` 按照文件权限来查找文件。

## 基本格式

```shell
find start_directory test options criteria_to_match action_to_perform_on_results
```

## 查找文件

### `-name`

```shell
# 查找当前目录
find . -name '*.sh'
# 查找家目录
find $HOME -name '*.sh'
# 指定多个起始目录
find $HOME /tmp -name '*.sh'
	# 无相应权限
	find:  /tmp/orbit-root: Permission denied
	# 会将所有错误消息发送到空文件，就不会显示上述错误信息
	find $HOME /tmp -name '*.sh' 2>/dev/null
```

### `-iname` 

```shell
find . -iname '*.gif'
```

### `-type`

1. `b` - 块（缓存）特殊。
2. `d` - 目录。
3. `c` - 字符（未缓存）特殊。
4. `p` - 命名管道 (FIFO)。
5. `l` - 符号链接文件。
6. `f` - 普通文件。
7. `s` - 套接字。

```shell
find . -type f #普通文件
find . -type d #查找一个目录中的所有子目录
find . -type p #管道文件
find . -type l #使用以下命令查找您的/usr 目录中的所有符号链接：
find . -type s #socket文件
```

## 查找时间

find 命令有几个用于根据您系统的时间戳搜索文件的选项。这些时间戳包括

*•* mtime *—* 文件内容上次修改时间

*•* atime — 文件被读取或访问的时间

*•* ctime — 文件状态变化时间

mtime 和 atime 的含义都是很容易理解的，而 ctime 则需要更多的解释。由于 inode 维护着每个文件上的元数据，因此，如果与文件有关的元数据发生变化，则 inode 数据也将变化。这可能是由一系列操作引起的，包括创建到文件的符号链接、更改文件权限或移动了文件等。由于在这些情况下，文件内容不会被读取或修改，因此 mtime 和 atime 不会改变，但 ctime 将发生变化。

这些时间选项都需要与一个值 *n* 结合使用，指定为 *-n、n* 或 *+n*。

*• -n* 返回项小于 *n*

*• +n* 返回项大于 *n*

*• n* 返回项正好与 *n* 相等

```shell
# -atime 访问时间 -mtime 修改时间 -ctime 文件元数据（例如权限或者所有权）最后一次改变的时间。时间单位：天
find . -type f -atime 7 -print  #打印出恰好在七天前被访问过的所有文件
find . -type f -atime +7 -print #打印出访问时间超过七天的所有文件
find . -type f -atime -7 -print #打印出访问时间小于七天的所有文件
# -amin 访问时间 -mmin 修改时间 -ctime 变化时间 时间单位：分
find . -type f -amin -7 -print  #打印出访问时间小于7分钟的文件
# -newer 参数
find . -type f -newer file.txt -print #找出比file.txt 修改时间更长的所有文件
```

## 其他

1. 基本命令

   ```shell
   find base_path                       #要列出当前目录及子目录下所有的文件和文件夹
   find studyC/ -print                  #打印文件和目录的列表
   find ./ -name "*.php" -print         #查找当前目录下所有以php结尾的文件，并打印出文件
   find ./ -iname "t*.php" -print       #查找当前目录下所有以 t （不区分大小写）和 .php 结尾的文件
   find . \( -name "*.pdf" -o -name "*.txt" \) -print   #匹配多个条件中的一个，采用OR条件操作
   find ../ iregex ".*\( \.php\|\.sh)"  #查看 .php 或者 .sh 文件
   find . ! -name "*.txt" -print        #否定参数，查看除了txt文件的其他文件
   find -maxdepth 1 -type f -print      #指定find的向下查找的深度。查找普通文件
   ```

2. 基于文件大小的搜索

   ```shell
   find -type f -size +2k		#查找大于2k的文件
   ```

3. 删除匹配的文件

   ```shell
   find . -type f -name "*.swp" -delete	#查找并删除
   ```

4. 基于文件权限和所有权的匹配

   ```shell
   find . -type f -perm 600 -print						#打印出权限为644的文件
   find . -type f -name "*.php" ! -perm 644 -print		#查找合适的执行权限
   ```

5. 根据用户查找文件

   ```shell
   find ../home/ -type f -user slynux -print			#打印出用户slynux拥有的所有文件
   ```

6. 结合 find 执行命令或者动作

   ```shell
   find . -maxdepth 1 -perm 644 -name "test.log" -exec chown slynux:slynux {} \; #查找并修改权限 root用户
   ```

7. 查找并跳过特定的目录

   ```shell
   find . \( -name ".git" -prune \) -o \( -type of -print \) #打印出不含 .git 的文件路径
   ```

