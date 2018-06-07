#### homestead 环境搭建

1. 下载

   ```html
   https://www.vagrantup.com/downloads.html   vagrant下载
   https://www.virtualbox.org/wiki/Downloads  virtualbox下载
   
   https://ninghao.net/blog/1566   Vagrant 本地环境搭建日志
   ```

2. 安装遇到问题

   ```html
   运行  regedit 注册表
   
   错误代码:
   
   Failed to instantiate CLSID_VirtualBox w/ IVirtualBox, but CLSID_VirtualBox w/ IUnknown works.
   PSDispatch looks fine. Weird.
   
   返回代码: E_NOINTERFACE (0x80004002) 
   组件: VirtualBoxClientWrap 
   界面: IVirtualBoxClient {d2937a8e-cb8d-4382-90ba-b7da78a74573} 
   
   解决办法：在注册表里面进行修改
   
   HKEY_CLASSES_ROOT\CLSID\{00020420-0000-0000-C000-000000000046}
   InprocServer32 修改为C:\Windows\system32\oleaut32.dll
   HKEY_CLASSES_ROOT\CLSID\{00020424-0000-0000-C000-000000000046}
   InprocServer32 修改为C:\Windows\system32\oleaut32.dll
   ```

3. 盒子网站

   ```html
   https://app.vagrantup.com/boxes/search?provider=virtualbox
   ```

   