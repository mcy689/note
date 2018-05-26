#### Yii2.0学习记录 (`\Yii::getVersion();`)

1. 安装和启动

   * Yii2.0检测环境

     ```html
     http://hostname/yii2.0/basic/requirements.php
     ```

   * 配置cookie, 用于框架的cookie的验证

     ```php
     报错: yii\web\Request::cookieValidationKey must be configured with a secret key
     basic/config/web.php
     !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
        'cookieValidationKey' => '',
     ```

2. 控制器的创建

   ```php
   <?php
   namespace app\controllers;
   //每一个控制器必须继承这个类
   use yii\web\Controller;
   class HelloController extends Controller
   {
   	public function actionIndex()
   	{
   		echo 'hello world';
   	}
   }
   ```

3. 请求的基础

   ```php
   /*请求类
   	 response 响应类  \basic\vendor\yiisoft\yii2\web\ResponseCollection.php
         request  请求类   \basic\vendor\yiisoft\yii2\web\RequestCollection.php
         header 设置请求头的类  \basic\vendor\yiisoft\yii2\web\HeaderCollection.php
    */
   public function actionTest()
   {
       $response = \Yii::$app->response;
       // $response->setStatusCode(400,'nothing');
       //浏览器允许缓存
       // $response->headers->add('Pragma','max-age=5');
       //跳转
       // $response->headers->add('location','http://www.baidu.com');
       //跳转连接
       // $this->redirect('https://www.baidu.com',302);
       //文件下载
       // $response->headers->add('content-disposition','attachment;filename="1.jpg"');
       $response->sendFile('./b.jep');
   }
   ```

4. session 和cookie

   ```php
   //session 类的位置   \basic\vendor\yiisoft\yii2\web\Session.php
   //session 类的位置   \basic\vendor\yiisoft\yii2\web\CookieController.php
   <?php
   namespace app\controllers;
   //每一个控制器必须继承这个类
   use yii\web\Controller;
   use yii\web\Cookie;   //需要将 \basic\vendor\yiisoft\yii2\web\Cookie.php 引入
   public function actionTestsess()
   {
       //session
       $session = \Yii::$app->session;
       if ($session->getIsActive()) {
           echo 'session is start';
       }
       echo $session->get('user');
        $session->set('user','machunyu');
   	//也可以这样
       $session['user'] = 'zhangsan';
       $session['name'] = 'dddd';
       //cookie
       /*设置
       $cookies = \Yii::$app->response->cookies;
   	$cookie_data = array('name'=>'user','value'=>'lisi2');
   	$cookies->add(new Cookie($cookie_data));*/
       $cookies = \Yii::$app->request->cookies;
       echo $cookies->getValue('user');
   }
   ```

5. 视图相关

   * 防止跨站攻击的

     ```php
     <?php
     use yii\helpers\Html;
     use yii\helpers\HtmlPurifier;
     ?>
     <h1><?=Html::encode($view_hello_str);?><h1> 		//转义
     <h1><?=HtmlPurifier::process($view_hello_str)?></h1> // 过滤直接删除js代码
     ```

   * 模板布局( `yii2.0\basic\vendor\yiisoft\yii2\base\view.php` )

     ```php
     //控制器中
     public $layout = 'common';   //通过控制器的layout这个属性来设置公共方法
     public function actionRendertest()
     {
     	return $this->render('rendertest');
     }
     //common.php
     <!DOCTYPE html>
     <html>
     <head>
     	<meta charset="utf-8">
     	<meta http-equiv="X-UA-Compatible" content="IE=edge">
     	<title></title>
     	<link rel="stylesheet" href="">
     </head>
     <body>
     	<?php if (!isset($this->blocks['block1'])): ?>
     		<?=$this->blocks['block1'];?>
     	<?php else: ?>
     		<h1>hello common</h1>
     	<?php endif; ?>
     	<?= $content; ?>
     </body>
     </html>
     //rendertest.php 视图   这边定义数据块
      hello world
     <?php $this->beginBlock('block1');?>
     <h1>rendertest</h1>
     <?php $this->endBlock();?>
     ```

6. 数据模型

   * 记录数据库连接的属性 `yii2.0\basic\vendor\yiisoft\yii2\db\Connection.php`

     ```php
     $res = Users::find()->where(['id'=>1])->all();
     $res = Users::find()->where(['<','id',3])->all();
     $res = Users::find()->where(['between','id',1,2])->all();
     $res = Users::find()->where(['like','username','admi'])->all();
     //将数据库返回的信息转换成数组
     $res = Users::find()->where(['between','id',1,2])->asArray()->all();
     //批量查询
     Users::deleteAll('id<:id',array('id'=>0));
     ```

   * `Users::className();` 获取model的class

   * 

