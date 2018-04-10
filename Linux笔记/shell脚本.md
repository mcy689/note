### shell基础

1. 变量

   * 定义变量时, 变量名不加美元符号

     ```shell
     name='test'
     ```

   * 使用变量

     ```shell
     echo $name
     ```

   * 只读变量( 使用readonly命令可以将变量定义为只读变量, 只读变量的值不能被改变 )

     ```shell
     name='ddd'
     readonly name
     name='ddddd'  -bash: name: readonly variable
     ```

   * 使用unset 命令可以删除变量

     ```shell
     unset variable_name
     ```

   * 变量类型

     1. __局部变量__  局部变量在脚本或命令中定义, 仅在当前shell实例中有效,
     2. __环境变量__  所有的程序, 包括shell启动的程序, 都能访问环境变量, 有些程序需要环境变量来保证其正常运行
     3. __shell__ 遍历 shell变量是由shell程序设置的特殊变量

2. __Shell字符串__ 

   * 单引号

     1. 单引号里的任何字符都原样输出, 单引号字符串中的变量是无效的
     2. 单引号字符串中不能出现单引号( 对单引号使用转义符也不行 )

   * 双引号

     1. 双引号里可以有变量
     2. 双引号里可以转移字符

   * 拼接字符串

     ```shell
     test='test1'
     greeting='hello,'$test' !'
     greeting='hello,'{$test}' !'
     ```

   * 提取子符串

     ```shell
     #查看字符串长度   
        string='abcd'
        echo ${#string}
     #提取字符串 从字符串第2个字符开始截取4个字符
     	string='my name is hello world'
     	echo ${string:1:4}  #y na
     #查找字符串 查找字符 c的位置
     	string='abcdefg'
     	echo `expr index "$string" c` #3
     ```

3. shell数组

   * bash 支持一维数组 ( 不支持多维数组 )

   * 数组元素的下标由 0 开始编号

     ```shell
     #定义数组
     	# 数组名=(值1 值2 值3)   定义数组
         # array_name[0]=value0  可以单独定义数组的分量
     #读取数组
     	#${数组名[小标]}
     	
     	echo ${array_name[0]}

     ```

     ​

   ​