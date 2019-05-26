#### 安装git

1. git是一个版本控制软件，与svn类似，特点是分布式管理，不需要中间总的服务器，可以增加很多分支。

2. windows下的git叫msysgit，下载地址`https://git-for-windows.github.io/`或者`https://git-scm.com/download/win`


__第一步安装 __ 

* git Bash Here   可以从当前文件夹打开git命令行界面

* git GUI Here     可以打开图形界面

  ![20160914155105513](./img/20160914155105513.png)

 __第二部安装 __ 

* use git from git bash only只能从git bash里面使用git命令，即不能在cmd命令行中使用，应为这个选项不会把git命令加入到环境变量中。

* user git from the widowscommand prompt在cmd命令行中使用git命令，可以在git bash和cmd中同时使用git命令，会自动在增加环境变量

* user git and optionalunix tools from the windows command prompt在第二个选项的基础上增加了unix系统中的一些工具。

  ![20160914155208483](./img/20160914155208483.png)

* 因为GIT是用C语言写的，所以服务器里面都是按照UNIX系统格式保存的。所以客户端再提交和下载的时候，需要对文件进行格式的转换。

* checkoutwindows-style,commit unix-style line endings按照windows系统格式来下载，按照unix系统格式去上传，这种配置应用在跨平台系统整合代码时，windows系统需要的配置。完后默认配置文件core.autocrlf中会进行修改为true。

* checkout as-is,commitunix-style line endings按照它原本的格式直接下载，按照unix系统格式去上传，这种配置应用在跨平台系统整合代码时，unix系统需要的配置。完后默认配置文件core.autocrlf中会进行修改为input。

*  checkout as-is ,commit a-is 按照它原本的格式直接下载，按照原本的格式直接上传。这种配置不能应用在跨平台系统上面。完后默认配置文件core.autocrlf中会进行修改为false。

__第三部安装设置GIT Bash终端仿真器的样式。__ 

- use mintty是一种仿真样式，比cmd窗口好在可以调节大小，字体样式啥的。

- use windows defaultconsole window使用windows系统自带的cmd窗口打开git bash。

  ![20160914155340874](./img/20160914155340874.png)

__第四部安装__ 

- enable file system caching允许文件缓存。即在提交文件的时候，可以先将文件放到缓存区，然后再统一提交。

- enable git credentialmanager允许git许可证管理（会检测并下载.netframework v4.5）

  ![20160914155626578](./img/20160914155626578.png)