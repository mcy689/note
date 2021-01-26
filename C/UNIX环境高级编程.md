## 基础

```c
#include <stdio.h>
#include <string.h>
#include <errno.h>
#include <stdlib.h>

int main(int argc, char *argv[])
{
    /*
      1. strerror 函数将 errnum （通常就是errno值）映射为一个出错消息字符串，并且返回此字符串的指针。
        #include <string.h>
          char *strerror(int errnum);

      2. perror 函数基于 errno 的当前值，在标准错误上产生一条错误消息，然后返回。
        #include <stdio.h>
          void perror(const char *msg);
    */
    fprintf(stderr,"EACCES: %s\n",strerror(EACCES));
    errno = ENOMEM;
    perror(argv[0]);
    exit(0);
}
```

## 文件 I/O

1. 文件描述符是一个非负整数，当打开一个现有文件或创建一个新文件时，内核向进程返回一个文件描述符。

   *  0 标准输入。
   * 1 标准输出。
   * 2 标准错误。

2. `#include <unistd.h>` 

   * `0 STDIN_FILENO`
   * `1 STDOUT_FILENO` 
   * `2 STDERR_FILENO`

3.  文件描述符的变化范围是 `0 ～ OPEN_MAX-1`

   * `#include <limits.h>`

4. `open` 或 `openat` 函数可以打开或创建一个文件。

   ```c
   #include <fcntl.h>
     int open(const char *path, int oflag, ...));
     int openat(int fd, const char *path, int oflag, ...));
   /*
   oflag:
     O_RDONLY 只读打开
     O_WRONLY 只写打开
     O_RDWR 读、写打开
     O_APPEND 每次写时都追加到文件的末尾。
     O_CREAT 若文件不存在则创建它，使用此选项时，open 函数需要同时说明第 3 个参数 mode（文件权限位）
     O_DIRECTORY 如果 path 引用的不是目录，则出错。
     O_TRUNC 如果此文件存在，而且为只写或读-写成功打开，则将其长度截断为 0。而原来存于该文件的资料也会消失。
   区别：
      1. path 参数指定的是绝对路径名，在这种情况下，fd 参数被忽略， openat 函数就相当于 open 函数。
      2. path 参数指定的是相对路径名，fd 参数指出了相对路径名在文件系统中的开始地址。fd 参数是通过打开相对路径名所在的目录来获取。
      3. path 参数指定来了相对路径名，fd 参数具有特殊值 AT_FDCWD。在这种情况下，路径名在当前工作目录中获取，openat 函数在操作上与 open 函数类似。
   */
   ```

5.  `time-of-check-to-time-of-use` (TOCTTOU):如果有两个基于文件的函数调用，其中第二个调用依赖于第一个调用的结果，那么程序是脆弱的。因为两个调用并不是原子操作，在两个函数调用之间文件可能改变了，这样就造成了第一个调用的结果就不再有效。使得程序最终的结果是错误的。

6. 文件名和路径名截断。

   ```c
   #include <limits.h>
     //NAME_MAX 表示在当前系统中创建一个文件名的最大字符长度。
     //PATH_MAX 表示路径名最大的长度。
   ```

7. 函数 creat，创建一个新文件。

   ```c
   #include <fcntl.h>
     int creat(const char *path, mode_t mode);
                   //返回值：若成功，返回为只写打开的文件描述符；若出错，返回-1；
      //此函数等效于 open(path, O_WRONLY | O_CREAT | O_TRUNC, mode);
   ```

8. close函数，关闭一个打开文件。

   ```c
   #include <unistd.h>
     int close(int fd);
            //返回值：若成功，返回0；若出错返回 -1；
     //关闭一个文件时还会释放该进程加在该文件上的所有记录锁。
   ```

9. 函数 `lseek`，显式地为一个打开文件设置偏移量。每个打开文件都有一个与其相关联的“当前文件偏移量”。它通常是一个非负整数，用以度量从文件开始处计算的字节数。**按系统默认的情况，当打开一个文件时，除非指定了 `O_APPEND` 选项，否则该偏移量被设置为 0** 。

   ```c
    #include <unistd.h>
      off_t lseek(int fd, off_t offset, int whence);
    					//返回值：若成功，返回新的文件偏移量；若出错，返回为-1。
      //对参数 offset 的解释于参数 whence 的值有关。
           //若 whence 是 SEEK_SET, 则将该文件的偏移量设置为距文件开始处 offset 个字节。
           //若 whence 是 SEEK_CUR，则将该文件的偏移量设置为其当前值加 offset，offset 可为正或负。
           //若 whence 是 SEEK_END，则将该文件的偏移量设置为文件长度加 offset，offset 可正可负。
   	 //确定打开文件的当前偏移量
           //off_t currpos;
           //currpos = lseek(fd, 0 , SEEK_CUR);
   
   //例子1. 测试标准输入是否可以设置偏移量
   #include <stdio.h>
   #include <unistd.h>
   #include <stdlib.h>
   int main()
   {
       if (lseek(STDIN_FILENO,0,SEEK_CUR) == -1) {
           printf("cannot seek \n");
       } else {
           printf("seek ok \n");
       }
       exit(0);
   }
   ```

   