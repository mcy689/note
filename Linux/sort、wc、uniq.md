## sort 排序

sort 命令用来对文件行进行排序，常用的一些参数

* `-f` 忽略字母大小写

- `-n` 根据字符串数值比较
- `-r` 表示逆序
- `-k,` 表示根据第几列
- `-t,` 表示字段与字段之间的分隔符

```shell
#/etc/passwd 以第三栏来排序，文本内容是以 : 来分隔的
cat /etc/passwd | sort -t ':' -nk3
```

## wc 统计

`wc [options] files`

### 参数

```shell
-c 	--bytes 统计字节数
-l  --lines 统计行数
-m	--chars 统计字符数，不能和 -c 一起使用
-w  --words 打印单词数
-L  --max-line-length  打印最长行的长度
```

### 命令

```shell
#查看passwd文件有多少行
wc -l /etc/passwd
#查看输出有多少个单词
echo 'aa bb cc' | wc -w
#查看输出多少字符
echo '1234' | wc -m
```

## uniq

uniq 命令从标准输入读取过滤邻近的相同的行，然后写回标准输出，在此过程中 uniq 会将重复的行 merge 合并到第一个结果中，所以 uniq 可以用来排除文件中重复的行。因此 uniq 经常和 sort 合用，先排序，然后使用 uniq 来过滤重复行。

### 常用参数

```shell
-c 在每一行前打印行出现的次数
-d 只打印重复的行，重复的行只打印一次
-D 打印出所有重复的行
-f 在比较时跳过前 N 个 fields
-i 在比较重复行时忽略大小写
-s 在比较时忽略前 N 字符
-u 只打印唯一的行
-w 比较时只比较每一行的前 N 个字符
```

### 命令

```shell
#示例文件 jj
#this is a test  
#this is a test  
#this is a test  
#i am tank  
#i love tank  
#i love tank  
#this is a test  
#whom have a try  
#WhoM have a try  
#you  have a try  
#i want to abroad  
#those are good men  
#we are good men

#uniq的一个特性，检查重复行的时候，只会检查相邻的行。重复数据，肯定有很多不是相邻在一起的
uniq -c jj
      #3 this is a test
      #1 i am tank
      #2 i love tank
      #1 this is a test
      #1 whom have a try
      #1 WhoM have a try
      #1 you  have a try
      #1 i want to abroad
      #1 those are good men
      #1 we are good men
#可以统计上面的错误统计
sort jj | uniq -c
	  #1 i am tank
      #2 i love tank
      #1 i want to abroad
      #4 this is a test
      #1 those are good men
      #1 we are good men
      #1 whom have a try
      #1 WhoM have a try
      #1 you  have a try
#打印重复行的数量
sort jj | uniq -dc
	  #2 i love tank  
      #4 this is a test
#仅显示不重复的行
sort jj | uniq -u
	#i am tank
    #i want to abroad
    #those are good men
    #we are good men
    #whom have a try
    #WhoM have a try
    #you  have a try
```