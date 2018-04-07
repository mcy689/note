#### php面向对象
 1. 可以使用 `use` 关键字之后使用as 子句, 可以为当前命名空间添加别名
 2. `__NAMESPACE__` 输出当前的命名空间.
    ```php
    //gloabl.php文件
    class Lister
    {
        public static function helloworld()
        {
            print 'hello from global \n';
        }
    }
    //一个文件
    <?php
    namespace com\getlinstance\util;
    require_once './global.php';
    class Lister
    {
        public static function helloworld()
        {
            print 'hello from '.__NAMESPACE__."\n";
        }
    }
    Lister::helloworld();
    \Lister::helloworld();
    
    ```
 3. 支持这样的用法 
    ```php
    //大括号语法提供的一项功能就是可以在一个文件中切换到全局空间,
    namespace com\getinstance\util{
    class Debug {
        static function hellowrold()
        {
            print "hello from Debug \n";
        }
    }
    }
    namespace main {
        echo __NAMESPACE__;
        \com\getinstance\util\Debug::hellowrold();
    }
    
4. 类函数和对象的函数
    ```php
    class_exists(); 
    get_declared_classses(); 用来获取脚本进程中定义的所有类的数组
    
5. 高级的对象函数的用法  `call_user_func()`和 `call_user_func_array()` 
    ```php
  
        $obj = new person();
        $method = 'demo';
        $obj -> $method();
    //call_user_func()这是这个函数的功能，可以这样理解
    
    // call_user_func() 如果调用静态的方法
   class person
    {
    	public static function demo()
    	{
    		echo 'demo class';
    	}
    }
    
    $returnVal = call_user_func(array('person','demo'));  //这样可以调用静态的方法 慎用
    
    // call_user_func() 如果调用普通的方法
    class person
    {
    	public function demo()
    	{
    		echo 'demo class';
    	}
    }
    $returnVal = call_user_func(array(new person(),'demo'));
    
    //call_user_func() 可以传递参数给目标函数或者方法；
    class person
    {
    	public function demo($id)
    	{
    		echo 'demo class'.$id;
    	}
    }
    $returnVal = call_user_func(array(new person(),'demo'),20);
    
    //call_user_func_array();  委托的这样的写法
    function __call($method,$args)
	{
		if(method_exists($this -> thirdpartyShop,$method)) {
			return call_user_func_array(array($this -> thirdpartyShop,$method),$args);
		}
	}
	//用法相同基本 调用静态的方法
	forward_static_call_array()
	forward_static_call()
	
	```
6. 使用反射
    ```php
    
    