# shell 脚本

## 变量和环境变量

1. 在 Bash 中，每个变量的值都是字符串。无论你给变量赋值时有没有使用引号，值都会以字符串的形式存储。

   ```shell
   # $PATH
   # 通常定义在 /etc/environment 或者 /etc/profile 或者 ~/.bashrc 中
   echo $PATH
   ```

2. 向 `$PATH` 添加一条新路径

   ```shell
   exprot PATH="$PATH:/home/usr/bin"
   #或者
   PATH="$PATH:/home/usr/bin"
   exprot PATH
   ```

3. 获取字符串的长度

   ```shell
   length=${#var}
   echo $length
   ```

4. 识别当前的 shell 脚本

   ```shell
   echo $SHELL
   ```

5. 检查是否为超级用户

   ```shell
   if[$UID -ne 0];then
   echo Non root user.Please run as root;
   else
   echo "Root user"
   fi
   ```

## 数学运算





