#### YII1.1记录

1. 开启debug

   ```html
   defined('YII_DEBUG') or define('YII_DEBUG', true);
   ```

2. 请求地址

   ```html
   http://hostname/index.php?r=ControllerID/ActionID
   http://hostname/ControllerID/ActionID
   ```

3. request请求对象 `\framework\web\CHttpRequest.php`

   1.  yii获取ip地址                  `Yii::app() -> request -> userHostAddress  `
   2. yii判断提交方式               `Yii::app() -> request -> isPostRequest`
   3. 获得上一页的url以返回   `Yii::app() -> request -> urlReferrer` 
   4. 获得基于网站根目录的当前url  `Yii::app() -> request -> url`
   5. 得到当前域名:                   `Yii::app()->request->hostInfo ` 
   6. 获取当前的项目路径        `dirname(Yii::app()->BasePath)`

4. yii全局model

   ```php
   public static function model($className=__CLASS__)
   {
       if(isset(self::$_models[$className]))
           return self::$_models[$className];
       else
       {
           $model=self::$_models[$className]=new $className(null);
           $model->attachBehaviors($model->behaviors());
           return $model;
       }
   }
   ```

   ​

### Yii记录笔记

1.  框架流程

   ```html
   控制器----》父类控制器----》compoments---》main.php----》index.php
   ```

2.  生成前台项目基础 ( 使用`gii` )

   ```shell
   #进入yii的框架目录, 后面跟的路径和项目名
   yiic webapp ../shop
   ```

3. 渲染页面

   ```php
   /*	
   	render         renderPartial
   	render 和renderpartial之间最大的区别就是：一个是渲染模板，一个不渲染模板。
   */
   $this -> renderPartial('login');
   $this -> render('login');
   ```

4.  布局文件

   * 制作布局文件 layouts/ 文件下, 使用 $content 代表普遍模板内容
   * 设置布局文件 protected 文件夹下 的`controller.php`文件 

5.  后台文件创建

   ```php
   'modules'=>array(
   	// 在 main.php 文件中将 gii模块打开 自动生成后台代码
       'gii'=>array(
           'class'=>'system.gii.GiiModule',
           'password'=>'Enter Your Password Here',
           // If removed, Gii defaults to localhost only. Edit carefully to taste.
           'ipFilters'=>array('127.0.0.1','::1'),
       ),
   ),
   ```

6.  路径 `src ="index.php?r=blog/login/login"`

7.  测试Yii 框架是否有链接上数据库

   * 在控制器里边随便一个地方输出信息：`var_dump(Yii::app()->db);` 
   * Yii::app()：Yii 框架是纯OOP面向对象框架，每次web请求，相当于通过创建一个类的对象，让对象调用相关方法执行。对象是我们框架应用的核心对象，我们也可以通过代码获得这个应用对象(Yii::app())。

8.  模型对象

   ```php
   <?php
   class Region extends CActiveRecord
   {
       public static function model($className=__CLASS__)
       {
           return parent::model($className);
       }
       public function tableName()
       {
           return '{{region}}';
       }
   }
   $RegionModel = Region::model();
   ```

9.  将数据展示到视图模板中

   ```php
                        // 视图    传递到模板的变量  
   $this -> renderPartial('login',array('name' => '马春雨','age' => 20));
   ```

10.  执行原生的sql语句

   ```php
   $sql = 'select id,name,name_py from {{region}} limit 10';
   $res = Region::model() -> findAllBySql($sql);
   foreach ($res as $key => $value) {
   var_dump($value -> name);
   var_dump($value -> id);
   var_dump($value -> name_py);
   }
   var_dump($res);die;
   ```

11.  数据的添加

    ```php
    //1. 单纯的添加
    $RegionObj = Region::model();
    $regionObj -> name = '测试';
    $regionObj -> comment = '评论内容';
    $regionObj -> save(); 
    ```

12.  表单小物件

    ```html
    <!-- <form class="mws-form" action="dashboard.html" method="post"> -->
    <?php $form = $this->beginWidget('CActiveForm'); ?>
    <div class="mws-form-row">
        <div class="mws-form-item">
        <!-- <input type="text" name="username" value="" class="mws-login-username required" placeholder="username"> -->
            <?php
                echo $form->textField($region_model,'name',
                		array(
                			'class' => "mws-login-username required",
                			'placeholder' => 'username',
               				'value' => $name,
                		)
                	);
             ?>
        </div>
    </div>
    <div class="mws-form-row">
        <div class="mws-form-item">
    <!-- <input type="password" name="password" value="" class="mws-login-password required" placeholder="password"> -->
            <?php
                echo $form->passwordField($region_model,'name_py',
                		array(
                		'class' => 'mws-login-password required',
                		'placeholder' => 'password',
                		'value' => $age,
                	)
                );
    		?>
        </div>
    </div>
    <div class="mws-form-row">
        <input type="submit" value="Login" class="btn btn-success mws-login-button">
    </div>
    <?php $this->endwidget(); ?>
    ```

13.  页面重定向

    ```php
    $this -> redirect('./index.php?r=site/index');
    ```

14.  创建模型对象

    1. 模型名:model();
    2. `new 模型名`

15.  小物件表单展现

    * model  attributeLabels()  模型属性与汉字标签名字对应
    * controller 将模型传递到视图里边
    * view  `CaticeForm这个类`

16.  ​

    ​

    ​

    ​
