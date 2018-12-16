# 升级Mac系统到Mojave出现不能使用gcc的报错

1. 错误信息

   ```shell
   xcrun: error: invalid active developer path (/Library/Developer/CommandLineTools), missing xcrun at: /Library/Developer/CommandLineTools/usr/bin/xcrun
   ```

2. 解决办法

   ```c
   sudo xcode-select --install
   ```
