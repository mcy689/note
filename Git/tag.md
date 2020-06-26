## 场景

> Git 可以给仓库历史中的某一个提交打上标签，以示重要。 比较有代表性的是人们会使用这个功能来标记发布结点（ `v1.0` 、 `v2.0` 等等）。 在本节中，你将会学习如何列出已有的标签、如何创建和删除新的标签、以及不同类型的标签分别是什么。

## 创建 tag

### 轻量 tag

```shell
git tag v1.0
```

### 附注 tag

```shel
git tag -a v1.4 -m '这是描述信息'
```

### 后期打 tag

```shell
git tag -a v1.2 9fceb02 # commit 提交历史
```

## 共享 tag

默认情况下，`git push` 命令并不会传送标签到远程仓库服务器上。**在创建完标签后你必须显式地推送标签到共享服务器上**。

```shell
# git push origin [tagname]
git push origin v1.5

# 批量推送标签，会把所有不在远程仓库服务器上的标签全部传送到那里
git push origin --tags
```

## 查看 tag

```shell
# 查看全部标签
git tag

# 查看指定标签
git tag -l 'v1.8.5*'
```

## 删除 tag

```shell
# 删除本地 tag
git tag -d [tag]
# 删除远程 tag
git push origin :refs/tags/[tagName]
```

