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

## 分布式锁

### 乐观锁

1. Redis 使用 watch 命令来代替对数据进行加锁，因为 watch 只会在数据被其他客户端抢先修改了的情况下通知执行了这个命令的客户端，而不会阻止其他客户端对数据进行修改，所以这个命令被称为 __乐观锁。__
2. 使用 watch 命令不够完美，在程序尝试完成一个事务的时候，可能会因为事务执行失败而反复地进行重试。

### 加锁注意事项

1. 持有锁的进程因为操作时间过长而导致锁被自动释放，但进程本身并不知晓这一点，甚至还可能会错误地址释放了其他进程持有的锁。
2. 一个持有锁并打算执行长时间操作的进程已经崩溃，但其他想要获取锁的进程不知道哪个进程持有着锁，也无法检测出持有锁的进程已经崩溃，只能白白得浪费时间等待锁被释放。
3. 在一个进程持有的锁过期了之后，其他多个进程同时尝试去获取锁，并且都获得了锁。
4. __dogpile效应__ 指的是，执行事务所需的时间，并使得那些带有时间限制的事务失败的几率大幅上升，最终导致所有事务执行失败的几率和进行重试的几率都大幅地上升。

### 加锁的应用场景

解决资源竞争、缓存风暴等问题。例如，在__缓存风暴中__ ，没有锁保护的情况下，缓存失效，会导致短时间内，多个请求透过缓存到达数据库，请求同一份数据，修改同一份缓存；如果使用了锁，可以让获得锁的请求到达数据库，请求数据后回写缓存，后续没有得到锁的就直接读取新的缓存数据，而不用请求数据库了。 

### Redis加锁

1. 给锁加上超时限制特性，这样确保了在客户端已经崩溃了的情况下仍然能够自动被释放，客户端会在尝试获取锁失败后，检查锁的超时时间，并为未设置超时时间的锁设置超时时间。

2. 代码实现

   ```php
   <?php
   class RedisBase
   {
   	private static  $instance = NULL;
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
   
   class lockeRedis
   {
   	private $redis;
   	public function __construct()
   	{
   		$this->redis = RedisBase::getInstance();
   	}
   	/**
   	 * 通过redis中的setnx设置锁
   	 * @param  [type]  $lockname        锁的key
   	 * @param  integer $acquire_timeout 获取的锁的时间 单位秒
   	 * @param  integer $lock_timeout    定义锁超时间 单位秒
   	 * @return [type]                   [description]
   	 */
   	public function setnxR($lockname,$acquire_timeout=10,$lock_timeout = 10)
   	{
   		if (empty($acquire_timeout) || empty($lockname)) {
   			return '请设置锁的key或者获取锁的时间';
   		}
   		$lockname = 'lock:'.$lockname;
   		$end = time() + $acquire_timeout;
   		$lock_timeout = (int)$lock_timeout;
   		//确定要设置的有效期是将来的
   		if (time() < $end) {
   			//通过 set 命令 实现 setnx 的操作
   			if ($this->redis->set($lockname,$end,['nx', 'ex'=>$lock_timeout])) {
   				return true;
   			} else {
   				//如果获取失败，判断是否锁过期
   				if(time() > $this->redis->get($lockname) &&  time() > $this->getSet($lockname,$lock_timeout)) {
   					return true;
   				}
   				//表示设置了key 但是 没有添加有效时间 重新设置超时间，防止死锁
   				if ($this->redis->ttl($lockname) == -1) {
   					$redis->expire($lockname,$lock_timeout);
   				}
   				return false;
   			}
   		}
   		return false;
   	}
   }
   
   $lockObj = new lockeRedis();
   var_dump($lockObj->setnxR('mcy689',10,10));
   ```

## 计数信号量



