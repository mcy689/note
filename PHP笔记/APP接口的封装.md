### APP接口的封装

1. APP接口的介绍

   * 接口的三要素    __接口的地址、接口文件(api.php处理一些业务逻辑)、接口数据__ 
   * 请求APP地址  -->  返回接口数据  -->  解析数据  --> 填充数据客户端

2. 接口的通信

   * xml 扩展标记语言, 可以用来标记数据、定义数据类型， 是一种允许用户对自己的标记语言进行定义的源语言。xml格式统一，跨平台，非常适合数据传输和通信。
   * json 一种轻量级的数据交换格式, 具有良好的可读和便于快速编写的特性, 可在不同平台之间进行数据交换, json采用兼容性很高的, 完全独立于语言文本格式

3. 通信数据格式xml和json的区别

   | 可读性方面  | xml胜出      |
   | :----: | ---------- |
   | 生成数据方面 | json比较方便   |
   | 传输速度方面 | json数据传递方便 |

4. APP接口做的事情

   * __获取数据__        从数据库中或缓存中获取数据, 然后通过接口数据返回给客户端
   * __提交数据__        通过接口提交数据给服务器, 然后服务器入库处理, 或者其他处理

5. 封装通信接口数据方法

   - 通信数据标准格式

     code                   状态码(200 400等)
     message           提示信息(邮箱格式不正确; 数据返回成功)
     data  		   返回数据

6. 封装通信接口数据方法

   * json数据的封装

     ```php
     //json_encode($value)该函数只能接受UTF_8编码的数据, 如果传递其他格式的数据该函数会返回null
     class Response
     {	
     	/**
     	 * 按照json方式输出通信数据
     	 * @param   integer $code  状态码
     	 * @param   string $message  提示信息
     	 * @param   array $data  数据
     	 * return string
     	 */
     	public static function json($code, $message='',$data = array())
     	{
     		if(!is_numeric($code)){
     			return '';
     		}

     		$result = array(
     			'code' => $code,
     			'message' => $message,
     			'data' => $data
     			);

     		echo json_encode($result);
     		exit;
     	}
     }
     ```

   * php生成xml数据

     > 1. 组成字符串
     >
     > 2. 使用系统类  DomDocument  XMLWriter     SimpleXML
     >
     >    ```php
     >     public static function xmlEncode($code, $message, $data=array())
     >     {
     >       if(!is_numeric($code)){
     >         return '';
     >       }
     >
     >       $result = array(
     >         'code' => $code,
     >         'message' => $message,
     >         'data' => $data
     >       );
     >
     >       header("Content-Type:text/xml");
     >       $xml = "<?xml version='1.0' encoding='UTF_8' ?>";
     >       $xml .= '<root>';
     >       $xml .= self::xmlToEncode($data);
     >       $xml .=  '</root>';
     >
     >       return $xml;
     >     }
     >
     >     public static function xmlToEncode($data)
     >     {
     >       $xml = $attr ='';
     >       foreach($data as $key => $value){
     >         if(is_numeric($key)) {
     >           $attr = "id='{$key}'";
     >           $key = 'item';
     >         }
     >         $xml .= "<{$key} {$attr}>"; 
     >         $xml .= is_array($value) ? self::xmlToEncode($value):$value;
     >         $xml .= "</{$key}>"; 
     >       }
     >       return $xml;
     >     }
     >    ```

7. 综合的接口

   ```php
   <?php
   class Response
   {
   	/**
   	 * 按照json方式输出通信数据
   	 * @param   integer $code  状态码
   	 * @param   string $message  提示信息
   	 * @param   array $data  数据
   	 * @param   string $type  数据类型
   	 * @return  string 
   	 */	
   	public static function show ($code, $message='',$data=array(),$type='json')
   	{
   		if(!is_numeric($code)){
   			return '';
   		}
   		//判端数据的格式
   		$type = isset($_GET['format']) ? $_GET['format'] : $type;
   		//拼接数据类型
   		$result = array(
   			'code' => $code,
   			'message' => $message,
   			'data' => $data
   			);
   		if(strtolower($type) == 'json'){
   			echo self::json($code,$message,$data);
   			exit;
   		} else if(strtolower($type) == 'xml') {
   			echo self::xml($code,$message,$data);
   			exit;			
   		}
   	} 
   	/**
   	 * 按照json方式输出通信数据
   	 * @param   integer $code  状态码
   	 * @param   string $message  提示信息
   	 * @param   array $data  数据
   	 * @return  string
   	 */	
   	public static function json($code, $message='',$data = array())
   	{
   		if(!is_numeric($code)){
   			return '';
   		}
   		$result = array(
   			'code' => $code,
   			'message' => $message,
   			'data' => $data
   			);
   		return json_encode($result);
   		exit;
   	}
   	/**
   	 * 按照json方式输出通信数据
   	 * @param   integer $code  状态码
   	 * @param   string $message  提示信息
   	 * @param   array $data  数据
   	 * @return  string
   	 */	
   	public static function xml($code, $message, $data=array())
   	{
   		if(!is_numeric($code)){
   			return '';
   		}
   		$result = array(
   			'code' => $code,
   			'message' => $message,
   			'data' => $data
   			);
   		header("Content-Type:text/xml");
   		$xml = "<?xml version='1.0' encoding='UTF_8' ?>";
   		$xml .= '<root>';
   		$xml .= self::xmlToEncode($data);
   		$xml .=  '</root>';
   		return $xml;
   	}
   	/**
   	 * 生成xml格式的数据
   	 * @param   array $data  数据
   	 * @return  string
   	 */	
   	public static function xmlToEncode($data)
   	{
   		$xml = $attr ='';
   		foreach($data as $key => $value){
   			if(is_numeric($key)) {
   				$attr = "id='{$key}'";
   				$key = 'item';
   			}
   			$xml .= "<{$key} {$attr}>"; 
   			$xml .= is_array($value) ? self::xmlToEncode($value):$value;
   			$xml .= "</{$key}>"; 
   		}
   		return $xml;
   	}	
   }
   $arr = array(
   	'data' => [
   		'id' => 1,
   		'name' => 'machunyu',	
   		'type' => [2,4,5]
   	]
   );
   Response::show (200, 'success',$arr);
   ```

8. 缓存技术

   * 静态缓存       保存在磁盘上的静态文件, 用php生成数据放入静态文件中

     ```php
     1. 生成缓存
     2. 获取缓存
     3. 删除缓存
     class File
     {
     	private $dir;
     	const TXT = 'txt'; 
     	public function __construct()
     	{
     		$this -> dir = dirname(__FILE__).'/files/';
     	}
     	public function cacheData($key,$value='',$cacheTime=0)
     	{
     		$filename = $this -> dir.$key.'.'.self::TXT;
     		//判断缓存的目录
     		if($value !== '') {
     			//删除文件
     			if(is_null($value)) {
     				return @unlink(str_replace('\\', '/', $filename));
     			}
     			//写入文件
     			$dir = dirname($filename);
     			if(!is_dir($dir)) {
     				mkdir($dir,0777);
     			}
     			$cacheTime = sprintf('%011d',$cacheTime);
     			return file_put_contents($filename, $cacheTime.json_encode($value));
     		}
     		//读取缓存
     		if(!is_file($filename)) {
     			return FALSE;
     		} else {
     			$contents = file_get_contents($filename);
     			$cacheTime = (int)substr($contents,0,11);
     			$value = substr($contents,11);
     			if($cacheTime != 0 && ($cacheTime + filemtime($filename) < time())) {
     				unlink($filename);
     				return FALSE;
     			}
     			return json_decode($value,true);
     		}
     	}
     }
     $res = new File();
     // if($res -> cacheData('mytest','machunyu',10)) {
     // 	echo 'success';
     // } else {
     // 	echo 'error';
     // }
     var_dump($res -> cacheData('mytest'));
     ```

9. 定时任务

   ```php
   crontab -e 	//编辑某个用户的cron服务
   crontab -l	//列出某个用户的cron服务的详细内容
   crontab -r	//删除某个用户的cron服务
   格式 : 分 小时  日  月  星期(0-6)
   	   *   *    *  *   *          命令
    *  代表取值范围内的数字
    /  代表每 比如每分 */1  表示每一分钟
   ```

10. 读取缓存方式开发接口的原理

  ```html
  http请求 -> 服务器  -> 存在缓存? -> 不存在  数据库中获取数据  -> 生成缓存
  							   ->  存在   返回数据
  ```

11. 版本升级接口开发以及APP演示

    版本升级分析以及数据表设计

    版本升级接口开发以及APP演示