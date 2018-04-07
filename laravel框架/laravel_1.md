### laravel基础入门

1. 安装   composer create-project laravel/laravel your-project-name --prefer-dist "5.1.*"   
      创建-项目   软件包名称     (项目的文件夹名称)   prefer  dist 优先版本  5.1是LTS版本    

2. laravel框架应用的根目录在`public`下. 需要配置虚拟主机将网站的根目录设置到public目录下

3. laravel程序密钥    .env文件中  `APP_KEY=KIn6iEinoXffdjOgeUl18n93Sov4p4nR`

  ```
  > php artisan key:generate
  		脚本    执行参数
  ```

4. 路由是将信息从源地址传递到目的地的元素.
  位置: /app/Http/routes.php

5. PUT和DELETE请求其实就是请求报文的行的第一段内容为PUT和DELETE.

   ```php
   PUT /admin HTTP/1.1
   Host: www.baidu.com
   User-agent: lamp179
   refer: dongtu
   ```

6. 路由中通过别名来创建url `echo route('b-e',['id' => 10]);`   __前端页面的url尽量不要写死,起别名__ 

  ```php
  Route::get('Admin/bankuai/edit/{id}',[
  	'as' => 'b-e',
  	'uses' => function(){
  		//echo '这是后台的修改操作';
  		//通过别名来创建url
  		echo route('b-e',['id' => 10]); //在项目中涉及到路径的地方尽量使用
  	}
  ]);
  ```

7. `Middleware` 单词本意 中间件

  * 中间件类文件 是放置在 `app/http/Middleware` 下的

  * `php artisan make:middleware LoginMiddleware`  创建中间件的artisan命令

  * 中间件注册的位置 app/http/Kernel.php中

  * 全局中间件  每一个请求都要执行 (检测,信息的记录)

  * 路由中间件  针对部分请求进行执行.

  * 对中间件文件的理解  `\App\Http\Middleware\VerifyCsrfToken::class,`

    ```php
    <?php
    namespace one\two;
    class Test
    {

    }
    //这种方式可以获得这个类加上了命名空间后执行的绝对路径的类名
    //在这基础上将文件名与命名空间联系起来构成了laravel的框架结构
    //这个  class常量是php内置的常量
    echo \one\two\Test::class;   // one\two\Test
    ```

  *  这个文件中的设置方式有两种

    ```php
    'check-login' => \App\Http\Middleware\login::class,
    'check-login' => '\App\Http\Middleware\login',
    ```

  * 中间件对请求的检测方式

    对于单个请求的检测

    ```php
    Route::get('/user/center', function(){

    	echo "不加cookie不让你上";
    }) -> Middleware('check-login');
    ```

    对于路由组的检测

    ```php
    Route::group(['middleware'=>'check-login'],function(){

    	Route::get('/admin', function(){
    		echo '这是后台界面';
    	});

    	Route::get('/admin/user', function(){
    		echo '这是后台界面用户';
    	});
    });
    ```

8. 控制器

  1. 控制器的位置 `app/http/controllers` 

  2. 创建控制器

     * `php artisan make:controller UserController`
     * `php artisan make:controller UserController --plain`

  3. 访问

     * 基本的命令

       ​	生成key   key:generate
       ​	创建中间件 make:middleware
       ​	创建控制器 make:controller

       ​	make:controller --pain  可以创建一个里边没有方法的文件

       * 普通访问    `Route::get('/admin','nameController@index');`

     * 带参数访问      `Route::get('/User/edit/{id}', 'UserController@edit');`

     * __别名方式的访问__ 

       * 设置  Route::get('/admin',['as' => 'u-c','uses' => 'nameController@index']);
       * 使用这个 route('u-c');可以生成路径信息
         *  route('u-c',['a' => 'c']); 这个样做可以带参数 `输出结果http://iloveyou.com/admin?a=c`

     * 中间件的添加

          ```php
          //第一种方式
          Route::get('/user/list','listController@list')->middleware('check-login');

          //第二种方式
          Route::get('/user/list',[
          	'middleware' => 'check-login',
          	'uses' => 'UserController@list'
          ]);
          ```

     * 隐式控制器url路由的规律

       1. 路由规则这样写    `Route::controller('goods','goodsController');` 

       2. 控制器文件中这样写

          ```php
           public function getAdd()  //get请求的    如果是post请求  postAdd
          {
              echo 'getadd';
          }

          浏览器中这样请求 http://iloveyou.com/goods/add 

           public function getEdit()
           {
             echo 'getEdit';
           }
          浏览器中这样请求 http://iloveyou.com/goods/edit

          注意   类中的方法与类名不区分大小写
          ```

     * restful控制器

          1. 路由规则  `Route::resource('article','ArticleController');` 

          2. 控制器中这样写

             ```php
             第一种get
               	public function index()
              	{
                    echo 'index';
                 }
             	浏览器中  http://iloveyou.com/article

             第二种get
                 public function create()
                 {
                      echo 'create';
                 }
             	浏览器中  http://iloveyou.com/article/create

             第三种用post请求
               public function store(Request $request)
               {
                  echo '文章的插入操作';   
               }
               form表单这样添
                   <form action="/article" method="post">
                       {{csrf_field()}}   					//这是一个laravel的验证
                       <input type="text" name="title">
                       <input type="text" name="author">
                       <input type="submit" value="提交">
                   </form>
                浏览器这样请求  http://iloveyou.com/article

             第四种get带参数
                 public function show($id)
                 {
                     echo  'id是'.$id;
                 }
             	浏览器这样请求 http://iloveyou.com/article/20

             第五种get带参数
               public function edit($id)
               {
                 echo 'id是edit'.$id;
               }
               浏览器这样请求 http://iloveyou.com/article/20/edit
             ```

          3. 伪造put与delete请求可以控制模拟提交,在页面添加隐藏域

             ```php
             <form action="/article/10" method="POST">
                 <input type="hidden" name="_method" value="PUT">
                 {{csrf_field()}}
                 <input type="text" name="id" />
                 <input type="submit" value="提交" />
             </form>
             <hr />
             <form action="/article/10" method="POST">
                 <input type="hidden" name="_method" value="Delete">
                 {{csrf_field()}}
                 <input type="text" name="id" />
                 <input type="submit" value="提交" />
             </form>	
             ```

9. 请求

  * 基本获取
    * 获取请求方法` $method = $request -> method();` 
    * 检测方法   `$request->isMethod('post')`
    * 请求路径(从网站的根目录算起 )  ` $path = $request->path();`
    * 获取完整url   ` $url = $request -> url();`
    * 获取ip   `$ip = $request -> ip();`
    * 获取端口   `$getPort = $request -> getPort();` 
  * 提取请求参数
    * 基本获取	     ` $name = $request->input('name');`
    * 设置默认值  `$request->input('page', '1');`
    * 检测是否存在       `$request->has('name')`
    * 提取所有的参数   `$input = $request->all();`
    * 提取部分       __注意这里传的是一个数组__  
      * 提取一个参数   `$request->only(['username', 'password']);`
      * 提取除了某个参数  `$request->except(['credit_card']);`  
    * 获取头信息   `$request->except('host');` 

10. 文件上传

   ```php
   public function move(Request $request)
   {
         $res = $request -> hasFile('img');
    
         if($res){
             $request -> file('img') -> move('./upload/','iloveyou.jpg');
           								//这里文件的路径因为是程序的路径所以是相对路径
         }
    }
   ```

11. cookie操作

    * 设置 
      * `\Cookie::queue('name','iloveyou',10);`
      * `return response('haha')->withCookie('uid',10,10);`
    * 读取
      * `\Cookie::get('name');`
      * `$request->cookie('name');`

12. 闪存信息

    1. 步   将所有的请求参数写入闪存中  ` $request->flash()`

    2. 步   简便使用 ` return back()->withInput();` 就是在表单提交失败以后显示已经填过的信息

       * 设置第一种方式:

         ```php
           public function flash()
            {
               $res = \Session::flash('name','machunyu');
               var_dump($res);
            }

            public function next()
            {
               $res = \Session::get('name');
               var_dump($res);
            }
         ```

       * 第二种方式

         ```php
         //php代码
         public function form()
         {
           return view('form');
         }

         /**
         * 处理表单
          */
         public function doform(Request $request)
         {
           //检测爱好有么有
           if(!$request->has('aihao')) {
             	//退回啊  header('location')
             return back()->withInput();  //withInput() 这个操作就会将输入的内容写入到内存中
           }

         }

         html中的设置
         <form action="/form" method="post">
           用户名：<input type="text" name="username" value="{{old('username')}}"><br>
           邮箱: <input type="text" name="email" value="{{old('email')}}"><br>
           电话: <input type="text" name="phone" value="{{old('phone')}}"><br>
           爱好: <input type="text" name="aihao" value="{{old('aihao')}}"><br>
               {{csrf_field()}}
         <button>提交</button>
         </form>
         ```

13. 响应

    * 返回字符串  `return '哈哈哈';`
    * 设置cookie   `return response('')->withCookie('id',20,60);`
    * 返回json       `return response()->json(['a'=>100,'b'=>2000]);`
    * 下载文件        `return response()->download('web.config');`
    * 页面跳转      `return redirect('/goods/add');`
    * 显示模板      `return response()->view('goods.add');`

14. 模板

    ```php
    <!DOCTYPE html>
    <html lang="en">
    <head>
    	<meta charset="UTF-8">
    	<title>@yield('title')</title>
    	<link rel="stylesheet" href="/node_modules/bootstrap/dist/css/bootstrap.css">
    	@yield('css')
    </head>
    <body>
    	<header style="height:100px;" class="bg-primary"></header>
    	@section('content')
    	<section style="height:400px;">
    		<div class="col-md-8 bg-info"  style="height:400px;"></div>
    		<div class="col-md-4 bg-warning" style="height:400px;"></div>
    	</section>
    	@show

    	@section('footer')
    	<footer style="height:100px;" class="bg-success"></footer>
    	@show

    	@yield('script')

    </body>
    </html>
      
      
      往页面返回
      return view('article.control', [
                'total'=>85,
                'students'=>[
                    ['name'=>'冠景','age'=>18],
                    ['name'=>'卢野','age'=>20],
                    ['name'=>'叶强','age'=>30],
                    ['name'=>'金州','age'=>26],
                ]]);

    @extends('layout.home')

    @section('title')
    	测试模板页面
    @endsection

    @section('content')
    	<section style="height:400px;">
    		<div class="col-md-8" style="background:yellowgreen;height:400px"></div>
    		@include('layout.slider')
    	</section>
    @endsection

    @section('script')
    	<script>
    		alert(123);
    	</script>
    @endsection
          
    @section('css')
    	<style>
    		*{margin:0px;padding:0px;}
    	</style>
    @endsection
    ```


17. 相关路径的解释

   ```php
   make:controller 用的是LINUX系统路径符号，
   Admin/LoginController 代表 Admin 文件夹下创建LoginController，实现就是系统路径
   Route::get('/', 'Admin\WelcomeController@index'); 用的是命名空间分隔符
   return view('admin.login'); 这里的 . 可以换成LINUX系统路径符号 / ,得到同样的效果，因为Laravel 查找 view的时候，有个方法把. 最终替换成 / ,实际也是查找系统路径文件
   ```

18. __强烈注意__ 

   1. Windows下目录使用 `\`  作为目录分隔符, 而Linux下使用 `/`  作为目录分隔符. 但是在windows下也能识别  `/` 所以我们在写php的时候使用 `/` 
   2. php程序操作的根和html  css  javascript 操作的根是不一样的. php 操作的是__系统的根__ , 而在浏览器中解析的语言( html  css   javascript ), 一般来说都是使用的网站根目录
   3. laravel中前端的静态文件的相对路径是相对于前面的路由规则显示的

19. __php中的页面跳转与js中的页面的history.back();的区别__ 

    * php中是使用` header('location:地址');` 这种方式的原理是将页面跳转的请求通过http发送到客户端, 然后客户端在解析的时候发现请求头中有这个请求,就会再次跳转
    * js中的 `history.back();` 是采用请求头中的refer, 返回到上一个页面

20. __注意laravel中思路相关的__ 

    laravel路由中的 `Route::get` 其实现的原理是通过 魔术方法   `__callstatic( );` 

    `__callstatic` 当尝试调用类内部不存在的静态成员方法时, 自动触发

    laravel中  一些属性的访问也是通过魔术方法的来实现的  `__get();  __set();` 

21. __关于返回客户端数据__ 

    自己书写的php代码返给客户端内容时,需要使用`echo`
    在laravel框架中的控制器方法中,返回客户端内容时,可以使用`return`

22. sublime快捷键

   `ctrl+r`快速定位方法的位置
   `ctrl+g`快速定位行号

   sublime快捷键 `ctrl+p` 快速定位文件

23. 共性问题

   - 将laravel的软件包拖到sublime工具区, 其他的工作目录全部移出.

   - httpd-vhosts.conf 文件配置

     ```php
     <VirtualHost *:80>
     	ServerName love.com
     	DocumentRoot C:/wamp/www/class/lamp179/laravel/test/public
     </VirtualHost>
     ```

   - 做本地域名解析时(修改host文件), 将ip设置为127.0.0.1 (hosts 文件修改不成功)

24. 模板的编译文件(临时文件)放置位置  storage/framework/views

25. 类型约束. 函数和方法是可以限制参数的类型的

26. 类中的成员方法名是可以使用关键字的

27. `.gitlgnore`  在git中执行的时候会忽略这个文件中的内容

28. 项目中的`bootstrap`跟之前的前端框架`bootstrap`不是同一个东西.

29. 遇到的错误

    1. `NotFoundHttpException in RouteCollection.php`

       意思为没有找到资源就是404的意思

       手动抛出404命令  `abort(404,'么有找到相关的页面呀!!!');` 

    2. TokenMismatchException 异常, 表单中缺少隐藏参数. {{csrf_field()}}

### laraval需要在学习的

1. 类型约束需要掌握
2. 面向对象的函数
3. 请求体中的参数提取使用input( );
4. 闪存的应用
5. env函数
6. 魔术常量  
7. pdo:PDO::CLASS  返回对象
8. 在使用session中不要使用dd(); 打印,如果打印会造成session不能写入服务器端
9. 在外部尝试调用不存在的或者是非公有的属性时会调用该对象的`__get()` 
