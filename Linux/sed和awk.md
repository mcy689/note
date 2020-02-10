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

关于awk脚本，我们需要注意两个关键词BEGIN和END。

- BEGIN{ 这里面放的是执行前的语句 }
- END {这里面放的是处理完所有的行后要执行的语句 }
- {这里面放的是处理每一行时要执行的语句}

```shell
#查询在 sed 中包含 root
awk '/root/' sed
#根据 : 分隔，打印第一个
awk -F':' '{print $1}' sed
#可以用任何计算值为整数的表达式来表示一个字段，而不只是数字和变量
echo a b c d | awk 'BEGIN {one=1;two=2} {print $(one+two)}'   # 结果 c
#规定输入分隔字符，输出分隔字符
cat sed | awk 'BEGIN {FS=":";OFS="---"} {print $1,$2}'
#函数 split 用法
echo 12:34:56 | awk '{split($0,a,":"); print a[2]}'
echo 12:34:56 | awk '{z = split($0,a,":"); print a[2],z}'
#ARGV
BEGIN { for (x =0; x< ARGV; ++x)
			print ARGV[x]
}
```

### 命令行脚本运行

```shell
#!/bin/awk -f
{print "hello world"}

#或者

#!/bin/sh
/bin/awk {print "hello world"}
```