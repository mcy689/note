## 手动安装

1. 拉取 centos 最新镜像

   ```bash
   docker pull centos
   ```

2. 运行该镜像

   ```bash
   docker run --name basic -d -it  centos /bin/bash
   ```

3. 登录到 `basic` 容器中

   ```bash
   docker exec -it basic /bin/bash
   ```

4. 安装以及配置ssh

   * 安装

   ```bash
   dnf install openssh-server -y
   ```

   * 生成密钥文件

   ```bash
   ssh-keygen -t rsa -b 2048 -f /etc/ssh/ssh_root_rsa_key
   ssh-keygen -t ecdsa -b 256 -f /etc/ssh/ssh_host_ecdsa_key
   ssh-keygen -t ed25519 -b 256 -f /etc/ssh/ssh_host_ed25519_key
   ssh-keygen -t rsa -f /etc/ssh/ssh_host_rsa_key
   ```

   * 修改配置文件

   ```bash
   vi /etc/ssh/sshd_config
   ```

   * 启动 sshd 服务

   ```bash
   /usr/sbin/sshd -D &
   ```

   * 配置自动启动脚本 `vi /run.sh` ，并给权限 `chmod +x /run.sh` 

   ```bash
   #!/bin/bash
   /usr/sbin/sshd -D
   ```

5. 配置 root 用户的密钥登录

   * 生成密钥文件

     ```bash
     ssh-keygen -t rsa -b 2048 -f /root
     ```

   * 将密钥公钥文件复制到 `~/.ssh` 文件夹中

     ```bash
     mkdir ~/.ssh
     mv /root/id_rsa.pub authorized_keys
     ```

   * 修改 `/etc/ssh/sshd_config`

     **这里有一点很重要，在不是 docker 的环境中不要在你配置密钥登录成功之前，将 PasswordAuthentication 设置no，否则你密钥登录不了，然后又禁止密码登录，就悲剧了；在密钥登录设置成功之后，可以将PasswordAuthentication 设置为no，禁用密码登录了，比较安全** 

     ```html
     # 打开该文件中的这个选项
       PubkeyAuthentication yes
     
     # 设置密码不能登录
       PasswordAuthentication no
     ```

6. 退出后，复制 root 账号的登录密钥

   ```bash
             #容器中的路径         本地路径
   docker cp basic:/root/id_rsa ~/.ssh/docker_root_rsa
   ```

7. 保存镜像

   ```bash
   docker commit basic sshd:centos
   
   # 查看镜像是否生成
   docker images
   ```

8. 运行新的包含了ssh登录的镜像

   ```bash
   docker run -p 10022:22 -d sshd:centos /run.sh
   ```

9. 登录新启动的容器。

   ```bash
   ssh 127.0.0.1 -p 10022 -l root -i ~/.ssh/docker_root_rsa
   ```

10. 其他

   * 相关 `docker` 命令

     ```html
     docker images 查看镜像文件
     docker ps  查看正在运行的容器
     docker rmi 删除镜像
     docker rm 删除容器
     ```

   * 报错

     ```html
     1. 在步骤8中，容易忘记给启动脚本 `run.sh` 执行权限，导致权限不足。
     2. 登录以后提示信息
     		"System is booting up. Unprivileged users are not permitted to log in yet. Please come back later. For technical details, see pam_nologin(8)."
       
       需要删除 nologin 文件
          ls -l /run/nologin
          rm /run/nologin
     ```

## dockerfile 搭建 ssh

1. 目录结构

   ```html
   authorized_keys
   dockerfile
   id_rsa_ssh
   id_rsa_ssh.pub
   ```

2. 创建dockerfile文件

   ```shell
   touch dockerfile
   ```

3. 创建密钥文件

   ```shell
   # 创建密钥文件
   ssh-keygen -t rsa
   # 重命名公钥文件
   cat id_rsa_ssh.pub > authorized_keys
   ```

4. 编写 dockerfile 文件

   ```dockerfile
   FROM centos:centos8
   
   MAINTAINER "nideshijian@gamil.com"
   
   RUN dnf install openssh-server -y \
       && ssh-keygen -t rsa -b 2048 -f /etc/ssh/ssh_root_rsa_key \
       && ssh-keygen -t ecdsa -b 256 -f /etc/ssh/ssh_host_ecdsa_key \
       && ssh-keygen -t ed25519 -b 256 -f /etc/ssh/ssh_host_ed25519_key \
       && ssh-keygen -t rsa -f /etc/ssh/ssh_host_rsa_key \
       && sed -ri 's/PasswordAuthentication yes/PasswordAuthentication no/g' /etc/ssh/sshd_config \
       && mkdir -p /root/.ssh \
       && mkdir /var/run/sshd
   
   #复制配置文件到相应位置
   ADD authorized_keys /root/.ssh/authorized_keys
   
   # 开放端口
   EXPOSE 22
   
   # 设置自启动命令
   CMD /usr/sbin/sshd -D
   ```

5. 使用 build 生成镜像

   ```dockerfile
   # 生成镜像
     docker build -t sshd:dockerfile .
   
   # 运行镜像
     docker run -d -p 8880:22 sshd:dockerfile
   ```

