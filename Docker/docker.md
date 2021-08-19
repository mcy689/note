## 安装docker

```shell
docker ps -a //会列出所有容器，包括正在运行的和已经停止的
docker ps -l //列出最后一个运行的容器，无论其正在运行还是已经停止

#运行并未容器命名
	docker run --name mcy -i -t centos /bin/bash

# 重新启动已经停止的容器
	#通过ID启动已经停止运行的容器
		docker start 050e65747307
	#通过名字来启动
		docker start mcy

# Docker 容器重新启动的时候，会沿用 docker run 命令时指定的参数来运行。
  docker attach mcy #重新附着到容器的会话
```

## 基本使用

### 创建守护式容器

```shell
docker run --name daemon_dave -d centos /bin/sh -c "while true; do echo hello world; sleep 1; done"

# 查看容器的日志
	docker logs daemon_dave

# tail 形式查看log
	docker logs --tail 10 -f daemon_dave

# 查看容器内的所有进程
	docker top daemon_dave

# docker stats 命令，用来显示一个或多个容器的统计信息。
	docker stats daemon_dave

# 在容器内部运行进程，可以在容器内运行的进程有两种类型：后台任务和交互式任务。
	docker exec -d daemon_dave touch /root/test

# 该命令会在 daemon_dave 容器内创建一个新的 bash 会话。
	docker exec -t -i daemon_dave /bin/bash
```
### 停止守护式容器

```shell
# 通过名字
  docker stop daemon_dave
 
# 通过ID
  docker stop 050e65747307

# 查看最后 x 个容器，不论这些容器正在运行还是已经停止。
  docker ps -n x 
```

### 自动重启容器

```shell
docker run --restart=always --name daemon_dave -d centos /bin/sh -c "while true; do echo hello world; sleep 1; done"
  # --restart 标志被设置为 always。无论容器的退出代码是什么，Docker 都会自动重启该容器。
  # 还可以将这个标志设为 on-failure ，这样，只有当容器的退出代码为非 0 值的时候，才会自动重启。

  # 接受一个可选的重启次数参数。
  --restart=on-failure:5
```

### 查看容器的信息

```shell
docker inspect mcy
```

### 删除容器

```shell
docker rm 050e65747307

# 删除所有容器
  docker rm `docker ps -a -q`
```

## 镜像和仓库

Docker Hub 中有两种类型的仓库：用户仓库和顶层仓库。用户仓库的镜像都是由 Docker 用户创建的，而顶层仓库则是由 Docker 内部的人来管理的。

```shell
# 查看镜像列表
  docker images

# 拉取 Ubuntu 镜像
  docker pull ubuntu:12.04

# 运行一个带标签的 Docker 镜像
  docker run -t -i --name new_name ubuntu:12.04 /bin/bash

# 拉取镜像
  # 命令从镜像启动一个容器时，如果该镜像不在本地，Docker 会先从 Docker Hub 下载该镜像。如果没有指定具体的镜像标签，那么Docker 会自动下载 latest 标签的镜像。
  docker run  # 或者 先docker pull 再 run
  
 # 查找镜像
   docker search centos
```

### 构建镜像

1. 使用 docker commit 命令。

   ```shell
   # 基于 050e65747307 系统生成的 redis6.0 版本
   	docker commit 050e65747307 machunyu/redis6.0
   	# 增加额外信息
   	docker commit -m "A new redis context" 050e65747307 machunyu/redis6.0
   ```

2. 使用 docker build 命令和 Dockerfile 文件。

### 删除镜像

```shell
docker rmi
```

