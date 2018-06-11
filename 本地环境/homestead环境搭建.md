## homestead 环境搭建

1. 下载

   ```html
   https://www.vagrantup.com/downloads.html   vagrant下载
   https://www.virtualbox.org/wiki/Downloads  virtualbox下载
   
   https://ninghao.net/blog/1566   Vagrant 本地环境搭建日志
   ```

2. laravel参考文档地址

   ```html
   http://laravelacademy.org/post/7658.html
   http://laravelacademy.org/post/354.html
   ```

### 安装遇到的问题

1. 安装 virtualbox 启动时遇到的问题

   ```html
   错误代码:
   Failed to instantiate CLSID_VirtualBox w/ IVirtualBox, but CLSID_VirtualBox w/ IUnknown works.
   PSDispatch looks fine. Weird.
   
   解决办法：运行  regedit 注册表 在注册表里面进行修改
   
   HKEY_CLASSES_ROOT\CLSID\{00020420-0000-0000-C000-000000000046}
   InprocServer32 修改为C:\Windows\system32\oleaut32.dll
   HKEY_CLASSES_ROOT\CLSID\{00020424-0000-0000-C000-000000000046}
   InprocServer32 修改为C:\Windows\system32\oleaut32.dll
   ```

2. 安装box遇到的问题 (解决安装laravel/homestead vagrant环境报"A VirtualBox machine with the name 'homestead-7' already exists."的错误 )

   >当键入homestead up时，却报出
   >
   >**==> default: Importing base box 'laravel/homestead'...**
   >
   >**==> default: Matching MAC address for NAT networking...**
   >
   >A VirtualBox machine with the name 'homestead' already exists.
   >
   >Please use another name or delete the machine with the existing
   >
   >name, and try again.
   >
   >这样的错误
   >
   >1. vagrant global-status 查看虚拟器，
   >2. VBoxManage list vms 获取虚拟机列表，__运行这个目录需要在 `Oracle VM VirtualBox` 软件的同级目录下查找。__ 
   >3. VBoxManage unregistervm homestead --delete
   >
   >之后，重新运行homestead up之后 一切回归正常

### homestead默认的一些环境和账户密码

1. MySQL的默认账户密码

   ```html
   用户名：homestead
   密码：  secret
   ```


### 配置文件（homestead.yaml）的设置说明

1. 文件说明

   ```html
   ip: "192.168.10.10"  #虚拟机的地址
   memory: 2048
   cpus: 1
   provider: virtualbox   #虚拟机平台，用virtualbox装的一定要确认这里是virtualbox
   
   authorize: ~/.ssh/id_rsa.pub     #ssh的公钥
   
   keys:
       - ~/.ssh/id_rsa   #ssh的私钥，配置了ssh以后，登录虚拟机可以直接在终端输入homestead ssh进入
   
   folders:  #设置文件夹机映射关系
       - map: /Users/codingLady/Code   #需要映射到虚拟机的本地机器的文件夹
         to: /home/vagrant/Code        #需要映射到虚拟机中哪个的文件夹
   
   sites:  #设置域名和网站的映射关系
       - map: blogA.app                        #网站域名
         to: /home/vagrant/Code/blogA/public   #Laravel项目对应index.php的位置，以虚拟机的路径表示
   
       - map: blogB.app                        #网站域名
         to: /home/vagrant/Code/blogB/public   #Laravel项目对应index.php的位置，以虚拟机的路径表示
   ```

2. folders是要把本地机器某个文件夹映射到虚拟机上，也就是说登录虚拟机以后，查看/home/vagrant/Code文件夹的内容，是和Users/codingLady/Code的内容是一样的。这里需要注意的地方：

   * to字段里虚拟机的文件夹一定是已经在虚拟机上存在，如果不存在的话，要先建立一个，要不映射关系是建立不起来的。在虚拟机上建立文件夹的方法是：

     ```html
     #执行以下命令前，请先确认终端的当前目录是homestead的安装目录
     homestead up       #启动虚拟机
     homestead ssh      #登录虚拟机，这个时候虚拟机上的路径是/home/vagrant/
     #如果folders的to的路径是/home/vagrant/Code，那么就在/home/vagrant/建立Code文件夹
     mkdir  Code        #建立/home/vagrant/Code文件夹
     exit               #退出虚拟机的登录
     ```


###命令地址及说明

1. vagrant 命令地址 `https://www.vagrantup.com/docs/cli/box.html`

###安装redis扩展

###切换php版本

