# C语言入门

## 编程初步

### 计算机内存

1. 计算机中的位以8个为一组，每组的8个位称为一个字节（byte）。
2. 每个字节在内存里都有一个和其他字节不同的地址，字节的地址唯一地表示计算机内存中的字节。

### 变量

1. 变量是计算机里一块特定的内存，它是由一个或多个连续的字节所组成。
2. 声明变量时就初始化它一般是很好的做法。它可以避免对初始值的怀疑，当程序运作不正常时，它有助于追踪错误。避免在创建变量时使用垃圾值。
3. 相同类型的不同变量总是占据相同大小的内存（字节数）。但不同类型的变量需要分配的内存空间就不一样。

### 数据类型

1. 任何数，只要有小数点，就是double 类型，除非加了f。
2. 使用浮点数进行除法运算，会得倒正确的结果--- 至少是一个精确到固定位数的值。

### 定义常量

```c
#define PI 3.141592f
```

## 字符串

### 字符串常量

1. 字符串常量是放在一对双引号中的一串字符或者符号。

2. 内存中字符串的例子

   ![201812121150](./img/201812121150.png)

3. 字符串的结尾 `\0`

### 存储字符串的变量

1. 声明

   ```c
   1.方法一
   	//这个变量最多存储19个字符的字符串，因为必须给终止符号提供一个数组元素。
   	char saying[20];
   2.方法二
   	//编译器会指定一个足够容纳这个初始化字符串常量的数值。
   	char saying[] = "This is a string";
   ```


## 指针

### 基本概念

1. 存储地址的变量称为指针。
2. 类型名 void* 表示没有指定类型，所以 void* 类型的指针可以包含任意类型的数据项地址。类型 void* 常常用做参数类型，或以独立于类型的方式处理数据的函数的返回值类型。

### 声明指针

1. 代码

   ```C
   //下面两种方式是等价的
   int *pnumber;
   int* pnumber;
   ```

   __注意__ 未初始化的指针式非常危险的，比未初始化的普通变量危险的多，所以应总是在声名指针时对它初始化。

   ```C
   int *pnumber = NULL;
   ```

2. NULL 

   是在标准库中定义的一个常量，对于指针它表示0。NULL 是一个不指向任何内存位置的值。这表示，使用不指向任何对象的指针，不会意外覆盖内存。

3. 用已声明的变量地址初始化子针变量

   ```c
   int number = 99
   int *pnumber = &number;
   ```

4. 批量声明

   ```c
   double value,*pVal=NULL;
   ```

### 使用指针

1. 取消引用的指针

   ```c
   //*号表示访问 pnumber 变量所指向的内容。这里它是变量 number 的内容。
   *pnumber += 25;
   ```

2. 改变指针的指向

   ```c
   int value = 999;
   int *pnumber = NULL;
   pnumber = &value;
   *pnumber += 25;
   ```

3. 指针可以包含同一类型的任意变量的地址，所以使用一个指针变量可以改变其他许多变量的值。

4. 使用指针`++`数值

   ```c
   int *pnum = NULL;
   int val = 1;
   pnum = &val;
   ++(*pnum);
   (*pnum)++;
   ```

5. NULL 的指针，它是相当于数字0的指针。

### 指向常量的指针

1. 声明指针时，可以使用 const 关键字指定，该指针指向的值不能改变。

   ```c
   long value = 9999L;
   const long *pvalue = &value;
   ```

2. 把 pvalue 指向的值声明为 const。所以编译器回检查是否有语句试图修改 pvalue 指向的值，并将这些语句标记为错误。

   ```c
   *pvalue = 888L;
   ```

3. pvalue 指向的值不能改变，但可以对 value 进行任意操作

   ```c
   value = 777L;
   ```

### 常量指针

1. 声明指针时，使的指针中存储的地址不能改变。

   ```c
   int count = 43;
   int *const pcount = &count;
   ```

2. pcount 该指针存储的地址不能改变。编译器会检查代码是否无意中把指针指向其他地方。

   ```c
   //报错
   int item = 34;
   pcount = &item;
   ```

3. 但使用 pcount，仍可以改变 pcount 指向的值

   ```C
   *pcount = 345;
   ```

### 数组和指针

1. 数组和指针
   * 数组是相同类型的对象集合
   * 指针是一个变量，它的值是给定类型的另一个变量或者常量的地址。使用指针可以在不同的时间访问不同的变量，只要它们类型相同即可。

2. 如果需要传入一个引用，但没有使用 & 运算符，而是使用数组名称，同时也没有带索引值，它就引用数组的__第一个元素的地址__ 。但数组不是指针，它们有一个重要区别：可以改变指着包含的地址，但不能改变数组名称引用的地址。

   ```c
   #include <stdio.h>
   int main()
   {
       char multiple [] = "My string";
       char *p = &multiple[0];
       printf("The address of the first array element : %p\n",p);
       p = multiple;
       printf("The address of the first array name:%p\n",multiple);
       return 0;
   }
   ```

3. 指针地址的累加。

   ```c
   #include <stdio.h>
   #include <string.h>
   // #include <stdlib.h>
   int main(void)
   {
       char multiple[] = "a string";
       char *p = multiple;
   
       for(int i =0;i < strnlen(multiple,sizeof(multiple));i++){
           printf("multiple[%d] = %c *(p+%d) = %c &multiple[%d] = %p p+%d = %p\n",i,multiple[i],i,*(p+i),i,&multiple[i],i,p+i);
       }
       return 0;
   }
   ```

### 多维数组和指针

1. 如下代码

   ```c
   #include <stdio.h>
   int main(void)
   {
       char board[3][3] = {
           {'1','2','3'},
           {'4','5','6'},
           {'7','8','9'}
       };
       printf("address of board :%p\n",board);
       printf("address of board[0][0]:%p\n",&board[0][0]);
       printf("value of board[0]:%p\n",board[0]);
       return 0;
   }
   //输出结果
   /*
   address of board :0x7ffee1b2fb8f
   address of board[0][0]:0x7ffee1b2fb8f
   value of board[0]:0x7ffee1b2fb8f
   */
   ```

   * 3个输出值都是相同的，说明：声明一维数组 `x[n1]`时，`[n1]`放在数组名称之后，告诉编译器它是一个有n1个元素的数组，声明二维数组`y[n1][n2]` 时，编译器就会创建一个大小为n1的数组，它的每个元素是一个大小为n2的数组。
   * 声明二维数组时，就是在创建一个数组的数组，因此，用数组名称和一个索引值访问这个二维数组时，例如 `board[0]`，就是在引用一个子数组的地址。仅使用二维数组名称，就是引用该二维数组的开始地址，它也是第一个子数组的开始地址。
   * board、`board[0]` 和 `&board[0]`的数值相同，但它们并不是相同的东西，board 是char型二维数组的地址，`board[0]` 是char 型以为子数组的地址，它是 board 的一个子数组，`&board[0][0]`是char型数组元素的地址。







