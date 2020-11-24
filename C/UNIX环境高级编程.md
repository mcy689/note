## 文件 I/O

### 文件描述符

1. 文件描述符是一个非负整数。当打开一个现有文件或创建一个新文件时，内核向进程返回一个文件描述符。当读、写一个文件时，使用 open 或 creat 返回的文件描述符标识该文件，将其作为参数传送给 read 或 write。

2. 文件描述符 0 与进程的标准输入关联，文件描述符 1 与标准输出关联，文件描述符 2 与标准错误关联。

   ```c
   #include <unistd.h> //头文件 <unistd.h> 定义了
   
   #define	 STDIN_FILENO	0	/* standard input file descriptor */
   #define	STDOUT_FILENO	1	/* standard output file descriptor */
   #define	STDERR_FILENO	2	/* standard error file descriptor */
   ```

###`open` 和 `openat`

```c
/*
 文件目录
  1.txt
  hello.c
  change
    2.txt
*/
#include <stdio.h>
#include <fcntl.h>

int main()
{
    int fd_file = open("./1.txt",O_RDONLY); //3
    int fd_dir = open("./change",O_DIRECTORY); //4
    int fd_file_2 = openat(fd_dir,"2.txt",O_RDONLY);//基于打开目录的相对路径打开文件
    printf("%d\n\%d\n\%d\n",fd_file,fd_dir,fd_file_2); //3
}
```

### close

1. 关闭一个文件时还会释放改进程加在该文件上的所有记录锁。
2. 当一个进程终止时，内核自动关闭它所有的打开文件。

```c
#include <unistd.h>

int close(int fd);
```

### `lseek`

1. 每个打开文件都有一个与其相关联的“当前文件偏移量”。用以度量文件开始处计算的字节数。
2. 通常，读、写操作都从当前文件偏移量处开始，并使偏移量增加所读写的字节数。
3. 按系统默认的情况，当打开一个文件时，除非指定`O_APPEND` 选项，否则该偏移量被设置为0。
4. `lseek` 显式地为一个打开文件设置偏移量。仅将当前的文件偏移量记录在内核中，它并不引起任何 `I/O` 操作。
5. 文件偏移量可以大于文件的当前长度，在这种情况下，对该文件的下一次写将加长该文件，并在文件中构成一个空洞。位于文件中但没有写过的字节都被读为0。

```c
/*
	#include <unistd.h>
	off_t lseek(int fd, off_t offset, int whence);
                                               错误返回 -1
   1. whence 是 SEEK_SET，则将该文件的偏移量设置为距文件开始处 offset 个字节。
   2. whence 是 SEEK_CUR，则将该文件的偏移量设置为其当前值加 offset，offset 可为正或负。
   3. whence 是 SEEK_END，则将该文件的偏移量设置为文件长度 offset，offset 可正可负。
*/
#include <stdio.h>
#include <unistd.h>
#include <fcntl.h>

int main()
{
    int fd_file = open("./1.txt",O_RDONLY); //3
    int seek = lseek(fd_file,0,SEEK_CUR);
    printf("%d\n",seek);
}
```

### pread 和 pwrite

1. UNIX 系统提供了原子操作。即在打开文件时设置为 `O_APPEND` 标志。这样做使得内核在每次写操作之前，都将进程的当前偏移量设置到该文件的尾端处。

```c
/*
  允许原子性定位并执行 `I/O`。
  #include <unistd.h>
  ssize_t pread(int fd,void *buf,size_t nbytes, off_t offset);
                                      //返回值：读到的字节数，若已到文件尾，返回0；若出错，返回-1
  ssize_t pwrite(int fd, const void *buf, size_t nbytes, off_t offset);
                                      //返回值：若成功，返回已写的字节数；若出错，返回-1
*/
#include <unistd.h>
#include <fcntl.h>
#include <stdio.h>

int main(void)
{
    char str[11];
    int fd_file = open("./1.txt",O_RDONLY);
    unsigned int a = pread(fd_file,str,10,0);
    printf("%d\n%s\n%d\n",a,str,fd_file);
}
```

### dup 和 dup2

都可用来复制一个现有的文件描述符。

```c
#include <unistd.h>

int dup(int fd);
int dup2(int fd, int fd2);
```

### sync、fsync 和 fdatasync

1. 传统的UNIX系统实现在内核中设有缓冲区或页高速缓存，大多数磁盘`I/O` 都通过缓冲区进行。当向文件写入数据时，内核通常先将数据复制到缓存区中，然后排入队列，晚些时候再写入磁盘。这种方式被称为 **延时写**。

2. 为了保证磁盘上实际文件系统与缓冲区中内容的一致性，提供了下面三个函数。

```c
#include <unistd.h>

int fsync(int fd);
int fdatasync(int fd);

void sync(void);
```

## 文件和目录

### stat、fstat、fstatat 和 lstat

```c
#include <sys/stat.h>

int stat(const char *restrict pathname, struct stat *restrict buf);
int fstat(int fd, struct stat *buf);
int lstat(const chat *restrict pathname, struct stat *restrict buf);
int fstatat(int fd, const char *restrict pathname, struct stat *restrict buf, int flag);
```

