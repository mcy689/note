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

### d 命令

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

