# 第二章

## cat 命令

```shell
cat -s file.txt                     #压缩连续的空白行输出
cat -s file.txt | tr -s '\n'        #压缩并移除空白行  说明:tr -s '\n' 将连续多个'\n'字符压缩成单个'\n'
cat -t table.log                    #查看制表符 说明：这个命令可以将连续的空格区和制表符区分开
```

## 录制与回放终端会话文件和数据

1. Script 命令可以用于简历可在多个用户之间进行广播的视频会话

   ```shell
   # 1. 在 Terminal1 中输入以下命令
   mkfifo scriptfifo
   # 2. 在 Terminal2 中输入
   cat scriptfifo
   # 3. 返回 Terminal1，输入
   script -f scriptfifo
   # 到这里在 Terminal1 输入的命令就会在 Terminal2 的终端实时播放；需要退出 exit
   ```

2. 录制终端会话

   ```shell
   # 开始录制
       script -t 2>time.log -a action.log
       .... # 命令
       ..	 # 命令
       exit;# 结束
   # 说明：time.log 文件用于记录何时运行；action.log 用于存储命令输出。-t 选项用于将时序数据导入stderr。 2> 则用于将 stderr 重定向到 time.log
   # 回放命令执行过程
   scriptreplay time.log action.log
   ```

## xargs 将命令输出作为命令参数

1. ![屏幕快照 2019-04-01 下午10.30.53](./image/2019-04-01.png)

2. 用自己的定界符来分隔参数

   ```shell
   echo "splitXsplitXsplit" | xargs -d X	#使用X为定界符
   cat example.txt | xargs -i touch {}		#创建文件
   cat example.txt | xargs -i rm {}		#删除匹配的文件
   ```

3. Find 和 xargs 结合使用

   ```shell
   #find匹配并列出所有的.txt文件，并删除
   	find . -type f -name "*.jpg" -print0 | xargs -0 rm -f
   #统计php文件的行数
    	find . -maxdepth 1 -type f -name "*.php" -print0 | xargs -0 wc -l
   ```

## 用 tr 进行转换检查与词典操作

`tr` 只能通过 stdin（标准输入），而无法通过命令行参数来接受输入。 `tr [options] set1 set2` ,将来自stdin 的输入字符从 set1 映射到 set2，并将其输出写入 stdout（标准输出）。

```shell
#将输入字符由大写转换成小写
	echo "HELLO WORLD" | tr 'A-Z' 'a-z'
```

## 校验

```shell
md5sum 1.log > zzz.md5		#生成md5加密检验和
md5sum -c zzz.md5			#校验
```

## 排序(sort)、单一(uniq)与重复

```shell
grep -oE '[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}' /var/log/nginx/access.log |sort |uniq -c    #查看nginx访问IP数

sort uniq.log | uniq -c #统计各行在文件中出现的次数
sort uniq.log | uniq -d #找出文件中重复的行
```

## 临时文件名与随机数

```shell
dd if=/dev/zero bs=100k count=1 of=data.file #生成一个大小为100kb的测试文件
```

## 根据扩展名切分文件名

```shell
#获取文件名
file_jpg="sample.jpg"
name=${file_jpg%.*}
echo $name //sample
```

##  交互输入

```shell
 #!/bin/bash
  2 read -p "Enter number:" no;
  3 read -p "Enter name:" name;
  4 echo You have entered $no,$name;
```

