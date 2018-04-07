#### PHP面向对象(高级特性四章)

1. 静态方法和属性

   * 静态方法是以类作为作用域的函数，静态方法不能访问这个类中的普通属性， 因为那些属性属于一个对象， 但可以访问静态属性

   * 静态方法和属性被称为类变量和属性，因此不能在静态中使用伪变量`$this`

   * 功能

     > 1. 不需要在对象间传递类的实例， 也不需要将实例存放在全局变量中，就可以访问类中的方法
     > 2. 类的每个实例都可以访问类中的定义的静态属性， 可以利用静态属性来设置值，该值也可以被__类的所有对象__ 使用
     > 3. 不需要实例化就可以访问静态属性和静态方法

2. 静态常量

   * 只能通过类的实例访问常量属性，引用常量不需要用美元符号

   * 当需要在类的所有实例中都能访问某个属性，并且属性值无需改变时，应该使用常量

     ```php
     class ShopProduct
     {
     	const AVAILABLE = 222;
     }
     echo ShopProduct::AVAILABLE;
     ```

     ​

3. 抽象类中只定义（或部分实现）子类需要的方法

   ```php
   abstract class ShopProductWrite
   {
   	protected $products = array();
   	public function addProduct(ShopProduct $shopProduct)
   	{
   		$this -> products[] = $shopProduct;
   	}
   	abstract public function write();
   }
   ```

   ​

   * 实现方法的访问控制不能比抽象方法的访问控制更严格。
   * 新实现方法的参数个数应该和抽象方法的个数一样。

4. 延迟静态绑定 （5.3 >）

   * 该特性的最明显的关键字`static` ,它指的是被调用的类而不是包含类

     ```php
     abstract class DomainObject
     {
     	public static function create()
     	{
     		return new static();
     	}
     }

     class User extends DomainObject
     {
     }
     class Document extends DomainObject
     {
     }
     Document::create();
     User::create();
     ```

      

 5.  `static` 也可以作为静态方法调用的标识符，甚至是从非静态上下文中调用。

     ```php
     abstract class DomainObject
     {
     	private $group;
     	public function __construct()
     	{
     		$this -> group = static::getGroup();
     	}
     	public static function create()
     	{
     		return new static();
     	}
     	public static function getGroup()
     	{
     		return 'default';
     	}
     }
     class User extends DomainObject {
     }
     class Document extends DomainObject
     {
     	public static function getGroup()
     	{
     		return 'document';
     	}
     }
     class SpreadSheet extends Document{
     }
     var_dump(User::create());
     var_dump(SpreadSheet::create());

     object(User)#1 (1) { 
       	["group":"DomainObject":private]=> string(7) "default" } 
     object(SpreadSheet)#1 (1) {
         ["group":"DomainObject":private]=> string(8) "document" }
     ```
     ​
6. __委托__ 是指一个对象转发或者委托一个请求给另一个对象，被委托的一方替原先对象处理请求。委托比继承具有更大的灵活性。
    ```php
    class PersonWrite
    {
    	function writeName(Person $p)
    	{
    		print $p -> getName()."\n";
    	}
    	function writeAge(Person $p)
    	{
    		print $p -> getAge()."\n";
    	}
    }
    class Person
    {
    	private $writer;
    
    	function __construct(PersonWrite $writer)
    	{
    		$this -> writer = $writer;
    	}
    	function __call($methodname, $args)
    	{
    	    //call 这个魔术方法这样的用法注意
    		if(method_exists($this -> writer, $methodname)){
    		    //这里的传入的$this;这个对象的代指
    			return $this -> writer -> $methodname($this);
    		}
    	}
    	function getName(){ return 'bob';}
    	function getAge(){return 44;}
    }
    //这里传入对象的用法注意，可取
    $person = new Person(new PersonWrite);
    $person -> writeName();
    
    ```
7. 析构函数 （在对象从内存中删除之前自动调用）
8. 对象的复制，`__clone()` 是在复制得到的对象上运行， 而不是在原始对象上运行的
    ```php
    //这样的浅复制，可以保证所有基本数据类型的属性被完全复制。
    class Person
    {
       private $name;
       private $age;
       private $id;
       public function __construct($name, $age)
       {
    	  $this -> name = $name;
    	  $this -> age = $age;
       }
    
       public function setId($id)
       {
    	  $this -> id = $id;
       }
    
       public function __clone()
       {
    	  $this -> id = 0;
       }
    }
    $person = new Person('bob',44);
    $person -> setId(343);
    $person2 = clone $person;
    
    //但像上面的复制，在复制对象属性时只复制引用，并不复制引用的对象
    class Account
    {
    	public $balance;
    	public function __construct($balance)
    	{
    		$this -> balance = $balance;
    	}
    }
    class Person
    {
    	private $name;
    	private $age;
    	private $id;
    	public $account;
    	public function __construct($name, $age, Account $account)
    	{
    		$this -> name = $name;
    		$this -> age = $age;
    		$this -> account = $account;
    	}
    	public function setId($id)
    	{
    		$this -> id = $id;
    	}
    	public function __clone()
    	{
    		$this -> id = 0;
    	}
    }
    
    $person = new person('bob',44, new Account(200));
    $person -> setId(343);
    $person2 = clone $person;
    $person -> account -> balance += 10;  //这里给$person 充一些钱
    print $person2 -> account -> balance; //这里$person2也得到了这笔钱，这不合理
    
    //如果不希望对象属性在被复制之后被共享，那么可以显示地在 __clone() 方法中复制指向的对象
     public function __clone()
     {
        $this -> id = 0;
        $this -> account = clone $this -> account;
     }
    ```
9. `__toString()` 这个魔术方法的用法
    * 对于日志和错误报告， `__toString()` 方法非常有用，
    * 也可用与设计专门用来传递信息的类，比如Exception 类可以把关于异常的总结信息写到`__toString()` 方法中。
10. 回调
    ```php
    //这里的代码有问题, 以后再看
        class Product
    {
    	public $name;
    	public $price;
    
    	function __construct($name, $price)
    	{
    		$this -> name = $name;
    		$this -> price = $price;
    	}
    }
    class ProcessSale
    {
    	private $callbacks;
    
    	function registerCallback($callback)
    	{
    		if(!is_callable($callback)) {
    			throw new Exception('callback not callable');
    		}
    		$this -> callbacks[] = $callback;
    	}
    
    	function sale($product)
    	{
    		print $product -> name.":processing \n";
    		foreach ($this -> callback as $callback) {
    			call_user_func($callback,$product);
    		}
    	}
    }
    
    $logger = create_function('$product', 'print " logging({$product -> name}) \n";');
    
    
    $processor = new ProcessSale();
    $processor -> registerCallback($logger);
    
    $processor -> sale(new Product('shoes',6));
    $processor -> sale(new Product('coffee',6));
    
    ```
 