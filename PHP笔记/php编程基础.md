### php基础

1. 类的相关

   * 属性 : 被称为成员变量, 用来存放对象之间互不相同的数据
   * `$this` 伪变量把类指向一个对象实例,
   * 构造方法, 用来确保必要的属性被设置, 并完成任何需要准备的工作.

2. 参数和类型 ( 可以预测性是面向对象编程的重要部分 )

   ```php
   //一种转换的思路
   function outputAddresses($resolve){
   	if(is_string($resolve)) {
   		$resolve = (preg_match('/false|no|off/i',$resolve));
   	}
   	return $is_string;
   }
   ```

3. 要增加类中的 一个方法的类型约束

   ```php
   class demo
   {
   	public function write(redis $redis){}
     	public function say(array $arr){}
   }
   ```
   ​


------

### 其他

1. 调用类, 函数 或者方法的代码通常被称为该类, 函数或者方法的客户端

2. 在面向对象开发中, "专注特定任务", 忽略外界上下文是一个重要的设计原则

3. 类型处理的思考

   >PHP是一种弱类型的语言, 这使得这件事更加重要, 我们不能依靠编译器来防止类型相关的bug, 必须考虑到当非法数据类型的参数传递给方法时, 会产生咋样的后果, 我们不能完全信任客户端的程序员, 应该始终考虑如何在方法中处理他们引入的无用信息

   ​

