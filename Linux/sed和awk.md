## sed

### sed 命令的语法

1. 行地址对于任何命令都是可选的。它可以使一个模式，被描述为由斜杆、行号或行寻址符号括号的正则表达式。大多数 sed 命令能接受由逗号分隔的两个地址。

   ```shell
   #[address]command
   #删除文件中从50行到最后一行的所有行
   sed -e '50,$d' test
   ```

2. 有些命令只接受单个行地址。它们不能应用于某个范围的行。

   ```shell
   #[line-address]command
   ```

3. 可以用大括号进行分组以使起作用于同一个地址。

   ```shell
   #删除oopp-ooop输入块中的空行，而且它还使用替换命令s，改变了aaa
   /^oopp/,/^ooop/{
   /^$/d
   s/aaa/machunyu/
   }
   ```

4. 命令之间用一个分号分隔，那么可以将多个 sed 命令放在同一行。

   ```shell
   n;d
   ```

### 替换

```shell
#[address]s/pattern/replacement/flags
```

| 修饰 Flags | 描述                                                         |
| ---------- | ------------------------------------------------------------ |
| n          | 1 到 512之间的一个数字，表示对文本模式中指定模式第 n 次出现的情况进行替换 |
| g          | 对模式空间的所有出现的情况进行全局更改。而没有 g 是通常只有第一次出现的情况被取代 |
| p          | 打印模式空间的内容。                                         |

```shell
#正则表达式在一行上重复匹配，而只需要对其中某个位置的匹配进行替换。
#将文本中每一行第一个是 pig 的转换为 test
sed s/pig/test/1 test_script
#将文本中的全部 root 替换成 mcy
sed s/root/mcy/g sed
#打印修改的行
sed -n s/root/mcy/gp sed
#在第二行，到第八行之间，替换以root开头的行，用mcy来替换，并显示替换的行
sed -n '2,8s/^root/mcy/gp' test
```

### 删除

```shell
#删除第2行，命令只影响一行
sed -e '2d' test
#删除最后一行
sed -e '$d' test
#删除除了第二行所有的行
sed -e '2!d' test
#删除文件中从50行到最后一行的所有行
sed -e '50,$d' test
#删除文件中的空行
sed -e '/^$/d' test
#删除两个地址包围的所有行
sed -e '/^oopp$/,/^ooop$/d' test
#删除第一行直到第一个空行的所有行
sed -e '1,/^$/d' test
```

### 追加、插入和更改

```shell
#追加 [line-address]a\ text
#插入 [line-address]i\ text
#更改 [address] c\ text

#在匹配的文本下面追加
sed '/pig/a\==========' test
#在匹配的文本上面插入
sed '/pig/i\==========' test
#将匹配的文本替换
sed '/pig/c\==========' test
```

## awk

- BEGIN{ 这里面放的是执行前的语句 }

- END {这里面放的是处理完所有的行后要执行的语句 }

- {这里面放的是处理每一行时要执行的语句}

- awk 命令的基本格式

  ```shell
  awk [options] 'script' file
  
  #options 这个表示一些可选的参数选项
  #script 表示 awk 的可执行脚本代码（一般被{} 花括号包围）。
  #file 这个表示 awk 需要处理的文件
  ```

### 自定义分隔符

awk 默认的分隔符为**空格和制表符**，awk 会根据这个默认的分隔符将每一行分为若干字段，用`$n（$1,$2,...）` 来表示每个字段。

```shell
#通过参数指定分隔符
awk -F ':' '{print $1}' /etc/passwd

#通过变量指定分隔符
awk 'BEGIN {FS=":"} {print $1}' /etc/passwd

#指定分隔符并指定输出分隔符
awk 'BEGIN {FS=":";OFS="---"} {print $1,$2}' /etc/passwd

#同时指定多个分隔符
echo 'Grape(100g)1980' | awk -F '[()]' '{print $1, $2, $3}'
```

### 内置变量

- 0这个表示文本处理时的当前行，1 表示文本行被分隔后的第 1 个字段列，2表示文本行被分割后的第2个字段列，2表示文本行被分割后的第2个字段列，3 表示文本行被分割后的第 3 个字段列，$n 表示文本行被分割后的第 n 个字段列。

- NR 表示文件中的行号，表示当前是第几行。

- NF 表示文件中的当前行被分割的列数，可以理解为 MySQL 数据表里面每一条记录有多少个字段，所以 NF就表示最后一个字段，NF就表示最后一个字段，(NF-1) 就表示倒数第二个字段。

- FS 表示 awk 的输入分隔符，默认分隔符为空格和制表符，可以对其进行自定义设置。

- OFS 表示 awk 的输出分隔符，默认为空格，也可以对其进行自定义设置。

- FILENAME 表示当前文件的文件名称，如果同时处理多个文件，它也表示当前文件名称。

- RS 行分隔符，用于分割行，默认为换行符。

- ORS 输出记录的分隔符，默认为换行符。

- ARGC 命令行参数的数目。

- ARGV 包含命令行参数的数组。

  ```shell
  #test.log
  #root:x:0:0:root:/root:/bin/bash
  #bin:x:1:1:bin:/bin:/bin/false
  #daemon:x:2:2:daemon:/sbin:/bin/false
  
  #表示打印输出文件的每一整行的内容
  awk '{print $0}' test.log 
  #表示打印输出文件的每一行的第1列内容
  awk '{print $1}' test.log 
  #通过对 $2 变量进行重新赋值，来隐藏每一行的第2列内容，并且用星号代替其输出
  awk -F ':' '{$2="***"; print $0}' test.log
  #打印每一行的倒数第二列
  awk '{print $(NF - 1)}' test.log
  #同时处理多个文件
  awk '{print FILENAME "\t" $0}' test.log test2.log
  #打印出了 6 倍数行之外的其他行
  awk 'NR % 6'
  #打印第 5 行之后内容，类似 tail -n +6 或者 sed '1,5d'
  awk 'NR > 5'
  #打印大于等于 6 列的行
  awk 'NF >= 6'
  #打印匹配 /foo/ 和 /bar/ 的行
  awk '/foo/ && /bar/'
  #打印包含 /foo/ 不包含 /bar/ 的行
  awk '/foo/ && !/bar/'
  #或
  awk '/foo/ || /bar/'
  #打印从匹配 /foo/ 开始的行到 /bar/ 的行，包含这两行
  awk '/foo/,/bar/'
  ```

### 使用变量

```shell
#脚本中声明和使用变量
awk '{var="hello world"; print var}' test.log
#声明"全局变量"，可以在任何花括号中使用
awk 'BEGIN {msg="hello world"} {print msg}' /etc/passwd
```

### 其他运算符

1. 数学运算符

   ```shell
   #+ 加法运算符
   #- 减法运算符
   #* 乘法运算符
   #/ 除法运算符
   #% 取余运算符
   
   awk '{a = 10; b = 30; print a + b}' test.log
   ```

2. 条件运算

   ```shell
   #< 小于
   #<= 小于或等于
   #== 等于
   #!= 不等于
   #> 大于
   #>= 大于或等于
   #~ 匹配正则表达式
   #!~ 不匹配正则表达式
   
   awk '{if ($3<300) print $0}' access.log
   ```

### 参考

```shell
#查询在 sed 中包含 root
awk '/root/' file
#根据 : 分隔，打印第一个
awk -F':' '{print $1}' file
#可以用任何计算值为整数的表达式来表示一个字段，而不只是数字和变量
echo a b c d | awk 'BEGIN {one=1;two=2} {print $(one+two)}'   # 结果 c
#规定输入分隔字符，输出分隔字符
cat sed | awk 'BEGIN {FS=":";OFS="---"} {print $1,$2}'
#函数 split 用法
echo 12:34:56 | awk '{split($0,a,":"); print a[2]}'
echo 12:34:56 | awk '{z = split($0,a,":"); print a[2],z}'
#查看最近哪些用户使用系统
last | grep -v "^$" | awk '{ print $1 }' | sort -nr | uniq -c
#打印长度超过72字符的行
awk 'length>72' test
#过滤文件中重复行
awk '!x[$0]++' test
#输出最常用的命令
history | awk '{a[$2]++}END{for(i in a){print a[i] " " i}}' | sort -rn | head
```

### 命令行脚本运行

```shell
#!/bin/awk -f
{print "hello world"}
#或者
#!/bin/sh
/bin/awk {print "hello world"}
```