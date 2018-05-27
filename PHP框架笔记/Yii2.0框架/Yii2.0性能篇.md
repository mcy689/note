### 性能篇

#### 类的延迟加载原里

```php
function  my_loader($class){
	require('./class/'.$class.'.php');
}

spl_autoload_register('my_loader',true);

try{
	$class = new Class3;
	$class->index();
} catch(Exception $e) {
	echo $e->getMessage();
}
```

#### 组件的延迟加载

```php
通过__get魔术方法延迟组件类的加载, 将组件的加载延迟到控制器中才加载组件
$res = \Yii::$app->session;
var_dump($res);
```

#### 数据缓存( 文件缓存 )

```php
$cache = \Yii::$app->getCache();
// 增加
 $res = $cache->add('key1','cache test',15);
// 修改
$res = $cache->set('key1','cache set test');
// 删除
$res = $cache->delete('key1');
// 获取
$cache->get('key1')
// 删除缓存
$cache->flush();
```

#### 片段缓存 

* 地址 `http://www.yiichina.com/doc/guide/2.0/caching-fragment`

* 片段缓存指的是缓存页面内容中的某个片段。例如，一个页面显示了逐年销售额的摘要表格， 可以把表格缓存下来，以消除每次请求都要重新生成表格的耗时。 片段缓存是基于数据缓存实现的。 

  ```php
  <?php if($this->beginCache('cache_div',['duration' => 10])): ?>
  	<div id='cache_div'>
  		<span>这里是被缓存的</span>
  	</div>
  <?php $this->endCache(); endif;?>
  ```

* 片段缓存的依赖

  ```php
  // 缓存依赖
  $dependency = [
  	'class' => 'yii\caching\FileDependency',
  	'fileName' => 'cachetest',
  ];
  //缓存开关
  $enabled = false;
  if($this->beginCache('cache_div',['dependency' => $dependency,'enabled'=> $enabled])): ?>
  	<div id='cache_div'>
  		<span>这里是被缓存的</span>
  	</div>
  <?php $this->endCache(); endif;?>
  ```

#### 页面缓存 

* 地址 `http://www.yiichina.com/doc/guide/2.0/caching-page`

```php
public function behaviors()
{
    return [
        [
            'class' => 'yii\filters\PageCache',
            'duration' => 60,
            'only' => ['cachetest'],
            'dependency' => [
                'class' => 'yii\caching\FileDependency',
                'fileName' => 'cachetest',
            ],
        ],
    ];
}
```

#### HTTP缓存

* 网址 `http://www.yiichina.com/doc/guide/2.0/caching-http`

* Web 应用还可以利用客户端缓存 去节省相同页面内容的生成和传输时间。 

* 两个http协议的属性  `Last-Modified ` (浏览器使用格林尼治时间)和 `ETag`

  ```php
  //lastModified设置http缓存 和 Etag设置http缓存
  public function behaviors()
  {
      return [
  	        [
  	            'class' => 'yii\filters\HttpCache',
  	            'only' => ['httptest'],
  	            'lastModified' => function () {
  	               return 1527418605;
  	            },
  	            'etagSeed' => function () {
  	            	return 'etagseed1';
  	            },
  	        ],
      	];
  }
  ```

  



