# C语言入门

## 基础概念

1. 预处理指令

   ```c
   #include <stdio.h>
   /*
   	符号#表示这是一个预处理指令,告诉编译器在编译源代码之前，要先执行一些操作。编译器在编译过程开始之前的预处理间断处理这些指令。
   */
   ```

2. 预处理器

   宏是提供给与处理器的指令，来添加或修改程序中的C语句。

   ```c
   #define INCHES_PER_FOOT 12
   ```

3. 输出格式

   * `%d` 以十进制输出带符号整数
   * `%f` 以小时形式输出单、双精度小数
   * `%c` 输出单个字符。

## 常用函数

1. `sizeof` 运算符

   * 使用 `sizeof` 运算符可以确定给定的类型占据多少字节。

   * 表达式`sizeof(int)` 会得到 int 类型的变量所占据的字节数，所得的值是一个`size_t` 类型的整数。 
   * `size_t` 类型在标准头文件 `<stdio.h>` （和其它头文件）中定义。

   ```c
   size_t size = sizeof(long long int)
   ```

## 编程初步

### 计算机内存

1. 计算机中的位以8个为一组，每组的8个位称为一个字节（byte）。
2. 每个字节在内存里都有一个和其他字节不同的地址，字节的地址唯一地表示计算机内存中的字节。
3. 1KB 是 1024 字节。

### 变量

1. 定义变量

   ```c
   int num;     //先声明
   int num = 0; //声明并初始化
   ```

2. 变量是计算机里一块特定的内存，它是由一个或多个连续的字节所组成。

3. 声明变量时就初始化它一般是很好的做法。它可以避免对初始值的怀疑，当程序运作不正常时，它有助于追踪错误。避免在创建变量时使用垃圾值。

4. 相同类型的不同变量总是占据相同大小的内存（字节数）。但不同类型的变量需要分配的内存空间就不一样。

### 数据类型

1. 任何数，只要有小数点，就是double 类型，除非加了f。
2. 使用浮点数进行除法运算，会得倒正确的结果--- 至少是一个精确到固定位数的值。

### 定义常量

```c
#define PI 3.141592f
```

### 整数类型

|          类型          | 字节数 |
| :--------------------: | :----: |
|      signed char       |   1    |
|       short int        |   2    |
|          int           |   4    |
|        long int        |   4    |
|     long long int      |   8    |
|     unsigned char      |   1    |
|   unsigned short int   |   2    |
|      unsigned int      |   4    |
|   unsigned long int    |   4    |
| unsigned long long int |   8    |

```c
#在数值的后面加上一个大写L或小写l，表示long类型
long Big_Number = 187600L;
#将整数常量指定为long long 类型时，应添加两个L
long long really_big_number = 123456789LL;
#将常量指定为无符号类型时，应添加U
unsigned int count = 1000U;
unsigned long value = 9999999999UL;
#最大范围的整数
unsigned long long max = 946073047250800ULL
```

### 浮点数类型

|   关键字    | 字节数 |     数值范围     |
| :---------: | :----: | :--------------: |
|    float    |   4    | 精确到6到7位小数 |
|   double    |   8    |  精确到15位小数  |
| long double |   12   |  精确到18位小数  |

## 字符串

1. char 类型的变量有双重性，可以把它解释为一个字符，也可以解释为一个整数。

   ```c
   char letter = 'C'
   letter = letter + 3;
   ```

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

2. 如果需要传入一个引用，但没有使用 & 运算符，而是使用数组名称，同时也没有带索引值，它就引用数组的__第一个元素的地址__ 。但数组不是指针，它们有一个重要区别：可以改变指针包含的地址，但不能改变数组名称引用的地址。

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

1. 二维数组和一维数组的关系

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

2. 二维数组的存储方式

   ```c
   //二维数组的元素存储为一个很大的一维数组，编译器确保可以像一维数组的数组那样访问它。
   #include <stdio.h>
   int main()
   {
       char board[3][3] = {
           {'1','2','3'},
           {'4','5','6'},
           {'7','8','9'}
       };
       for(int i = 0; i < 9; ++i){
           printf("board:%c\n",*(*board+i));
       }
       return 0;
   }
   /*
   board:1
   board:2
   board:3
   board:4
   board:5
   board:6
   board:7
   board:8
   board:9
   */
   ```

3. 多维数组和指针

   ```c
   #include <stdio.h>
   int main()
   {
       char board[3][3] = {
           {'1','2','3'},
           {'4','5','6'},
           {'7','8','9'}
       };
       char *pboard = *board;//char *pboard = &board[0][0];
       for(int i = 0; i<9;++i){
           printf("board:%c\n",*(pboard+i));
       }
       return 0;
   }
   ```

   **注意：** 取消了对 board 引用`(*board)`，得到了需要的地址，因为 board 是 `char **` 类型，是指针的指针，是子数组 `board[0]`的地址，而不是一个元素的地址（它必须是 char*类型）。 

## 内存的使用

### 基本概念

1. **堆**： 在程序的执行期间分配内存时，内存区域中的这个空间称为堆。堆中的内存时由程序员控制分配和释放的。
2. **堆栈：** 用来存储分配给函数的参数和本地变量。在执行完该函数后，存储参数和本地变量的内存空间就会释放。

### 动态内存分配：malloc() 函数

```c
#分配25个 int 值的内存
int *pNumber = (int*)malloc(25*sizeof(int));

#include <stdio.h>
#include <string.h>
#include <stdlib.h>
int main()
{
    int *p = NULL;
    p = (int *)malloc(sizeof(int)*10);
    if(p == NULL){
        printf("Cant get memory! \n");
    }
    printf("%d\n",*p);
    memset(p,0,sizeof(int)*10);
    printf("%d\n",*p);
    *p = 10;
    printf("%d\n",*p);
    return 0;
}
```

### 释放动态分配的内存

要释放动态分配的内存，必须能访问引用内存快的地址。

```c
//要释放动态分配的内存，而该内存的地址存储在 pNumber 指针中。
free(pNumber);
pNumber = NULL;
```

### 用 calloc() 函数分配内存

1. 它给内存分配为给定大小的数组，

2. 它初始化了所分配的内存，所有的位都是0。

3. 分配75个 int 元素的数组分配内存。

   ```c
   int *pNumber = (int*) calloc(75,sizeof(int));
   ```


### realloc() 函数扩展内存

```c
int current_element = 0;
int total_element = 128;
char *dynamic_arr = (char *)malloc(total_element);
void add_element(char c)
{
  if(current_element == total_element-1)
  {
    char *p_temp = NULL;    //很关键
    total_element*=2;    //给内存扩容，一般都是直接扩大为2倍
    p_temp = (char *)realloc(dynamic_arr, total_element);
    if(p_temp == NULL) 
    {
      printf("扩展表内存失败！");
      return;
    }
    dynamic_arr = p_temp;
  }
  current_element++;
  dynamic_arr[current_element] = c;
}
```

1. 避免分配大量的小内存块，分配堆上的内存有一些系统开销，所以分配许多小的内存块比分配几个大内存块的系统开销大。
2. 仅在需要时分配内存。只要使用完堆上的内存块，就释放它。
3. 总是确保释放已分配的内存，在编写分配内存的代码时，就要确定在代码的什么地方释放内存。
4. 在释放内存之前，确保不会无意中覆盖堆上已分配的内存的地址，否则程序就会出现内存泄露。在循环中分配内存时，尤其需要注意。
5. **内存泄露** 指由于疏忽或错误造成程序未能释放已经不再使用的内存。

## 程序的结构

### 变量的作用域和生存期

1. 变量只存在于定义它们的块中，他们在声明时创建，在遇到下一个闭括号时就不存在了。

2. 在一个块内的其他块中声明的变量也是这样。

3. 变量在一个块内声明时创建，在这个块结束时销毁，这中变量称为自动变量。

   ```c
   {
       int a = 0; //create a
       // reference to a is ok here
       // reference to b is an error here - it hasn't been created yet
       {
           int b = 10;  // create b
       }
       // refernce to b is an error here - it has been destroyed
       // reference to a is ok here
   }
   ```

4. 希望返回一个能灵活返回指向各种类型的地址时，就可以使用`void *`。

### 按值传递机制

1. 给函数传送变量时，变量值不会直接传递个函数，而是先制作变量的副本，存储在栈上，再时这个副本可用于函数，而不是使用初始值。
2. 给函数传递变量的地址，它只是传递地址的副本，而不是初始的地址。但是，副本仍是一个地址，仍引用最初的变量。

## 函数再探

### 函数指针

1. 函数的内存地址存储了函数开始执行的位置（起始地址），存储在函数指针中的内容就是这个地址。

2. 声明函数指针

   ```c
   //这个指针的名称是 pfunction，指向一个参数是 int 类型、返回值是int类型的函数。
   int (*pfunction) (int)
   ```

### 通过函数指针调用函数

```c
//1. 假定定义如下函数原型
int sum(int a,int b); //calculates a+b
//2. 它的地址存储在声明如下的函数指针中：
int (*pfun)(int,int) = sum;
//3. 通过函数指针调用sum()函数。
int result = pfun(45,55);

//示例
#include <stdio.h>
int sum(int,int);
int product(int,int);
int difference(int,int);
int main()
{
    int a = 10;
    int b = 5;
    int result = 0;
    int (*pfun)(int,int);

    pfun =sum;
    result = pfun(a,b);
    printf("pfun = sum result = %2d\n",result);

    pfun = product;
    result = pfun(a,b);
    printf("pfun = product result = %2d\n",result);

    pfun = difference;
    result = pfun(a,b);
    printf("pfun = difference result = %2d\n",result);
    return 0;
}
int sum(int x,int y)
{
    return x+y;
}
int product(int x,int y)
{
    return x*y;
}
int difference(int x,int y)
{
    return x - y;
}
```

### 函数指针的数组

```c
//声明函数指针数组。
int (*pfunctions[10]) (int);

//示例
#include <stdio.h>
int sum(int,int);
int product(int,int);
int difference(int,int);
int main()
{
    int a = 10;
    int b = 5;
    int result = 0;
    int (*pfun[3])(int,int);
    pfun[0] =sum;
    pfun[1] = product;
    pfun[2] = difference;
    for(int i = 0; i < 3; ++i){
        result = pfun[i](a,b);
        printf("result = %d\n",result);
    }
    result = pfun[1](pfun[0](a,b),pfun[2](a,b));
    printf("result = %2d\n",result);
    return 0;
}
int sum(int x,int y)
{
    return x+y;
}
int product(int x,int y)
{
    return x*y;
}
int difference(int x,int y)
{
    return x - y;
}
```

### 作为变元的函数指针

可以将函数指针作为变量来传递，这样就可以根据指针所指向的函数而调用不同的函数了。

```c
#include <stdio.h>
int sum(int,int);
int product(int,int);
int difference(int,int);
int any_function(int(*pfun)(int,int),int x,int y);
int main()
{
    int a = 10;
    int b = 5;
    int result = 0;
    int (*pf)(int,int) = sum;

    printf("result = %2d\n",any_function(pf,a,b));
    printf("result = %2d\n",any_function(product,a,b));
    printf("result = %2d\n",any_function(difference,a,b));
    return 0;
}
int any_function(int(*pfun)(int,int),int x, int y)
{
    return pfun(x,y);
}
int sum(int x,int y)
{
    return x+y;
}
int product(int x,int y)
{
    return x*y;
}
int difference(int x,int y)
{
    return x - y;
}
```

### 静态变量：函数内部的追踪

