# rsync

* 是类 unix 系统下的数据镜像备份工具。可以方便的实现本地，远程备份。
* rsync 优于其他工具的重要一点就是支持增量备份。

## rsync 的命令格式

```shell
# 本地模式
rsync [OPTION...] SRC DEST
# 远程 Push
rsync [OPTION...] SRC [USER@]HOST:DEST
# 远程 Pull
rsync [OPTION...] [USER@]HOST:SRC DEST
# 通过 Rsync daemon Pull
rsync [OPTION...] [USER@]HOST::SRC DEST
rsync [OPTION...] rsync://[USER@]HOST[:PORT]/SRC... [DEST]
# 通过 Rsync daemon Push
rsync [OPTION...] SRC [USER@]HOST::DEST
rsync [OPTION...] SRC... rsync://[USER@]HOST[:PORT]/DEST
```

## 示例

1. 拷贝本地文件

   ```shell
   rsync -a ./dir1/ ./backDir1/
   ```

2. 远程 shell 拷贝到本地。

   ```shell
   rsync -avz user@remoteip:/home/user/dir1/  ./dir90/
   ```

3. 本地到远程。

   ```shell
   rsync -avz dir90/ user@remoteip:/home/user/dir1/
   ```

4. 本地到远程，本地不存在的文件，远程文件删除。

   ```shell
   rsync -avz --delete ./dir90/ user@remoteip:/home/user/dir1/
   ```

5. 远程到本地，远程不存在的文件，本地文件会被删除。

   ```shell
   rsync -avz --delete user@remoteip:/home/machunyu/dir1/ ./dir90/
   ```

## 常用参数

1. `a` 表示以递归方式传输文件，并保持所有文件属性

   ```shell
   rsync -a dir/ dir
   ```

2. `z` 对备份的文件在传输时进行压缩处理。

3. `--timeout` 超时时间，单位为秒。

4. `-stats` 给出某些文件的传输状态。

5. `-n` 测试现实哪些文件将被传输。

6. `--delete`  删除那些DST中SRC没有的文件。

7. `--exclude`  指定排除不需要传输的文件模式。

   ```shell
   rsync -a --exclude=.git SRC DST
   ```

8. `--delete-excluded`  同样删除接收端那些被该选项指定排除的文件。

   ```shell
   rsync -a --exclude=1.php --delete-excluded t1/ t2/
   ```

9. `-t, --times ` 保持文件时间信息。

   ```shell
   rsync -at t1/ t2/
   ```

10. `-u, --update`  仅仅进行更新，也就是跳过所有已经存在于DST，并且文件时间晚于要备份的文件，不覆盖更新的文件。

11. `-v` 详细模式输出。