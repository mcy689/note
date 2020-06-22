## 应用场景

1. 有时，当你在项目的一部分上已经工作一段时间后，所有东西都进入了混乱的状态，而这时你想要切换到另一个分支做一点别的事情。 问题是，你不想仅仅因为过会儿回到这一点而为做了一半的工作创建一次提交。
2. `git stash` 命令会处理工作目录的脏的状态（即修改的跟踪文件与暂存改动）然后将未完成的修改保存到一个栈上，而你可以在任何时候重新应用这些改动。

## 示例

```shell
# 存储
	git stash

# 查看存储在栈上
	git stash list
	#stash@{0}: WIP on master: 049d078 added the index file
	#stash@{1}: WIP on master: c264051 Revert "added file_size"
	#stash@{2}: WIP on master: 21d80a5 added number to log
	
# 重新应用存储的信息
	git stash apply				# 默认应用最新的
	git stash apply stash@{2}	# 通过名字指定应用
	
# 文件的改动被重新应用了，但是之前暂存的文件却没有重新暂存。 想要那样的话，必须使用 --index 选项来运行 git stash apply 命令，来尝试重新应用暂存的修改。
	git stash apply --index

# git stash drop 移除堆栈的信息，并应用它
	git stash drop stash@{2}
```

## 存储未被跟踪的文件

```shell
git stash -u
```
