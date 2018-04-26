### Composer

1. 运行Composer 需要 PHP 5.3.2 以上版本

2. 安装

   * 局部安装

     ```html
     curl -sS https://getcomposer.org/installer | php
     ```

   * 全局安装

     ```html
     curl -sS https://getcomposer.org/installer | php
     mv composer.phar /usr/local/bin/composer
     ```

3. 注意事项

   * 如果你正在使用Git来管理你的项目， 你可能要添加 `vendor` 到你的 `.gitignore` 文件中。 你不会希望将所有的代码都添加到你的版本库中。

   *  `install` 命令将创建一个 `composer.lock` 文件到你项目的根目录中。

     * 在安装依赖后，Composer 将把安装时确切的版本号列表写入 `composer.lock` 文件。这将锁定改项目的特定版本。

     * 如果不存在 `composer.lock` 文件，Composer 将读取 `composer.json` 并创建锁文件。

     * 这意味着如果你的依赖更新了新的版本，你将不会获得任何更新。此时要更新你的依赖版本请使用 `update` 命令。这将获取最新匹配的版本（根据你的 `composer.json` 文件）并将新版本更新进锁文件。

       ```html
       composer update  全部更新
       composer update monolog/monolog [..] 可以更新指定的插件
       ```

4. 平台软件包

   * Composer 将那些已经安装在系统上，但并不是由 Composer 安装的包视为一个虚拟的平台软件包。这包括PHP本身，PHP扩展和一些系统库。
   * ​

   ​