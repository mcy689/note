### Apache配置文件修改

1. 配置自动解析index文件

   ```html
   243 #
   244 # DirectoryIndex: sets the file that Apache will serve if a directory
   245 # is requested.
   246 #
   247 <IfModule dir_module>
   248     DirectoryIndex index.html index.php  <!--修改这里配置默认解析的文件-->
   249 </IfModule>
   ```

   ​