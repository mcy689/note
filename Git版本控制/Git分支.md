#### 分支 (Git保存的不是文件差异或者变化量, 而只是一系列文件快照)

1. 创建分支

   ```shell
   git branch testing
   ```

2. 切换分支

   ```shell
   git checkout testing
   ```

3. 分支的新建与合并

   * 流程

     >实际工作中大体的流程
     >
     >1. 开发某个网站
     >2. 为实现某个新的需求, 创建一个分支
     >3. 在这个分支上开展工作
     >
     >假设此时, 有一个很严重的问题需要紧急修补,
     >
     >1. 返回到原先已经发布到生产服务器上的分支
     >2. 为这次紧急修补建立一个新分支, 并在其中修复问题
     >3. 通过测试后, 回到生产服务器所在的分支, 将修补分支合并进来, 然后推送到生产服务器上
     >4. 切换到之前实现新需求的分支, 继续工作

   * 命令

     1. `git checkout -b branchname`

        ```shell
        # 这个命令相当于运行了
        git branch testing   # 创建
        git checkout testing # 切入
        ```

     2. `git branch -d branchname` 删除某个无用的分支

     3. 合并某个分支

        ```shell
        git checkout master
        git merge branchname
        ```

   ​