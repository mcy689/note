#### 第一天的源码阅读
1. `__DIR__`和 `__FILE__`的区别

    `__DIR__`  ： 当前内容写在哪个文件就显示这个文件目录

    `__FILE__` ： 当前内容写在哪个文件就显示这个文件目录+文件名
    
2. realpath()函数的用法
   > realpath() 扩展所有的符号连接并且处理输入的 path 中的 '/./', '/../' 以及多余的 '/' 并返回规范化后的绝对路径名。返回的路径中没有符号连接，'/./' 或 '/../' 成分。
   ```php
    $res = '../application/config.php';
    var_dump(realpath($res));die;
    //打印的结果
    D:\XAMPP\htdocs\thinkphp_5.0.12_full\application\config.php
   ```
3. `__FILE__` 和 `$_SERVER['SCRIPT_FILENAME']` 的区别
   ```php
   //在function.php中写入
       require './abc/config.php';
   //然后建立对应文件夹abc,并在abc下建立config.php中写入
      echo $_SERVER['SCRIPT_FILENAME'];
      echo '<br>';
      echo __FILE__;
   //结果输出
     /usr/local/apache2/htdocs/studyTP/function.php
     /usr/local/apache2/htdocs/studyTP/abc/config.php
   ```
4. parse_ini_file() 函数的用法
   ```php
   //在.env中写入
    [names]
    me = Robert
    you = Peter
    
    [urls]
    first = "http://www.example.com"
    second = "http://www.w3school.com.cn"
   //在php文件中写入下面的内容就会将.env文件读成关联数组,
    var_dump(parse_ini_file(ROOT_PATH.'.env',true));
   ```
5. `DIRECTORY_SEPARATOR` 这个是目录分隔符，是定义php内置常量，`windows('\')`   和linux('/')上系统的目录分隔符是不相同的，所以引入了这个php内置的常量。

6. call_user_func_array() 的用法
   ```php
    //1. 直接调用方法
    function say($word)
    {
        echo $word;
    }
    call_user_func_array('sat',['hello word']);
   
    class Object
    {
       public function __construct(){}
       
       public static function say1($word)
       {
           echo $word.'say1';
       }
       
       public function say2($word)
       {
           echo $word.'say2';
       }
    }
    
    /*使用方式一 无需实例化调用类的静态方法*/
    call_user_func_array(['Object','say1'],['hello word']);
    
    /*使用方式二 实例化后调用非静态方法*/
    call_user_func_array([new Object,'say2'],['hello word']);
   ```
 7. var_export() 输出或返回一个变量的字符串表示, 您可以通过将函数的第二个参数设置为 TRUE，从而返回变量的表示。
    ```php
    //这样可以读入配置文件
    $settings = include(dirname(__FILE__).'/setting.php');
    $res = var_export($settings,true);
    $file_length = file_put_contents(dirname(__FILE__).'/setting.php','<?php return ' . var_export( $settings, true ) . '; ?>' );
    ```
8. 查看类的路径信息
    ```php
    //查找类  的路径
    $object = new ReflectionClass($QRcode);
    Reflection::export($object);
    //查找类中的方法  的路径
    $reflector = new ReflectionClass('MyDB');
	echo $reflector -> getMethod('getData');
    ```
9. 设置php错误
     ```php
     ini_set("display_errors",'1');
     error_reporting(E_ALL);
     ```
10. 记录
    ```mysql
    关于order by排序
    单条件排序：order by id（按照id排序默认从小到大）
                order by id desc（按照id排序从大到小）
    多条件排序：order by date,id（先按照date从小到大再按照id从小到大）
                order by date,id desc（先按照date从大到小再按照id从大到小）
                order by date desc,id（先按照date从大到小再按照id从小到大
    ```
11. 修改字符集的问题
    ```php
    //获取当前字符串的编码
     $encode = mb_detect_encoding($str, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5')); 
    //将字符编码改为utf-8
     $str_encode = mb_convert_encoding($str, 'UTF-8', $encode);
    ```
  12. 获取代码运行的时间
        ```php
        public function microtime_float()
        {
            list($usec, $sec) = explode(" ", microtime());
            return ((float) $usec + (float) $sec);
        }
        $time_start= $this -> microtime_float();
        //计算结束时间
        $time_end = $this -> microtime_float();
        $time = $time_end - $time_start;
        ```