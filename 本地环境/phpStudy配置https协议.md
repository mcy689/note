#### phpstudy环境下配置https协议

1. 配置中加载`https` 协议的证书的路径为绝对路径的报错

   ```html
   2018/05/16 22:33:30 [emerg] 1092#6460: BIO_new_file("D:phpstudy/nginx/ca/server.crt") failed (SSL: error:02001003:system library:fopen:No such process:fopen('D:phpstudy/nginx/ca/server.crt','r') error:2006D080:BIO routines:BIO_new_file:no such file)
   ```

   __问题分析：只要写绝对路径，就会报错，无论windows还是linux，怀疑是对路径的处理有问题__ 

   __解决办法 :__  

   * `windows`下，将证书文件放到`conf`目录下
   * `linux`下，将证书文件放到`nginx.conf`所在的目录下

2. `https` 协议默认监听443端口, 但是被占用了

   * 如果我们的电脑既安装了`VMware` 虚拟机软件，又安装了`XAMPP` 或者 `phpstudy` ，这是后就会产生443端口被占用的情况

     **提示信息说明：** 443号端口被`vmware-hostd.exe` 进程占用了，导致Apache无法启动。

   * **解决方法有两种**：

     - 修改 `httpd-ssl.conf` 配置文件，将443端口改为其他空闲的端口（如4430）。
     - 将 `vmware-hostd.exe` 的自动启动改为手动启动并将其停止。在桌面的计算机图标点击鼠标右键，选择 “管理→服务和应用程序→服务”，将显示名称为`VMware Workstation Server` 的服务的启动类型改为手动，并停止该服务即可。

     **说明：** 上面两种方法中的任何一种都可以。第二种方法一般也不会影响`VMware`的正常使用。`VMware Workstation Server`  服务的描述信息为`Remote access service for registration and management of virtual machines`，也就是说它与远程管理`VMware`有关。而我们一般也不会使用到`VMvare` 的远程访问功能，所以第二种方法基本没有影响。

3. https协议的配置

   ```html
   openssl x509 -req -in server/server-req.csr -out server/server-cert.pem -signkey  server/server-key.pem -CA ca/ca-cert.pem -CAkey ca/ca-key.pem -CAcreateserial -days 3650
   ```

   ​

   ​

   ​