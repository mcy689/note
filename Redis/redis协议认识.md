## redis协议认识

## 请求

1. 请求格式

   ```html
   *<number of arguments> CR LF
   $<number of bytes of argument 1> CR LF
   <argument data> CR LF
   ...
   $<number of bytes of argument N> CR LF
   <argument data> CR LF
   ```

2. 例子如下

   ```php
   //命令：set lock:order 12346
   
   $fp = fsockopen("127.0.0.1",6379,$errno,$errstr,30);
   if ($fp) {
       $out ="*3\r\n$3\r\nset\r\n$10\r\nlock:order\r\n$5\r\n12346\r\n";
       fwrite($fp, $out);
       $contents = fread($fp,8192);
       fclose($fp);
       var_dump($contents);die; //回复：+ok\r\n
   }
   ```

## 回复

* 用单行回复，回复的第一个字节

* 整型数字，回复的第一个字节将是“:”

  ```php
  //命令： incr mcyp
  $fp = fsockopen("127.0.0.1",6379,$errno,$errstr,30);
  if ($fp) {
      $out = "*2\r\n$4\r\nincr\r\n$4\r\nmcyp\r\n";
      fwrite($fp, $out);
      $contents = fread($fp,8192);
      fclose($fp);
      var_dump($contents);die; // :1\r\n
  }
  ```

* 错误消息，回复的第一个字节将是“-”

* 批量回复，回复的第一个字节将是“$”

  ```php
  //命令： get lock:order
  $fp = fsockopen("127.0.0.1",6379,$errno,$errstr,30);
  if ($fp) {
      $out = "*2\r\n$3\r\nget\r\n$10\r\nlock:order\r\n";
      fwrite($fp, $out);
      $contents = fread($fp,8192);
      fclose($fp);
      var_dump($contents);die; // $5\r\n12346\r\n
  }
  ```

* 多个批量回复，回复的第一个字节将是“*”

  ```php
  $fp = fsockopen("127.0.0.1",6379,$errno,$errstr,30);
  if ($fp) {
      //lrange m-9 0 -1
      $out = "*4\r\n$6\r\nlrange\r\n$3\r\nm-9\r\n$1\r\n0\r\n$2\r\n-1\r\n";
      fwrite($fp, $out);
      $contents = fread($fp,8192);
      fclose($fp);
      var_dump($contents);die; //*2\r\n$2\r\n70\r\n$2\r\n80\r\n 
  }
  ```

  