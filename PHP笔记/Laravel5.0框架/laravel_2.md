### laravel

1. 数据库的使用

   * 首先引入数据库  'use  DB';

   * 配置`.env`文件是对当前的开发环境进行配置的位置, 该文件没有进行git版本库跟踪.

     ​	真实的环境   config/database.php

   * 在项目中,模板中的静态资源文件的路径,尽量都是用绝对路径.

   * 在laravel中打印数据一般使用`dd`,  但是 在进行session相关操作时, 尽量不要使用`dd`. 

2. 数据库基本操作

   * 这里的数据库条件操作没有先后顺序,因为是对象的操作,每次的操作只是在往对象中添加东西


   * 基本操作

     ```php
     //执行select语句的
     $res = DB::select('select * from k36s where id < ?',[8]);

     //执行插入语句
     $res = DB::insert('insert into k36s(title,img) values(?,?)',['马春雨','/upload/images/imm.png']);

     //修改操作的
     $res = DB::update('update k36s set title="machunyu",img="machunyu" where id=44');

      //执行数据库的删除操作	
     $res = DB::delete('delete from k36s where id > ?',[40]);

     //执行一般的语句
     $res = DB::statement('create table test_3 (id int primary key auto_increment,name varchar(20))');
     ```

   * 数据库的事务处理(前提必须是支持事务处理)

     ```php
     //开启事务处理
     DB::beginTransaction();

     //准备数据的sql语句
     $res = DB::delete('delete from user where id = ?',[15]);
     $ress = DB::delete('delete from userdetail where id = ?',[15]);

     if($res && $ress) {
       DB::commit();      //执行
       echo 'success';
     } else {
       DB::rollback();    //进行事务的回滚
       echo 'fail';
     }
     ```

   * 操作多个数据库,(执行主从搭建后可以采用这种方式)   config/database.php中将如下

     ```php
     'mysql' => [                              //将mysql换一个别名  如read复制
                 'driver'    => 'mysql',
                 'host'      => env('DB_HOST', 'localhost'),
                 'database'  => env('DB_DATABASE', 'forge'),
                 'username'  => env('DB_USERNAME', 'forge'),
                 'password'  => env('DB_PASSWORD', ''),
                 'charset'   => 'utf8',
                 'collation' => 'utf8_unicode_ci',
                 'prefix'    => '',
                 'strict'    => false,
             ],
     ```


     //代码的设置          使用别名    要执行的操作
     $res = DB::connection('read') -> select('select * from k36s where id = ?',[8]);
     ```

   * laravel中预处理的原理

     ```php
      public function select($sql, $args)
      {
             $pdo = new PDO;
             $stmt = $pdo->prepare($sql);
             $stmt->execute($args);
      }
     ```

   * 数据库的连贯操作

     ```php
     这里的where() 中有默认格式 where('id','=',1000) 所以可以这样写 where('di',10)
     //普通的操作
     $res = DB::table('k36s') -> insert([
         		'title' => '909090900',
         		'img' => 'upload'
         		]);
     $res = DB::table('表名') -> insert([关联数组]);

     //插入并获得最后插入的ID
     $res = DB::table('k36s') -> insertGetId([
         		'title' => '9333333',
         		'img' => 'uuuuooo'
         		]);
     //更新的
     $res = DB::table('k36s') -> where('id',6) -> update([

         			'title' => '88888',
         			'img' => 'uuuuuuu'
         		]);
     //删除
     $res = DB::table('k36s') -> where('id','>',1000)->delete();
     ```

   * 关于数据库的查询操作

     ```php

     //获取一条数据
     $res = DB::table('k36s') -> where('id',10) -> first();

     //获取多条数据
     $res = DB::table('k36s') -> where('id','<','20') -> get();
     $res = DB::table('k36s') -> get();

     //获取表中的某一列数据
     $res = DB::table('k36s') -> lists('title');

     //获取一条数据中的一个字段
     $res = DB::table('k36s') ->where('id',3) -> value('title');

     //获取指定字段的结果
     $res = DB::table('k36s') -> select('id','title') -> get();

     $res = DB::table('k36s') -> select('id','title') -> where('id','<',20) -> get();

     ```

   * 数据库的特殊的操作

     ```php
     //逻辑与
     $res = DB::table('user') -> where('name','xiaohigh') -> where('id','1')->first();

     //逻辑或
     $res = DB::table('user')-> where('username','admin') -> orwhere('username','machunyu') -> get();

     //区间
     $res = DB::table('user') -> whereBetween('id',[2,5]) -> get();

     //在某些值中
     $res = DB::table('user') -> whereIn('id',[3,4,6,8]) -> get();

     //排序
     $res = DB::table('user') -> where('id','<',20) -> orderBy('id','desc') -> get();

     //分页操作
     $res = DB::table('user') ->skip(2) -> take(3) -> get();

     //原生的多表联查
     select * from user left join userinfo on user.id = userinfo.id  where user.id = 3
       
     //多表联查
     $res = DB::table('user')
         		-> leftJoin('userdetail','user.id','=','userdetail.id')
         		-> where('user.id','=',4)
         		-> first();
     //运算
     $res = DB::table('userses')->count();

     //最大值
     $res = DB::table('userses')->max('zhanghu');

     //最小值
     $res = DB::table('userses') -> min('zhanghu');

     //平均值
     $res = DB::table('userses')->avg('zhanghu');
     ```

3. 关于在laravel中添加自定义的函数,自定义的类

   1. 一般建到app目录下        `  app/common/自定义文件` 

   2. 在项目下的composer.json中添加信息

      ```php
       "autoload": {
              "classmap": [
                  "database"
              ],
              "psr-4": {
                  "App\\": "app/"
              },
              "files": [							//这是添加的字段名
                  "app/common/function.php"		//这是添加的路径
              ]			
          },
      ```
      __注意__ 这里是数组`[]` 如果使用`{}`会报 如下的错

      ```mysql
       [Seld\JsonLint\ParsingException]
       "./composer.json" does not contain valid JSON
       Parse error on line 26:
       ...nction.php"        }    },    "autolo
       ---------------------^
       Expected: ':'
      ```

   3. 进入到artisan所在的这层目录,执行`  composer dump-autoload ` 重新生成自动加载文件

   4. 调用

      ```php
      //对于函数的调用
      demo()

      //对于类的调用看自己声明那里有没有加命名空间
      $res = new \Lib();
      dd($res->test());
      ```

4. 安装debugbar

   1. 在命令行中执行`composer require barryvdh/laravel-debugbar`

   2. 配置

      ```php
      在config/app.php里面的providers添加
      Barryvdh\Debugbar\ServiceProvider::class,
      ```

   3. 将debugbar的配置文件放到 laravel 的 config 目录中

      ```php
      php artisan vendor:publish --provider="Barryvdh\Debugbar\ServiceProvider
      ```

      __当遇到错误的时候就会出现__

5. model的操作

   1. 模型的操作的思路    一个类映射是一张表   类的一个实例化对象映射的时候一条记录

   2. 模型的使用  

      * 先将创建的model类引入   
      * 引入时的操作 `  use App\test;`   根据model创建生成的命名空间定

   3. Eloquent 模型约定

      * #### 数据表名称

        模型所对应的默认的表名是在模型后面加s, 如果模型名称后面有s,则表名跟模型名称同名.

              Order   =>  orders
              Goods  =>  goods
              country => countries              模型名  => 表名

      * #### 主键

        Eloquent 也会假设每个数据表都有一个叫做 `id` 的主键字段。你也可以定义一个 `$primaryKey` 属性来重写这个约定。

      * #### 时间戳

        默认情况下，Eloquent 会预计你的数据表中有 `created_at` 和 `updated_at` 字段。如果你不希望让 Eloquent 来自动维护这两个字段，可在模型内将 `$timestamps` 属性设置为 `false`：

      * #### 数据库连接

        默认情况下，所有的 Eloquent 模型会使用应用程序中默认的数据库连接设置。如果你想为模型指定不同的连接，可以使用 `$connection` 属性：

        __原理__  : 是通过一些默认的设置, 创建一个对象,如果设置属性,就直接设置运用类的形式设置

      ```php
        //执行添加      //创建一个空对象,往对象里写入东西
        $test = new test;
        $test -> name = 'machunyu';
        $test -> password = 'kkkkk';
        $test -> save();
        //执行更新     //从数据库中,获得一条数据,然后通过类的操作,实现数据的更新
        $test = test::where('id',2) -> first();
        $test -> name = 'kkk';
        $test -> save()
      ```

      4. 属性设置

         * 设置操作的表名   `public $table = 'userinfos';`
         * 设置默认的时间字段   `public $timestamps = false;` 
         * 修改默认的主键名称  `public $primaryKey = 'uid';`

         ```php
         namespace App;

         use Illuminate\Database\Eloquent\Model;
         use Illuminate\Database\Eloquent\SoftDeletes;
         class Song extends Model
         {
         	use SoftDeletes;
             //手动指定该模型所对应的表
             public $table = 'userses';
             //取消时间戳的限制
             public $timestamps = false;
             //手动指定该模型所的主键
             public $primaryKey = 'uid';
         }
         ```

   6. 基础使用要点

      1. 每一个继承了 Eloquent 的类都有两个 `固定用法` `Article::find($number)``Article::all()`，前者会得到一个带有数据库中取出来值的对象，后者会得到一个包含整个数据库的对象合集。
      2. 所有的中间方法如 `where()` `orderBy()` 等都能够同时支持 `静态` 和 `非静态链式` 两种方式调用，即 `Article::where()...` 和 `Article::....->where()`。
      3. 所有的 `非固定用法` 的调用最后都需要一个操作来 `收尾`，本片教程中有两个 `收尾操作`：`->get()` 和 `->first()`。

7. 模型关系的一对一的关系  

   * #### 配置类位置的原则  就是通过m 找n 的话就在m类中配置关系,如果再想通过n找m的话,就在n类中配置关系


   * users 和userinfos表          创建成这个样式的


   * 创建这两个模型  就是类文件

   * 配置关系  user类 和 userinfo类的关系 

     ```php
     //在user类里面配置   一对一的关系
     //声明成员方法 (方法名随意,一般定义成关系表的名称)
     public function userinfo()
     {						//这里有默认的设置userinfos表中记录用户id字段是 user_id
       						//这里是全称,加命名空间的
       return $this -> hasOne('\app\userinfo');    
     }

     //用户与文章的关系   一对多的关系  在user类中配置关系
     protected function article() 
     {
       //如果arcticle中记录用户id的字段设置成user_id  这里可以不用写的
       return  $this -> hasMany('\App\Article','uid'); 
     }

     ```

   * 在使用的页面引入使用的这个类   `  use App\User;` 

     ```php
     //获得关联表的信息就是 userinfo 表的信息
     //这里获得了id为3的userinfo表中的信
     $user = user::find(3);
     $info = $user -> userinfo() -> first(); 
     //上面那种方法等价于 
     $info = $user -> userinfo;   //实现的原理是 调用了 __get(); 魔术方法

     //获得当前的这个用户发表的文章
     $user = user::find(2);
     $arcs = $user -> article;
     ```

8. 模型关系的多对多的关系   用户与收藏文章的关系

   * 创建一个表关联表

   * 字段设置成  user_id  和  article_id

   * 配置关系

     ```php
     //多对多  还是在user表中配置
      protected function shoucang()
     {
          //中间表的默认格式  单词asc码排序  然后用 `_`连接
          //user_id 当前模型的名字+id
          //article_id  关联模型的名字+id  
         return $this->belongsToMany('\App\Article','article_user','user_id','article_id');
        
        //当表的格式符合上面要求的格式,就可以简化成这个样子
        return $this->belongsToMany('\App\Article');
     }
     ```

   * 调用

     ```php
     //获取文章的收藏者
     $arc = \App\Article::find(5);
     ```