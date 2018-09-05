# 及硅步致千里

## 自动补全

### 自动补全最近联系人(100人)

1. 为服务器上的数百万用户建立一个属于自己的联系人列表来存储最近联系过的人。

2. 需要在能够快速向列表里面添加用户或者删除用户的前提下，尽量减少存储这些联系人列表带来的内存消耗。

3. Redis 的列表会以有序的方式来存储元素，并且和 Redis 提供的其他结构相比，列表占用的内存是最少的。

4. 代码实现

   ```php
   <?php
   
   class RedisBase
   {
   	private static  $instance = NULL;    //静态redis保存全局实例
   	/**
        * 单例redis 返还此类的唯一实例
       */
       public static function getInstance()
       {
           if (is_null(self::$instance)) {
           	$redis = new \Redis();
           	$redis->connect('localhost', 6379);
               self::$instance = $redis;
           }
           return self::$instance;
       }
   }
   
   class contacts
   {
   	private $redis = null;
   	private $acList = null;
   	public function __construct($user)
   	{
   		$this->redis = RedisBase::getInstance();
   		$this->acList = self::getKey($user);
   	}
   	/**
   	 * 获取自动填充的联系人列表
   	 * @param  [type] $prefix [description]
   	 * @return [type]         [description]
   	 */
   	public function list($prefix)
   	{
   		$list = $this->redis->lrange($this->acList,0,-1);
   		$matches = [];
   		if(empty($list)) {
   			return [];
   		}
   		foreach($list as $val){
   			if (strpos($val,$prefix) !== false) {
   				$matches[] = $val;
   			}
   		}
   		return $matches;
   	}
   	/**
   	 * 添加最近的联系方式并保证只有100人
   	 * @param [type] $contact [description]
   	 */
   	public function addContact($contact)
   	{
   		$this->redis->multi($this->redis::PIPELINE); //启用管道和事务
   		$this->redis->lrem($this->acList,$contact);
   		$this->redis->lpush($this->acList,$contact);
   		$this->redis->ltrim($this->acList,0,99);
   		$this->redis->exec();
   	}
   	/**
   	 * 删除指定的联系方式
   	 * @param  [type] $contact [description]
   	 * @return [type]          [description]
   	 */
   	public function delContact($contact)
   	{
   		$this->redis->lrem($this->acList,$contact);
   	}
   	public function getKey($user)
   	{
   		return 'recent:'.$user;
   	}
   }
   
   $res = new contacts(1);
   // $res->addContact('apple');
   // $res->addContact('tangerine');
   // $res->addContact('banana');
   // $res->delContact('apple');
   $list = $res->list('ap');
   var_dump($list);die;
   ```

### 通讯录自动补全

