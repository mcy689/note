## sed

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

### 分组命令

```shell
#删除oopp-ooop输入块中的空行，而且它还使用替换命令s，改变了aaa
/^oopp/,/^ooop/{
/^$/d
s/aaa/machunyu/
}
```