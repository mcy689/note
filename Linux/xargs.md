## xargs 将命令输出作为命令参数

1. 将命令输出作为命令参数

   ![屏幕快照 2019-04-01 下午10.30.53](./image/2019-04-01.png)

2. 用自己的定界符来分隔参数

   ```shell
   #使用X为定界符
   echo "splitXsplitXsplit" | xargs -d X	# split split split
   cat example.txt | xargs -i touch {}		#创建文件
   cat example.txt | xargs -i rm {}		#删除匹配的文件
   ```

1. Find 和 xargs 结合使用

   ```shell
   #find匹配并列出所有的.txt文件，并删除
   	find . -type f -name "*.jpg" -print0 | xargs -0 rm -f
   #统计php文件的行数
    	find . -maxdepth 1 -type f -name "*.php" -print0 | xargs -0 wc -l
   ```

## 