## 基础

```shell
#查看当前运行的 shell
echo $SHELL
#查看当前 Linux 系统安装的所有 Shell
cat /etc/shells
#查看 bash 版本
bash --version
#bash 在每行的结尾加上 \ ，bash 就会将下一行跟当前行放在一起解释
echo hello \
world
```

### 命令的组合符 `&&` 和 `||`

1. `&&` ，如果 `command1` 命令运行成功，则继续运行 `command2` 命令。

   ```shell
   command1 && command2
   ```

2. `||` ，如果 `command1` 命令运行失败，则继续运行 `command2` 命令。

   ```shell
   Command1 || Command2
   ```

### Type 命令

Type 命令用来判断命令的来源。

```shell
type echo
#echo is a shell builtin
type git
#git is hashed (/usr/local/git/bin/git)
```

## bash 扩展

1. `~ 扩展`

   ```shell
   # ~ 用户名，返回用户的家目录
   $ echo ~machunyu
   #/home/machunyu
   $ echo ~root
   #/root
   
   # ~/foo 表示家目录下的子目录 
   $ echo ~/foo
   #/home/machunyu/foo
   
   # 当前所在的目录
   $ echo ~+
   #/home/machunyu/foo
   ```

2. `? 扩展` ，`?` 字符代表文件路径里面的任意单个字符，不包括空字符。

   ```shell
   ls ?.txt 	#a.txt b.txt
   ls ??.txt   #ac.txt nc.txt
   ```

3. `*` 扩展，`*` 字符代表文件路径里面的任意数量的任意字符，包括零个字符。

   ```shell
   rm ./*.log
   ```

4. `[]` 扩展

   ```shell
   ls [ab].txt    #a.txt b.txt
   ```

5. `[start-end]` 扩展

   ```shell
   ls [a-c].txt  			#a.txt b.txt c.txt
   echo report[!1–3].txt 	#表示排除1、2和3。
   ```

6. `{}`扩展，表示分别扩展成大括号里面的所有值，各个值之间使用逗号分隔。

   ```shell
   echo {1 , 2}
   #大括号可以嵌套。
   echo {j{p,pe}g,png}
   ```

7. `{start..end}` 扩展

   ```shell
   echo {a..c}  		# a b c
   echo {c..a}  		# c b a
   echo d{a..d}g 		# dag dbg dcg ddg
   #{start..end..step}
   echo {0..8..2}		# 0 2 4 6 8
   ```

8. `$(ls)` 可以扩展成另一个命令的运行结果，该命令的所有输出都会作为返回值。

   ```shell
   echo $(ls)
   ```

9. `$(())` 算术扩展

   ```shell
   echo $((2+2))		#4
   ```

10. 字符类

   - `[[:alnum:]]`：匹配任意英文字母与数字
   - `[[:alpha:]]`：匹配任意英文字母
   - `[[:blank:]]`：空格和 Tab 键。
   - `[[:cntrl:]]`：ASCII 码 0-31 的不可打印字符。
   - `[[:digit:]]`：匹配任意数字 0-9。
   - `[[:graph:]]`：A-Z、a-z、0-9 和标点符号。
   - `[[:lower:]]`：匹配任意小写字母 a-z。
   - `[[:print:]]`：ASCII 码 32-127 的可打印字符。
   - `[[:punct:]]`：标点符号（除了 A-Z、a-z、0-9 的可打印字符）。
   - `[[:space:]]`：空格、Tab、LF（10）、VT（11）、FF（12）、CR（13）。
   - `[[:upper:]]`：匹配任意大写字母 A-Z。
   - `[[:xdigit:]]`：16进制字符（A-F、a-f、0-9）。

   ```shell
   ls -l [[:upper:]]* 		# 查看首字母开头的文件名。
   ```

## 变量

Bash 没有数据类型的概念，所有的变量值都是字符串。

```shell
a=z                     # 变量 a 赋值为字符串 z
b="a string"            # 变量值包含空格，就必须放在引号里面
c="a string and $b"     # 变量值可以引用其他变量的值
d="\t\ta string\n"      # 变量值可以使用转义字符
e=$(ls -l foo.txt)      # 变量值可以是命令的执行结果
f=$((5 * 7))            # 变量值可以是数学运算的结果

unset $a				# 删除变量
```

### 特殊变量

1. `$?` 为上一个命令的退出码，用来判断上一个命令是否执行成功。返回值为`0`，表示上一个命令执行成功；如果是非零，上一个命令执行失败。

   ```shell
   $ lssd
   -bash: lssd: command not found
   $ echo $?
   127
   ```

2. `$$` 为当前 Shell 的进程ID。

   ```shell
   echo $$
   ```

### declare 命令

命令可以声明一些特殊类型的变量，为变量设置一些限制，比如声明只读类型的变量和整数类型的变量。

```shell
# -i 参数声明整数变量以后，可以直接进行数学运算。
    declare -i v1=1 v2=2
    declare -i result
    result=v1+v2
    echo $result
# -r 参数可以声明只读变量，无法改变变量值，也不能 unset 变量。
	declare -r foo=9
	unset $foo	#报错
	foo=90		#报错
# -u 参数声明变量为大写字母，可以自动把变量值转成大写字母。
# -l 参数声明为小写字母，可以自动把变量值转成小写字母。
	declare -u foo
	declare -u bar
	foo=upper
	bar=LOWER
	echo $foo	#UPPER
	echo $bar	#lower
# -f 参数输出当前环境的所有函数，包括它的定义。
# -F 参数输出当前环境的所有函数名，不包括函数定义。
```

### let 命令

let 命令声明变量时，可以直接执行算术表达式。

```shell
let foo=1+2
echo $foo #3
```

## 字符串

### 字符串长度

```shell
# 格式 ${#varname}
test='this is test'
echo ${#test}		#12
```

### 子字符串

1. `格式 ${varname:offset:length}`

2. 省略 length，则从位置 offset 开始，一直返回到字符串的结尾

3. 如果offset为负值，表示从字符串的末尾开始算起。注意，**负数前面必须有一个空格**， 以防止与`${variable:-word}` 的变量的设置默认值语法混淆。这时，如果还指定length，则length不能小于零。

   ```shell
   test='this is test'
   echo ${test:8:3}	# tes
   echo ${test:2}		#is is test
   echo ${test: -4:4}	# test
   ```

### 搜素和替换

```shell
test="/home/cam/book/long.file.name"
phone="555-456-1414"
path="/home/cam/foo/foo.name"

# 开头
echo ${test#/*/}			# cam/book/long.file.name
echo ${test##/*/}			# long.file.name
echo ${phone/#555/400}		# 400-456-1414

# 尾部
echo ${test%.*}				# /home/cam/book/long.file
echo ${test%%.*}			# /home/cam/book/long
echo ${phone/%1414/666}		# 555-456-666

# 任意位置的模式匹配
echo ${path/foo/bar}		# /home/cam/bar/foo.name
echo ${path//foo/bar}		# /home/cam/bar/bar.name

# 分隔符从:换成换行符。
echo -e ${PATH//:/'\n'}
```

### 改变大小写

```shell
foo=heLLo
echo ${foo^^}	#HELLO
echo ${foo,,} 	#hello
```

## 脚本参数

```shell
script.sh word1 word2 word3
```

* `$0` 脚本文件名，即`script.sh`
* `$1~$9` 对应脚本的第一个参数到第九个参数。
* `$#` 参数的总数。
* `$@` 全部的参数，参数之间使用空格分隔。
* `$*` 全部的参数，参数之间使用变量 `$IFS` 值的第一个字符分隔，默认为空格，但可以自定义。

### shift 参数

1. `shift`命令可以改变脚本参数，每次执行都会移除脚本当前的第一个参数（`$1`），使得后面的参数向前一位，即`$2`变成`$1`、`$3`变成`$2`、`$4`变成`$3`，以此类推。

2. `shift`命令可以接受一个整数作为参数，指定所要移除的参数个数，默认为`1`。

   ```shell
   #!/bin/bash
   
   echo "一共输入了 $# 个参数"
   
   while [ "$1" != "" ]; do
     echo "剩下 $# 个参数"
     echo "参数：$1"
     shift					# 或者 shift 1
   done
   ```

### [getopts 命令](<https://wangdoc.com/bash/script.html>)

`getopts`命令用在脚本内部，可以解析复杂的脚本命令行参数，通常与`while`循环一起使用，取出脚本所有的带有前置连词线（`-`）的参数。

### 配置项参数终止符 `--`

```shell
myPath='-1'
ls $myPath			# 相当于 ls -l
ls -- $myPath		# ls: cannot access -1: No such file or directory
```

### source 

1. 在脚本内部加载外部库。

   ```shell
   source ./lib.sh
   ```

2. 重新加载配置文件

   ```shell
   source ~/.bashrc
   ```

## 写法

```shell
# 只有 root 用户执行命令
if [ $(id -u) != "0" ]; then
  echo "根用户才能执行当前脚本"
  exit 1
fi
```







---

>说明：该笔记是在看**[阮一峰《Bash 脚本教程》](https://wangdoc.com/bash)**时的记录。