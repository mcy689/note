#### ThinkPHP路由

1. 路由参数

   * 路由参数是指可以设置一些路由匹配的条件参数，主要用于验证当前的路由规则是否有效.

     ```php
     // 定义GET请求路由规则 并设置URL后缀为html的时候有效
     Route::get('new/:id','News/read',['ext'=>'html']);
     //支持匹配多个后缀
     Route::get('new/:id','News/read',['ext'=>'shtml|html']);
     // 定义GET请求路由规则 并设置禁止URL后缀为png、jpg和gif的访问
     Route::get('new/:id','News/read',['deny_ext'=>'jpg|png|gif']);

     //V5.0.7版本以上，ext和deny_ext参数允许设置为空，分别表示不允许任何后缀以及必须使用后缀访问
     ```

2. 目录的介绍

   1. application目录

      ```php
      index 目录 必须有 controller model view 三个目录
      admin 目录 必须有 controller model view 三个目录
      command 这个是命令行文件
      common 这个文件是全局的函数或者类自定义的文件
      config  也是全局的配置文件
      tags是钩子文件
      ```

   2. extend目录       第三方的插件或者类文件的存放地

   3. runtime目录     这个目录是框剪启动以后的缓存文件,    日志,缓存文件

   4. public目录

      ```php
      static 这个目录是静态文件的存放目录
      robots.txt 这个文件是对搜索引擎来说的
      router.php  这个文件可以通过 php -S server_name router.php 这种命令行的方式启动php自带的web服务
      ```

   5. thinkphp  这是框架的核心

   6. vendor目录  通过composer安装的文件

3. 开发规范

   1. 目录使用小写 + 下划线的方式命名
   2. 类库函数文件名均以 .php 结尾
   3. 类的文件名均以命名空间定义, 且命名空间和类库文件所在的路径一致. 
   4. 类文件采用驼峰, 首字母大写, 其他文件是小写 +下划线
   5. 表和字段采用小写＋下划线的命名方式不能以下划线开头．
   6. 应用类库的命名空间统一为app(可以配置);

4. 模块设计

   5.0版本对模块的功能做了灵活的设计, 默认采用多模块的架构, 并且支持单一模块设计, 所有模块的命名空间均以app作为根命名空间. (可以修改的通过配置文件)

   __注意__ 

   ```php
   common模块在thinkphp5.0中不能被直接访问的,但是可以通过以下方式间接的使用
   //定义
   namespace app\common\controller;
   class Index
   {
     public function index()
     {
       return 'this is common Index index';
     }
   }
   //使用
   namespace app\index\controller;
   use app\common\controller\Index as commonIndex;
   class Index
   {
     public function index()
     {
       return "this is index Index index";
     }
     public function common()
     {
       $common = new commonIndex();
       return $common -> index();
     }
   } 
   //也可以使用
   namespace app\index\controller;
   use app\common\controller\User as commonUser;
   class User extends commonUser
   {
     public function demo()
     {
       return $this -> showName('machunyu');
     }
   }
   ```

   修改application模块的名字为app

   ```php
   // 修改public目录下index.php文件
   define('APP_PATH', __DIR__ . '/../app/'); 
   ```

5. thinkphp5.0中的助手函数

   ```php
   dump();
   input();
   config(); 这是个全局的配置助手函数
   url(); 这个助手函数  默认根据路由规则形成url
   session() 助手函数
   cookie() 助手函数
   	cookie('email','2448154972@qq.com','/');
       $req = $request -> cookie('email');
   ```

6.  ThinkPHP5.0的配置原理

   ```php
   class Index
   {
     public function index()
     {
       $conf1 = ['username' => 'mcy'];
       $conf2 = ['username' => 'machunyu'];
       dump(array_merge($conf1,$conf2)); //如果存在相同的key,后面的会将前面的覆盖
     }
   }
   ```

7. 配置文件的获取, 设置, 检测

   ```php
   配置   $res = \think\Config::get();   或者  use think\config;
   基本的方法
       Config::get(); 获取配置   Config::set(); 设置配置
       也可以直接使用助手函数 config();
   模块级的配置
       Config::set('username','mcy','index'); 等效于 config('username','mcy','index');
       dump(Config::get('username','index'));
   检测
       $res = config::has('username');
       $res = config::has('username','index');
   ```

8. `.env` 文件   环境变量配置和使用就是.env文件

   ```php
   1. 新建.env文件
   2. 在需要使用的文件中使用 use \think\ENV;  $res = ENV::get('email');  
   3. 特殊的用法
       //如果设置了email_name 这个参数, 就使用如果没有这个值就为default
       ENV::get('email_name','default');
   4. 设置的内容
        [database]                配置组   它下面的配置key就会变成 database_hostname;
        hostname = localhost
        username = root
        password = root
     这样设置采用 Config::get('database_username'); 获取
       		  Config::get('database.username'); 等效的
   ```

9. 入口文件

   * 单入口文件, 应用程序的所有http请求都有某一个文件接受并由这个文件转发到功能代码中.
   * thinkphp文件下的base.php文件中有定义了环境的常量

10. 隐藏入口文件

  ```php
  1. 第一步 开启httpd.conf文件  LoadModule rewrite_module modules/mod_rewrite.so
  2. 把httpd.conf文件这个  AllowOverride none      修改成 AllowOverride All
  3. 把下面的内容保存为.htaccess文件放到应用入口文件的同级目录下
    <IfModule mod_rewrite.c>
      RewriteEngine on
      RewriteCond %{REQUEST_FILENAME} !-d
      RewriteCond %{REQUEST_FILENAME} !-f
      RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]
    </IfModule>
  ```

11. 入口文件绑定

    * 基本绑定  直接在 `public/index.php` 中绑定

      ```php
      //只绑定模块
      define('BIND_MODULE','admin');  //请求直接输入域名+控制器名+方法名
      //绑定入口文件直接到模块下的控制器
      define('BIND_MODULE','admin/index');  //请求直接输入域名+方法名如 : http://thinktest.com/demo
      ```

    * 专门为api的请求, 可以用上述的方式为api做一个接口

      ```php
      1. 新建一个api.php在public目录下
      2. 写入与index.php相同的代码
      	 define('APP_PATH', __DIR__ . '/../application/');
      	 define('CONF_PATH',__DIR__. '/../conf/');
      	 define('BIND_MODULE','api');
      	 // 加载框架引导文件
      	 require __DIR__ . '/../thinkphp/start.php';
      3. 接口的请求  http://thinktest.com/api.php
      ```

12. 请求对象 __这个类在 `thinkphp/library/think` 目录下的(request.php)__ 

    * 获取请求对象

      ```php
      1. 可以直接使用  request();助手函数  这个函数返回的是一个请求对象
      2. 先 use think\Request;  在调用  $request = Request::instance(); //这个是单例模式
      3. 注入对象的方式   先 use think\Request;  在这样操作 public function index(Request $request)
      ```

    * 请求对象中的方法

      ```php
      //请求 http://thinktest.com/new/1.html 
        //获取域名
         $req = $request -> domain();      //http://thinktest.com
        //获取请求行的第二个参数带后缀的  
         $req = $request -> pathinfo();    //new/1.html 
        //获取请求行的第二个参数不带后缀的
         $req = $request -> path();        //new/1
        //获取请求的方式
         $req = $request -> method();      //GET
        //快捷判断请求方式的方式
         $req = $request -> isPost();     //false
         $req = $request -> isAjax();
         $req = $request -> isGet();
        //请求的参数获取
      	$req = $request -> get();
          $req = $request -> post();
      	$req = $request -> session();
          $req = $request -> cookie();
          $req = $request -> module();
          $req = $request -> controller();
          $request -> param();  //可以获得post和get方法提交的数据
      ```

13. 响应对象 Response

    * 返回数据的格式动态修改

      ```php
      $res = [
        'code' => 200,
        'result' => [
          'list' => [1,2,3,4,5,6]
        ]
      ];

      config::set('default_return_type','json');    //等效于 return json_encode($res);
      return $res;
      ```

    * 接口的返回    __在application下新建api/controller/index.php__   

      ```php
      1. 
      namespace app\api\controller;
      use think\Config;
      use think\Request;
      class Index
      {
       	public function getUserInfo(Request $request)
          {
            $data = [
              	'code' => 200,
              	'msg' => [
                    'username' => 'mcy',
                    'useremail' => '2448154972@qq.com'
              	]
            ];
           	Config::set('default_return_type','json'); 
           	return $data;
          } 
      }
      2. //定义返回的接口的类型可以直接在conf/api/建立config.php的这中方式一次性为这个模块来设置
      3. 一般情况下是这样配置的来实现同一个方法返回不同的数据格式
        public function getUserInfo($type = 'json')
        {
          if(!in_array($type,['json','xml'])){  
            $type = 'json';
          }
          $data = [
            'code' => 200,
            'msg' => [
              'username' => 'mcy',
              'useremail' => '2448154972@qq.com'
            ]
          ];
          Config::set('default_return_type',$type);
          return $data;
        } 
        //这样请求  http://thinktest.com/api.php?type=xml
      ```

14. 视图

    * 直接使用view() 函数的方式

      * 没有修改任何的配置`view()` 函数的默认请求路径是   `当前模板下的view目录+控制器名+当前方法名.html` 

        ```php
        1. 传递第一个参数, 修改模板文件目录的  return view('upload');
           请求的路径就是  app/index/view/index/upload.html

        2. 当传递的参数这样写 return view('upload/demo');
        	请求的路径就是 app/index/view/upload/demo.html
              
        3. 当传递的参数这样写  return view('./index.html');
        	请求的路径就是  当前的入口的文件的同级目录
        ```

      * 如果输入第二个参数

        ```php
        return view('index',[
                'email' => '2448154972@qq.com'
              ]);
        是相当于给页面传递分配变量  {$email}
        ```

      * 如果输入第三个参数

        ```php
        return view('index',[
                'email' => '2448154972@qq.com'
              ],[
                'MCY' => '这是哈哈'
              ]);
        在页面中直接使用大写的MCY就可以了
        ```

    * 使用继承的方式

      ```php
      namespace app\index\controller;
      use think\Controller;           					//引入这个类
      class Index extends controller   					//继承这个类
      {
        public function index()
        { 
          return $this -> fetch('index',[          		//这样使用
              'email' => '2448154972@qq.com'
            ],[
              'MCY' => '这是哈哈'
            ]);  	
        }
      }

      也可以这样往页面传递变量
      namespace app\index\controller;
      use think\Controller;           			 		//引入这个类
      class Index extends controller   			 		//继承这个类
      {
        public function index()
        {
          $this -> assign('assign','assign传递的值');
          return $this -> fetch('upload/demo');  			//这样使用
        }
      }
      ```

    * 其他的特殊用法`display` 中使用模板的编写规则,

      ```php
       return $this -> display('这是一个{$email}字符串',[        //注意此处要使用单引号
              'email' => '2448154972@qq.com'
             ]);

      //页面显示的结果     这是一个2448154972@qq.com字符串
      ```

15. 在页面中分配变量

    ```php
    namespace app\index\controller;
    use think\Controller;
    use think\view;
    class Index extends controller
    {
      public function index()
      {
        view::share('key','value');
        return $this -> fetch('index');
      }
     }

    默认的页面中的
    __URL__         //显示的是请求的
    __STATIC__      //显示的是public目录下的static目录
    __JS__          //显示的是public目录下的static/js目录
    __CSS__		    //显示的是public目录下的static/css目录  __CSS__/style.css
    __R00T__		//显示
    如果在页面中使用了__CSS__这样的形式 就可以在突然修改目录的时候直接修改配置文件中
       // 视图输出字符串内容替换
        'view_replace_str'       => [
          '__CSS__' => 'public/test'  //就可以修改目录
        ],

    也可以直接使用这样的方式配置
    namespace app\index\controller;
    use think\Controller;           					//引入这个类
    class Index extends controller   					//继承这个类
    {
      public function index()
      { 
        return $this -> fetch('index',[          		//这样使用
            'email' => '2448154972@qq.com'
          ],[
            '__CSS__' => 'public/test'
          ]);  	
      }
    }
    ```

16. 页面中的比较标签

    ```php
    //相等判断    
    	{eq name='a' value='10'}   //name就是要比较的变量名  value就是要比较的变量的值
            相等
        {else/}
            不相等
        {/eq}
        //用法是相同的
        {equal name='a' value='10'}
            相等
        {else/}
            不相等
        {/equal}

    //不相等的判断
        {neq name='a' value="10"}
            不相等
        {else/}
            相等
        {/neq}
        //等效的
        {notequal name='a' value="10"}
            不相等
        {else/}
            相等
        {/notequal}
    //判断变量是否大于 value值
    	{gt name='a' value='8'}
    		正确
    	{else}
    		value值小
    	{/gt}
    //判断变量是否小于 value值
    	{lt name='a' value='20'}
    		变量那个a的值比value值小
    	{else}
    		相反
    	{/lt}
    ```

17. 页面中的循环遍历

    ```php
    //for循环    开始的计数 结束的计数 步进   可以使用的变量名
            {for start="1" end="10" step="2" name="k"}
                    <p>{$k}</p>
            {/for}
    //foreach循环
    	//控制器中
    		 return $this -> fetch('index',[
                'list' => [
                  'username' => 'machunyu',
                  'email' => '2448154972@qq.com'
                ]
         	 ]);
    	//页面中
        {foreach name="list" item="value" key="key"}
    	 	{$key}对应的{$value}
    		{$value.username}   //这样的形式取得二维数组中的name下标
        {/foreach}
    ```

    ​

18. 条件判断

    ```php
    //switch 判断   这样请求  http://thinktest.com/new?level=2
        {switch name="Think.get.level"}                 //最后转换成 $Think['get']['level']
            {case value="1|2"} <p>铜牌会员</p>{/case}   //就是想把值为1和2的都叫铜牌会员
            {case value="3"} <p>黄金会员</p>{/case}
            {case value="4"} <p>钻石会员</p>{/case}
            {default /}<p>游客</p>                     //上面的都不满足时显示的功能
        {/switch}
    //判断值在不在value中 type的值为in或者notin
        {range name="Think.get.level" value="1,2,3" type="in"}  
            当前的level是1,2,3中的一个
        {else/}
            当前的level不是1,2,3中的一个
        {/range}
    //判断在不在一个范围 //type的值为between或者notbetween
        {range name="Think.get.level" value="1,4" type="between"}
            当前的level是1,2,3中的一个
        {else/}
            当前的level不是1,2,3中的一个
        {/range}
    //判断系统常量是否被定义
    	{defined name='APP_PATH'}
    		变量定义了
    	{else /}
    		变量未定义
    	{/defined}
    ```

19. 模板的布局 包含 继承

    ```php
    //引用的标签
    {include file="基于当前模块的view下的目录名+文件名" /}    //例子 {include file="upload/demo" /}
    //继承的标签
    {extend name="基于当前模块的view下的目录名+文件名" /}     //例子 {extend name="upload/base" /}
    	//继承中块状标记的定义
    	{block name="title"}     
    	{/block}
        //在页面中这样使用
    	{block name="title"}
    	这是测试
    	{/block}
    ```

20. ThinkPHP的注释

    ```php
    {/* */}
    ```

    ​