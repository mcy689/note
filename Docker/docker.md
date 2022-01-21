## 核心概念

1. 镜像：镜像是创建 Docker容器的基础。
2. 容器：容器是镜像的一个运行实例，Docker 利用容器来运行和隔离应用。
3. 仓库：是 Docker 集中存放镜像文件的场所。

## 命令

### 镜像命令

```shell
docker image ls                  #查看所有的镜像
docker images                    #查看所有的镜像
docker image pull centos:8       #拉去指定镜像
docker rmi 或 docker image rm    #命令可以删除镜像
```

### 镜像标签

```shell
docker tag centos:centos8 mycentos:myc8 #给本地创建一个新的标签，其实是别名
```

### 搜索镜像

```shell
docker search
```

### 创建镜像

#### commit 基于已有的镜像

```shell
# 1. 在镜像上创建修改文件
    docker run -it centos:centos8 /bin/bash
    touch test #创建一个修改
# 2. 本地提交镜像
    docker commit -m "added a new file" -a "docker new_mcy" d89bdb3ad7fa test:0.1
```

<img src="./image/docker_commit.png" alt="docker_commit" style="zoom:50%;" />

#### 基于本地模版导入

#### 基于 Docker 创建

### 本地备份

```shell
# 1. 导出镜像 
  save -o centos8.tar centos:centos8
# 2. 本地导出 centos8.tar
# 3. 导入镜像
  docker load -i centos8.tar
```

### 上传镜像到仓库

```shell
#1. 先添加新的标签 machunyugo/centos:centos8。
docker tag centos:centos8 machunyugo/centos:centos8
#2. 用户 machunyugo 上传本地 centos:centos8 镜像。
docker push machunyugo/centos:centos8
```

## Docker 容器

1. 当 Docker 容器中指定的应用终极时，容器也会自动终止。
2. 容器是镜像的一个运行实例。所不同的是，镜像是静态的只读文件，而容器带有运行时需要的可写文件层。

```shell
# 会列出所有容器，包括正在运行的和已经停止的
  docker ps -a
# 列出最后一个运行的容器，无论其正在运行还是已经停止
  docker ps -l

# 创建
 docker create -it centos:centos8

# 运行
 docker start c79

# 合并上述命令
 docker run -it centos:centos8 /bin/bash

# docker run
  #运行并未容器命名, -d 参数是以守护态运行
	  docker run --name mcy -itd centos:centos8 /bin/bash
	
# 拉取镜像
  # 命令从镜像启动一个容器时，如果该镜像不在本地，Docker 会先从 Docker Hub 下载该镜像。如果没有指定具体的镜像标签，那么Docker 会自动下载 latest 标签的镜像。
  docker run  # 或者 先docker pull 再 run

# 查看某容器的输出可以使用如下命令
  docker logs d46ad28248e7
 	docker logs daemon_dave
 	# tail 形式查看log
	  docker logs --tail 10 -f daemon_dave
 
# 停止容器
 docker stop d46ad28248e7

# 自动清除掉所有处于停止状态的容器
 docker container prune

#重新附着到容器的会话
 docker attach mcy 

# 查看容器内的所有进程
	docker top daemon_dave

# docker stats 命令，用来显示一个或多个容器的统计信息。
	docker stats daemon_dave

# docker exec
  # 在容器内部运行进程，可以在容器内运行的进程有两种类型：后台任务和交互式任务。
	  docker exec -d daemon_dave touch /root/test

  # 该命令会在 daemon_dave 容器内创建一个新的 bash 会话。
	  docker exec -ti daemon_dave /bin/bash
```

### 备份容器

```shell
#导出
 # 1. 查看容器
  docker ps -a
 # 2. 导出容器
  docker export -o test_for_run.tar febe

#导入
  #1. 导入
	docker import test_for_run.tar mycentos:v1.0
	#2. 查看
	docker ps -a
```

<img src="./image/docker_export.png" alt="docker_export" style="zoom:50%;" />

<img src="./image/docker_import.png" alt="docker_import" style="zoom:50%;" />

### 查看容器















## 基本使用

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

## 绑定目录

```bash
# 例子
  docker run -d --name study -i -t --mount type=bind,src=/data,dst=/Users/machunyu/docker_dir centos  /bin/bash
```

## 容器的命令

```html
create
start
run
wait
logs

停止容器
  pause
  stop

启动容器
  start
  restart

查看容器
	ps
  ps -a
  ps -q

进入容器
	attach
	exec

删除容器
  docker rm

导出与导入
	docker export -o test_for_run.tar study
```

## 端口映射与容器互联

1. 允许映射容器内应用的服务端口到本地宿主主机。
2. 互联机制实现多个容器间通过容器名来快速访问。
3. -p (小写的)则可以指定要映射的端口，并且，在一 个 指定端口上只可以绑定 一 个容器。 支持的格式有 `IP:HosPort:ConainerPort` I` IP::ContainerPort` I `HostPort:ContainerPort`。

### 映射地址

```html
1. 映射所有接口地址
		docker run -d -p 5000:5000 raining/webapp py七hon app.py
		此时默认会绑定本地所有接口上的 所有地址。多次使用-p标记可以绑定多个端口。例如:
		docker run -d -p 5000:5000 -p 3000:80 training/webapp python app.py

2. 映射到指定地址的指定端口
		docker run -d -p 127.0.0.1:5000:5000 raining/webapp python app.py

3. 映射到指定地址的任意端口
		docker run -d -p 127.0.0.1::5000 training/webapp pyhon app.py

```

