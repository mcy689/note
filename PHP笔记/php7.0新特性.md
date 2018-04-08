#### PHP新特性

1. 形参类型声明

   * php7.0支持的形参声明的类型  有整形、浮点型、字符串型、布尔型。示例

     ```php
     class Person
     {
     	public function age(int $age)
     	{
     		return $age;
     	}
     	public function name(string $name)
     	{
     		return $name;
     	}
     	public function isAlive(bool $alive)
     	{
     		return $alive;
     	}
     }
     	$person = new Person();
     	echo $person -> age(18);
     	echo $person -> name('machunyu');
     	echo $person -> isAlive(true);
     ```

     ​

   * 默认情况下，形参类型声明不是被完全限制，毕竟`php`是一个弱类型的语言

     ```php
     echo $person -> isAlive('true'); //是不会报错的
     ```

   * 可以加上限制，那就是在`php`文件开头的地方写上如下代码

     ```php
     declare(strict_types = 1);
     //再执行上面那个代码就会报错
       Fatal error: Uncaught TypeError: Argument 1 passed to Person::isAlive() must be of the type boolean, string given,
     ```

2. 返回类型声明

   * 支持返回类型的声明，操作就是有点骚

     ```php
     class Person
     {
     	public function age(int $age) : string
     	{
     		return "my age is $age";
     	}
     	public function name(string $name) : string
     	{
     		return $name;
     	}
     	public function isAlive(bool $alive) : string
     	{
     		return $alive ? 'yes' : 'No';
     	}
     }
     $person = new Person();
     echo $person -> age(20);
     echo $person -> name('machunyu');
     echo $person -> isAlive(true);
     ```

   * 还有个更骚的操作

     ```php
     class Address
     {
     	public function getAddress() : Address
     	{
     		return ['street' => 'Street 1','country' => 'pak'];
     	}
     }
     class Person
     {
     	public function age(float $age) : string
     	{
     		return 'Age is '.$age;
     	}
     	public function getAddress() : Address
     	{
     		return new Address();
     	}
     }
     $person = new Person();
     $Address = new Address();
     var_dump($person -> getAddress());
     var_dump($Address -> getAddress());//Fatal error: Uncaught TypeError: Return value of Address::getAddress() must be an instance of Address, array returned
     ```

   总结: 这样的类型声明有一个好处就是， 可以让函数方法的形参与返回值有所预期，避免出现不必要的数据传递，从而造成错误。 

   ​

3. 匿名类

   * 匿名类的声明与使用是同时进行的, 它具备其他类所具备的所有功能, 差别在于匿名类没有类名. 匿名类的一次性小任务代码流程对性能提升帮助很大

     ```php
     $name = new class()
     {
     	public function __construct()
     	{
     		echo 'test';
     	}
     };
     ```

   * 匿名类同样也可以继承的

     ```php
     class packt
     {
     	protected $number;

     	public function __construct()
     	{
     		echo 'I am parent construct';
     	}

     	public function getNumber() : float
     	{
     		return $this -> number;
     	}
     }

     $num = new class(7) extends packt{
     	public function __construct(float $number)
     	{
     		parent::__construct();
     		$this -> number = $number;
     	}
     };
     ```

   * 匿名类可以嵌套在一个类中使用

     ```php
     class Math
     {
     	public $first = 10;
     	public $second = 20;

     	public function add() : float
     	{
     		return $this -> first + $this -> second;
     	}

     	public function multiply()
     	{
     		return new class() extends Math {
     			public function multiply2(float $third)
     			{
     				return $this -> add() * $third;
     			}
     		};
     	}
     }
     $math = new Math();
     $res = $math -> multiply() -> multiply2(5);
     var_dump($res);die;   //150
     ```

     ​