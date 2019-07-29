# rsync

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

4. 镜像备份，本地到远程。

   ```shell
   rsync -avz --delete ./dir90/ user@remoteip:/home/user/dir1/
   #本地不存在的文件，远程文件删除。
   ```

5. 镜像备份，远程到本地。

   ```shell
   rsync -acP --delete user@remoteip:/home/machunyu/dir1/ ./dir90/
   #远程不存在的文件，本地文件会被删除。
   ```

## ssh登录

```shel
rsync -avz -e "ssh -p $port" /local/path/ user@remoteip:/path/to/files/
```

