## [应用场景](<https://git-scm.com/book/zh/v1/Git-%E5%88%86%E6%94%AF-%E5%88%A9%E7%94%A8%E5%88%86%E6%94%AF%E8%BF%9B%E8%A1%8C%E5%BC%80%E5%8F%91%E7%9A%84%E5%B7%A5%E4%BD%9C%E6%B5%81%E7%A8%8B>)

- 长期分支: 同时拥有多个开放的分支, 每个分支用于完成特定的任务, 随着开发的推进, 你可以随时把某个特性分支的成果并到其他分支中。**master分支中保留完全稳定的代码, develop 或者 next 平行分支, 专门用于后续的开发。**
- 特性分支: 一个特性分支是指一个短期的, 用来实现单一特性或与其相关工作的分支, 在提交了若干更新后, 把它们合并到主干分支, 然后删除.  ( 前提是分支的代码已经比较成熟了)。
- 远程分支 : 是对远程仓库中的分支的索引, 它们是一些无法移动的本地分支; 只有在Git进行网络交互是才会更新. 远程分支就像书签, 提醒着你上次连接远程仓库是上面个分支的位置。

## 远程分支

> 远程分支（remote branch）是对远程仓库中的分支的索引。它们是一些无法移动的本地分支；只有在 Git 进行网络交互时才会更新。远程分支就像是书签，提醒着你上次连接远程仓库时上面各分支的位置。
>
> 我们用 `(远程仓库名)/(分支名)` 这样的形式表示远程分支。比如我们想看看上次同 `origin` 仓库通讯时 `master` 分支的样子，就应该查看 `origin/master` 分支。

## 查看

```shell
# 查看本地分支列表
	git branch

# 查看各个分支最后一个提交对象的信息	
	git branch -v

# 查看哪些分支已经被并入当前分支
	git branch --merged

# 查看哪些分支尚未和当前分支合并
	git branch --no-merged
	
# 这会将所有的本地分支列出来并且包含更多的信息，如每一个分支正在跟踪哪个远程分支与本地分支是否是领先、落后或是都有。
	git branch -vv
```

![20180527225732](./img/20180527225732.png)

## 创建分支

```shell
# 创建本地分支
    git branch serverfix	# 创建
    git checkout serverfix  # 切换

# 创建本地分支并切入新创建的分支
	git checkout -b serverfix

# 跟踪远程分支	
	git checkout -b [分支名] [远程名]/[分支名]
```

## 跟踪分支

> 从一个远程跟踪分支检出一个本地分支会自动创建一个叫做 “跟踪分支”（有时候也叫做 “上游分支”）。 跟踪分支是与远程分支有直接关系的本地分支。 如果在一个跟踪分支上输入 `git pull`，Git 能自动地识别去哪个服务器上抓取、合并到哪个分支。
>
> 当克隆一个仓库时，它通常会自动地创建一个跟踪 `origin/master` 的 `master` 分支。

```shell
git branch -u origin/remote_branch your_branch
```



## 推送分支

```shell
# git push (远程仓库名) (分支名)
	git push origin sereverfix  # 推送本地到远程
```

## 删除

```shell
# 删除无用（该分支代码已经并入其他分支，如 master）分支
	git branch -d serverfix

# git push [远程名] :[分支名]，该处必须保留空格。
	git push origin :fix
```

## 版本回退

1. 使用 `git log` 查看实际工作中历史记录

   ![20180627001305](./img/20180627001305.png)

2. Git必须知道当前版本是哪个版本，在Git中，用`HEAD`表示当前版本，也就是最新的提交34aca0...`（注意我的提交ID和你的肯定不一样），上一个版本就是`HEAD^`，上上一个版本就是`HEAD^^`，当然往上100个版本写100个`^`比较容易数不过来，所以写成`HEAD~100`。 

   ```shell
   #回退上一个版本
   	git reset --hard HEAD^
   	git reset --hard HEAD～1
   #回退到指定版本
   	git reset --hard 34aca0a6e4670b882
   ```

3. Git提供了一个命令`git reflog`用来记录你的每一次命令： 

   ![20180627001945](./img/20180627001945.png)

4. 撤销修改

   - 命令`git checkout -- readme.txt`意思就是，把`readme.txt`文件在工作区的修改全部撤销，这里有两种情况：
   - 一种是`readme.txt`自修改后还没有被放到暂存区，现在，撤销修改就回到和版本库一模一样的状态；
   - 一种是`readme.txt`已经添加到暂存区后，又作了修改，现在，撤销修改就回到添加到暂存区后的状态。
   - 总之，就是让这个文件回到最近一次`git commit`或`git add`时的状态。
   - `git checkout -- file`命令中的`-- `很重要，没有`--`，就变成了“切换到另一个分支”的命令，我们在后面的分支管理中会再次遇到`git checkout`命令。 

## 示例

1. 初始化

   ```shell
   # 1. 创建本地目录
   	mkdir test
   # 2. 初始化本地
   	git init
   # 3. 添加远程仓库，即本地和远程建立关联
   	cd test
   	git remote add origin git@bitbucket.org:machunyu/test.git
   # 4. 拉取本地远程代码
   	git pull origin master
   # 5. 设置本地 master 分支跟踪远程 master 分支，这样设置以后直接 git pull 就可以拉取代码
   	git branch -u origin/remote_branch your_branch # 两者等价
   	git branch --set-upstream-to=origin/remote_branch  your_branch
   ```

2. [远程分支](<https://git-scm.com/book/zh/v1/Git-%E5%88%86%E6%94%AF-%E8%BF%9C%E7%A8%8B%E5%88%86%E6%94%AF>)

   ```shell
   #1. 推送远程分支。  other1
   	# 语意：取出我在本地的 serverfix 分支，推送到远程仓库的 serverfix 分支中去
   	git push origin fix
   	# 语意：上传我本地的 serverfix 分支到远程仓库中去，仍旧称它为 serverfix 分支
   	git push origin fix:fix
   #2. 拉取远程分支。	other2
   	git fetch origin
   #3. 创建本地分支或者合并远程分支到工作目录中。other2
   	# 值得注意的是，在 fetch 操作下载好新的远程分支之后，你仍然无法在本地编辑该远程仓库中的分支。换句话说，在本例中，你不会有一个新的 serverfix 分支，有的只是一个你无法移动的 origin/serverfix 指针。
   	#如果要把该远程分支的内容合并到当前分支，可以运行。
   	git merge origin/fix。
   	#如果想要一份自己的 serverfix 来开发，可以在远程分支的基础上分化出一个新的分支来：
   	git checkout -b fix origin/fix
   ```

