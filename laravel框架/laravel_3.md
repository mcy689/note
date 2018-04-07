#### laravel

1. 简介的bootstrap实现的后台模板.
   https://startbootstrap.com/template-overviews/sb-admin-2/

   ace后台模板也不错哦!!!

2. 数据库建表的方式

   - 命令行  create table ....
   - navicat 快速的创建
   - migration (数据库迁移)

3. 创建模型并创建数据库表结构

   ```php
   php artisan make:model Goods -m   
   //这个命令表示即创建模型 app/goods.php 
   //也创建数据库表结构database/migrations/下的可以执行数据库迁移的文件
   ```

4. 数据迁移

   ```php
   //先创建数据库迁移文件 将以前存在的那两个文件删除
   php artisan make:migration create_users_table
   //执行上一个命令生成的文件在 databases/migrations/目录下
     
   //执行up方法
   php artisan migrate  

   //当数据表字段改变时执行操作
   php artisan migrate:refresh
     
   ```

   * 数据库的操作

     ```php
     //删除数据库中单个字段的 
         if (Schema::hasColumn('users','age')) {
            Schema::table('users', function ($table) {
              $table->dropColumn('age');
            });                     
          }

         //然后执行这个命令 php artisan migrate:refresh
     ```

5. 表单验证

   ```php
   //这段代码的意思是post表单中的username字段,必须填
   $this -> validate($request, [
               'username' => 'required'
               ]);
   //验证规则
   	 //是对post传递过来的字段email中的值   去数据库中的users表中的email字段中检索
         'email' => 'unique:users,email' 
   //完整的验证
           
           $this -> validate($request, [
               'username' => 'required|regex:/^\w{6,16}$/|unique:users,username',
               'password' => 'required|regex:/^\S{6,16}$/|same:repassword',
               'email' => 'required|email' //laravel 中封装好的email检验
               ],[
                   'username.required' => '用户名必须填',
                   'username.regex' => '用户名格式不对',
                   'username.unique' => '用户名已经存在',
                   'password.required' => '密码必须填',
                   'password.regex' => '密码格式不对',
                   'password.same' => '两次密码输入不正确',
                   'email.email' => '邮箱格式不正确'
               ]);

    //闪存laravel自动已经做了直接在页面中使用
   		{{old('username')}}  
           
           
   //在html中显示错误
   @if (count($errors) > 0)
     	<div class="alert alert-danger">
     		<ul>
    		 @foreach ($errors->all() as $error)
     			<li>{{ $error }}</li>
     		@endforeach
     		</ul>
    	 </div>
     @endif
     	
   ```

   * 自动检测流程   手册中的'表单请求验证'

     - 创建请求检测类  make:request
     - 修改类文件代码
       - 将authorize 方法中的`false`改为`true`
       - 将验证规则添加到 `rules` 方法中
       - 将中文提醒放置到 `messages` 方法中
       - __在laravel中想修改表中的数据必须先将数据获取到对象中__ 

     ```php
     //先执行 php artisan make:request UserInsertRequest 
     	//在 /app/http/下生成UserInsertRequest文件
     	
     //然后在文件中加规则
     	//这是返回的结果
     	 public function authorize()
         {
             return true;
         }

        	//这是规则
         public function rules()
         {
             return [
                 'username' => 'required|regex:/^\w{6,16}$/|unique:users,username',
                 'password' => 'required|regex:/^\S{6,16}$/|same:repassword',
                 'email' => 'required|email'
                 ];
         }
     		//定义返回的信息 方法名是固定的
     	 public function Messages()
         {
             return [
                     'username.required' => '用户名必须填',
                     'username.regex' => '用户名格式不对',
                     'username.unique' => '用户名已经存在',
                     'password.required' => '密码必须填',
                     'password.regex' => '密码格式不对',
                     'password.same' => '两次密码输入不正确',
                     'email.required' => '邮箱必须填';
                     'email.email' => '邮箱格式不正确'
                 ];
         }


     添加用户
     //在文件中使用上面的验证规则
     	use App\Http\Requests\UserInsertRequest;
     						 //修改这里
     	public function store(UserInsertRequest $request)
         {
           
           //这里执行入库
           	$user = new User();   //模型

             $user -> username = $request -> input('username');    //获得数据
             $user -> password = Hash::make($request -> input('password'));  //用哈希将密码加密
             $user -> email = $request -> input('email');
           
           //执行文件上传
           if($request -> hasFile('img')){

                 //获取文件名
                 $filename = time().rand(1000,9999);
                 //获取文件后缀
                 $suffix = $request -> file('img') -> getClientOriginalExtension();
                 //将文件移动，并改名
                 $request -> file('img')-> move('./upload',$filename.".$suffix");
                 //拼接上传的路径信息
                 $path = '/upload/'.$filename.".$suffix";
                 //将文件路径写入对象中
                 $user -> img = $path;
             }
           //执行保存数据
           	if($user -> save()){

                 return redirect('/user');  //执行成功跳转
             }  else {

                 return back();  //执行不成功再跳转回来
             }   
         }

      编辑用户
      //根据获得的id从数据库中获得数据  如果成功就显示数据 不成功就报错
          $user = User::findoOrFail($id); 
              //上述代码等效于
              $user = User:find($id);
              if(empty($user)){
                abort(404);		 //抛出404页面
              }
     //显示编辑页面
      	 return view('Admin.user.edit',['user' => $user,'id'=>$id]);

     //可以通过这种形式来实现往页面中带值
      	return redirect('/user/')-> with('info','更新成功'); 
      	在页面中使用 {{Session::get('info')}} 读取里面的内容 
     ```


6. 数据的填存

  ```php
  //命令行生成填充数据的文件   //生成的文件放在  'database/seeds'目录下
  php artisan make:seeder UsersSeeder

  //在文件下写入逻辑
   public function run()
      {
          $data = [];

          for ($i = 0;$i < 20; $i++) {

          	$tmp['username'] = str_random(10);
          	$tmp['password'] = hash::make('machunyu');
          	$tmp['email'] = rand(10000,99999).'@qq.com';
          	$tmp['img'] = '/upload/14983782659121.jpg';

          	$data[] = $tmp;
          }

          DB::table('users') -> insert($data);
      }

  //在同级目录下的    DatabaseSeeder.php 中
  				//修改成对应的文件名
  		$this->call(UsersSeeder::class);
  //运行php artisan db:seed  详细解释看手册  
   	看报错的信息
       //[Symfony\Component\Debug\Exception\FatalThrowableError]
       //Class 'hash' not found
       添加 use Hash 

       //[ErrorException]
       //The use statement with non-compound name 'Hash' has no effect
       去掉解决了 use Hash 
  ```

7. 分页的实现

  ```php
  //模板中的if判断语句   @if($key % 2 == 0) odd @else even @endif
   //在控制器中给页面带参数  
  	return view('Admin.user.index',['users' => $users,'data' => $request -> all()]);
  //查询数据库
  	$users = User::orderBy('id','asc')
              ->where('username','like','%'.$username.'%')   //获得搜索的条件
              -> paginate($request -> input('num',10));     //从页面中取出几条 

  //模板中分页中实现带值的  {!! $users->appends($data)->render() !!}
  ```

8. php的原生手动分页 超级大数组 . array_slice

9. migration 是一种数据库表结构的管理方式

10. array_slice(); 也可以实现分页效果,数据源必须是数组

11. 课堂杂项

   * 修改hosts文件失败的一种特殊情况, 就是360软件.  尝试关闭360再次修改.
   * 在开发phpcms项目的时候, 在本地配置虚拟主机时, 选用的域名尽量跟实际的域名保持一致.
   * findOrfail();  可以少一个判断
   * 在存储图片的时候使用绝对路径
   * $user['id']    实现了这个接口php中 ArrayAccess,就可以用数组形式使用数组
