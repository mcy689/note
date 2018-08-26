#### 积硅步, 致千里

1. 乐观锁

   * 乐观锁不是数据库自带的，需要我们自己去实现。乐观锁是指操作数据库时(更新操作)，想法很乐观，认为这次的操作不会导致冲突，在操作数据时，并不进行任何其他的特殊处理（也就是不加锁），而在进行更新后，再去判断是否有冲突了。
   * 通常实现是这样的：在表中的数据进行操作时(更新)，先给数据表加一个版本(version)字段，每操作一次，将那条记录的版本号加1。也就是先查询出那条记录，获取出version字段,如果要对那条记录进行操作(更新),则先判断此刻version的值是否与刚刚查询出来时的version的值相等，如果相等，则说明这段期间，没有其他程序对其进行操作，则可以执行更新，将version字段的值加1；如果更新时发现此刻的version值与刚刚获取出来的version的值不相等，则说明这段期间已经有其他程序对其进行操作了，则不进行更新操作。

   ```php
   function dummy_business_5() {
       for ($i = 0; $i < 1000; $i++) {
           $connection=Yii::app()->db;
           $transaction = $connection->beginTransaction();
           try {
               $model = Test::model()->findByPk(1);
               $num = $model->num + 1;
               $version = $model->version;

               $sql = 'UPDATE {{test}} SET num = '.$num.' + 1, version = version + 1 WHERE id = 1 AND version = '.$version;
               $ok = $connection->createCommand($sql)->execute();
               if(!$ok) {
                   $transaction->rollback(); //如果操作失败, 数据回滚
                   $i--;
                   continue;
               } else {
                   $transaction->commit(); //提交事务会真正的执行数据库操作
               }
           } catch (Exception $e) {
               $transaction->rollback(); //如果操作失败, 数据回滚
           }
       }
   }
   ```

2. 悲观锁

   * 与乐观锁相对应的就是悲观锁了。悲观锁就是在操作数据时，认为此操作会出现数据冲突，所以在进行每次操作时都要通过获取锁才能进行对相同数据的操作，这点跟java中的synchronized很相似，所以悲观锁需要耗费较多的时间。另外与乐观锁相对应的，悲观锁是由数据库自己实现了的，要用的时候，我们直接调用数据库的相关语句就可以了. 
   * 悲观锁涉及到的另外两个锁概念就出来了，它们就是共享锁与排它锁。共享锁和排它锁是悲观锁的不同的实现，它俩都属于悲观锁的范畴。

3. 共享锁指的就是对于多个不同的事务，对同一个资源共享同一个锁。相当于对于同一把门，它拥有多个钥匙一样。就像这样，你家有一个大门，大门的钥匙有好几把，你有一把，你女朋友有一把，你们都可能通过这把钥匙进入你们家，进去啪啪啪啥的，一下理解了哈，没错，这个就是所谓的共享锁。

   刚刚说了，对于悲观锁，一般数据库已经实现了，共享锁也属于悲观锁的一种，那么共享锁在mysql中是通过什么命令来调用呢。通过查询资料，了解到通过在执行语句后面加上lock in share mode就代表对某些资源加上共享锁了。

   比如，我这里通过mysql打开两个查询编辑器，在其中开启一个事务，并不执行commit语句

   city表DDL如下：

   **[plain]** [view plain](https://blog.csdn.net/puhaiyang/article/details/72284702#) [copy](https://blog.csdn.net/puhaiyang/article/details/72284702#)

   1. CREATE TABLE `city` (  
   2.   `id` bigint(20) NOT NULL AUTO_INCREMENT,  
   3.   `name` varchar(255) DEFAULT NULL,  
   4.   `state` varchar(255) DEFAULT NULL,  
   5.   PRIMARY KEY (`id`)  
   6. ) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;  

   ![img](https://img-blog.csdn.net/20170516162604233?watermark/2/text/aHR0cDovL2Jsb2cuY3Nkbi5uZXQvcHVoYWl5YW5n/font/5a6L5L2T/fontsize/400/fill/I0JBQkFCMA==/dissolve/70/gravity/Center)

   begin;
   SELECT * from city where id = "1"  lock in share mode;

   然后在另一个查询窗口中，对id为1的数据进行更新

   ![img](https://img-blog.csdn.net/20170516163339831?watermark/2/text/aHR0cDovL2Jsb2cuY3Nkbi5uZXQvcHVoYWl5YW5n/font/5a6L5L2T/fontsize/400/fill/I0JBQkFCMA==/dissolve/70/gravity/Center)

   update  city set name="666" where id ="1";

   此时，操作界面进入了卡顿状态，过几秒后，也提示错误信息

   **[SQL]update  city set name="666" where id ="1";[Err] 1205 - Lock wait timeout exceeded; try restarting transaction**

   那么证明，对于id=1的记录加锁成功了，在上一条记录还没有commit之前，这条id=1的记录被锁住了，只有在上一个事务释放掉锁后才能进行操作，或用共享锁才能对此数据进行操作。

   再实验一下：

   ![img](https://img-blog.csdn.net/20170516164857534?watermark/2/text/aHR0cDovL2Jsb2cuY3Nkbi5uZXQvcHVoYWl5YW5n/font/5a6L5L2T/fontsize/400/fill/I0JBQkFCMA==/dissolve/70/gravity/Center)

   update city set name="666" where id ="1" lock in share mode;

   **[Err] 1064 - You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'lock in share mode' at line 1**

   加上共享锁后，也提示错误信息了，通过查询资料才知道，对于update,insert,delete语句会自动加排它锁的原因

   于是，我又试了试SELECT * from city where id = "1" lock in share mode;

   ### 排它锁

   排它锁与共享锁相对应，就是指对于多个不同的事务，对同一个资源只能有一把锁。

   与共享锁类型，在需要执行的语句后面加上for update就可以了

   ## 行锁

   行锁，由字面意思理解，就是给某一行加上锁，也就是一条记录加上锁。

   比如之前演示的共享锁语句

   SELECT * from city where id = "1"  lock in share mode; 

   由于对于city表中,id字段为主键，就也相当于索引。执行加锁时，会将id这个索引为1的记录加上锁，那么这个锁就是行锁