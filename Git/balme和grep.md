## `git blame`

### 作用

1. `git blame ` 显示指定文件是什么人在什么时间修改过。
2. 不会显示已删除和替换的行的任何信息。

### 示例

```shell
# 统计该文件每个人修改多少行。
git blame --line-porcelain take.log | sed -n 's/^author //p' | sort | uniq -c | sort -rn

# 显示该文件的40到60行
git blame -L 40,60 take.log
```

## `git grep`

### 作用

在工作树中的跟踪文件中查找指定的信息。

### 示例

```shell
# 查找指定的字符串
git grep checkMoney
# 查找指定字符串的行
git grep -n checkMoney
```



