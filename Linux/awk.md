## 运行程序

1. 文件 `awk 'program' input files`

   ```shell
   awk '$3 >0 {print $1}' emp.data
   ```

2. 在命令行中输入 `awk 'program'`后，awk 会将 program 应用到你接下来在终端输入的内容上面。

3. `awk -f progfile optional list of files`

## 比较运算

```
<        小于
<=       小于或等于
==       等于
!= 			 不等于
>=       大于或等于
>        大于
~        匹配
!~       不匹配
```

## 内置变量

- 0 这个表示文本处理时的当前行，1 表示文本行被分隔后的第 1 个字段列，2表示文本行被分割后的第2个字段列，2表示文本行被分割后的第2个字段列，3 表示文本行被分割后的第 3 个字段列，$n 表示文本行被分割后的第 n 个字段列。

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

## 模式

```html
1. BEGIN {statements}
	在输入被读取之前，statements 执行一次

2. END {statements}
	当所有输入被读取完毕之后，statements 执行一次

3. expression {statements}
	每碰到一个使 expression 为真的输入行，statements 就执行. expression 为真指的是其值非零或非空。

4. /regular expression/ {statements}
	当碰到这样一个输入行时, statements 就执行: 输入行含有一段字符串, 而该字符串可以被 regular expression 匹配。

5. compound pattern { statements}
	一个复合模式将表达式用 &&(AND), ||(OR), !(NOT), 以及括号组合起来; 当 compound pattern 为真时, statements 执行。

6. pattern1, pattern2 { statements}
	一个范围模式匹配多个输入行, 这些输入行从匹配 pattern1 的行开始, 到匹配 pattern2 的行结束 (包括这两行), 对这其中的每一行执行 statements


BEGIN 与 END 不与其他模式组合. 一个范围模式不能是其他模式的一部分. BEGIN 与 END 是唯一 两个不能省略动作的模式。
```

### BEGIN 与 END

1. BEGIN 和 END 这两个模式不匹配任何输入行。
2. 当 awk 从输入读取数据之前，BEGIN 的语句开始执行；当所有输入数据被读取完毕，END 的语句开始执行。于是，BEGIN 与 END 分别提供了一种控制初始化与扫尾的方式。
3. **BEGIN 与 END 不能与其他模式作组合** 。
4. 如果有多个 BEGIN, 与其关联的 动作会按照它们在程序中出现的顺序执行, 这种行为对多个 END 同样适用。

```shell
#指定分隔符并指定输出分隔符
awk 'BEGIN {FS=":";OFS="---"} {print $1,$2}' /etc/passwd
```

### 字符串匹配模式

```shell
cat 1.data
	/root:/bin/bash
  foo:ffff
  bar:foo
#1. /regexpr/
#	当当前输入行包含一段能够被 regexpr 匹配的子字符串时, 该模式被匹配。
	awk '/foo/' 1.data

#2. expression ~ /regexpr/
#	如果 expression 的字符串值包含一段能够被 regexpr 匹配的子字符时, 该模式被匹配。
	
#3. expression !~ /regexpr/
#	如果 expression 的字符串值不包含能够被 regexpr 匹配的子字符串, 该模式被匹配。


```

### 正则模式

### 复合模式

```shell
#打印匹配 /foo/ 和 /bar/ 的行
awk '/foo/ && /bar/'
#打印包含 /foo/ 不包含 /bar/ 的行
awk '/foo/ && !/bar/'
#或
awk '/foo/ || /bar/'
```

### 范围模式

