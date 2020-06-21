## 流程

### 创建测试目录

```shell
 git init testgit
```

### 创建分支并创建冲突

```shell
# 新建文件
    touch 1.sh
    git add 1.sh
    git commit -m 'init 1.sh'
    git branch dev				# 再在 master 分支提交 commit，dev 分支就落后 master 分支了
    vim 1.sh  					# 修改一些东西
    git add 1.sh
    git commit -m '1.sh bug'

# 创建分支
	git checkout -b dev
	vim 1.sh   					#增加一些东西
	git add 1.sh
	git commit -m 'dev 1.sh'
```

### rebase 合并

```shell
git checkout dev	# 确保进入了待合并的分支
git rebase master	# 记录1
	# 报错以后不想和并了。
	git rebase --abort
	# 如果想跳过该冲突合并
	git rebase --skip
	# 修改冲突合并代码
	# 先解决冲突
	git add 1.sh
	git rebase --continue
```

1. 冲突记录

   ```shell
   First, rewinding head to replay your work on top of it...
   Applying: test dev
   Using index info to reconstruct a base tree...
   M       1.sh
   Falling back to patching base and 3-way merge...
   Auto-merging 1.sh
   CONFLICT (content): Merge conflict in 1.sh
   error: Failed to merge in the changes.
   Patch failed at 0001 test dev
   hint: Use 'git am --show-current-patch' to see the failed patch
   Resolve all conflicts manually, mark them as resolved with
   "git add/rm <conflicted_files>", then run "git rebase --continue".
   You can instead skip this commit: run "git rebase --skip".
   To abort and get back to the state before "git rebase", run "git rebase --abort".
   ```

### 最终的merge

```shell
git checkout master
git merge dev
# 查看一下分支线
git log --oneline --graph
```

