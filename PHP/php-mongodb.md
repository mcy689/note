# mongodb 扩展

## MongoDB\Driver\Manager

1. 连接地址：

   ```php
   mongodb://[username:password@]host1[:port1][,host2[:port2],...[,hostN[:portN]]][/[database][?options]]
   ```

2. uri是一个URL，因此**其组件中**的任何特殊字符都需要根据»RFC 3986进行URL编码。这与用户名和密码特别相关，用户名和密码通常包括特殊字符，如@，：或％。通过Unix域套接字连接时，套接字路径可能包含特殊字符，如斜杠，必须进行编码。[rawurlencode()](<https://www.php.net/manual/zh/function.rawurlencode.php>) 函数可用于编码URI的组成部分。

   ```php
   $manager = new MongoDB\Driver\Manager("mongodb://" . rawurlencode("/tmp/mongodb-27017.sock"));
   
   $url = "mongodb://".$user.":".rawurlencode($pass)."@".$host.":".$port."/admin?replicaSet=".$config->replica;
   $manager = new MongoDB\Driver\Manager($url);
   ```

3. uriOptions 选项

   ```php
   replicaSet
       指定副本集的名称。
   retryWrites
   	如果为TRUE，则驱动程序将自动重试由于瞬时网络错误或副本集选举而失败的某些写入操作。默认为FALSE。
   ```

