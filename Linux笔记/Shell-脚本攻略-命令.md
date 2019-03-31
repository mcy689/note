# 第二章

## cat 命令

```shell
cat -s file.txt  				#压缩连续的空白行输出
cat -s file.txt | tr -s '\n'	#压缩并移除空白行  说明:tr -s '\n' 将连续多个'\n'字符压缩成单个'\n'
cat -t table.log 				#查看制表符	说明：这个命令可以将连续的空格区和制表符区分开
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

## 文件查找与文件列表扩展名切分文件名

```shell
find base_path 		#要列出当前目录及子目录下所有的文件和文件夹
find studyC/ -print #打印文件和目录的列表

```



## 将命令输出作为命令参数

## 用 tr 进行转换检查与词典操作

## 校验和与合适输入自动化

## 排序、单一与重复



