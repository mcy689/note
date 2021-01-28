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

### `open` 或 `openat`

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

###`time-of-check-to-time-of-use` (TOCTTOU)

如果有两个基于文件的函数调用，其中第二个调用依赖于第一个调用的结果，那么程序是脆弱的。因为两个调用并不是原子操作，在两个函数调用之间文件可能改变了，这样就造成了第一个调用的结果就不再有效。使得程序最终的结果是错误的。

### 文件名和路径名截断。

```c
#include <limits.h>
  //NAME_MAX 表示在当前系统中创建一个文件名的最大字符长度。
  //PATH_MAX 表示路径名最大的长度。
```

### 函数 `creat`

```c
#include <fcntl.h>
  int creat(const char *path, mode_t mode);
                //返回值：若成功，返回为只写打开的文件描述符；若出错，返回-1；
   //此函数等效于 open(path, O_WRONLY | O_CREAT | O_TRUNC, mode);
```

### 函数 `close`

```c
#include <unistd.h>
  int close(int fd);
         //返回值：若成功，返回0；若出错返回 -1；
  //关闭一个文件时还会释放该进程加在该文件上的所有记录锁。
```

### 函数 `lseek`

函数 `lseek`，显式地为一个打开文件设置偏移量。每个打开文件都有一个与其相关联的“当前文件偏移量”。它通常是一个非负整数，用以度量从文件开始处计算的字节数。**按系统默认的情况，当打开一个文件时，除非指定了 `O_APPEND` 选项，否则该偏移量被设置为 0** 。

```c
#include <unistd.h>
   off_t lseek(int fd, off_t offset, int whence);
 					//返回值：若成功，返回新的文件偏移量；若出错，返回为-1。
   /*
    1. 对参数 offset 的解释于参数 whence 的值有关。
        若 whence 是 SEEK_SET, 则将该文件的偏移量设置为距文件开始处 offset 个字节。
        若 whence 是 SEEK_CUR，则将该文件的偏移量设置为其当前值加 offset，offset 可为正或负。
        若 whence 是 SEEK_END，则将该文件的偏移量设置为文件长度加 offset，offset 可正可负。
	  2. 确定打开文件的当前偏移量
        off_t currpos;
        currpos = lseek(fd, 0 , SEEK_CUR);
    3. lseek 仅将当前的文件偏移量记录在内核中，它并不引起任何I/O操作。然后，该偏移量用于下一个读或写操作。
    4. 文件偏移量可以大于文件的当前长度，在这种情况下对该文件的下一次写将加长才文件并在文件中构成一个空洞。位于文件中但没有写过的字节都被读为0。
    5. 文件中的空洞并不要求在磁盘上占用存储区。具体处理方式与文件系统的实现有关，当定位到超出文件尾端之后写时，对于新写的数据需要分配磁盘块，但是对于原文件尾端和新开始写位置之间的部分则不需要分配磁盘块。
   */

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

//例子2. 创建一个具有空洞的文件。
#include <stdlib.h>
#include <stdio.h>
#include <unistd.h>
#include <fcntl.h>

#define FILE_MODE (S_IRUSR | S_IWUSR | S_IRGRP | S_IROTH)

char buf1[] = "abcdefghij";
char buf2[] = "ABCDEFGHIJ";

int main()
{
    int fd;
    if ((fd = creat("file.hole", FILE_MODE)) < 0){
        printf("creat error");
        exit(0);
    }
    if (write(fd, buf1, 10) != 10) {
        printf("creat error");
        exit(0);
    }
    if (lseek(fd, 16384, SEEK_SET) == -1) {
        printf("lseek error");
        exit(0);
    }
    if (write(fd, buf2, 10) != 10){
        printf("buf2 write error");
    }
    exit(0);
}
```

### 函数 `read`

```c
#include <unistd.h>

  ssize_t read(int fd, void *buf, size_t nbytes);
       //返回值：读到的字节数，若已到文件尾，返回0；若出错，返回-1；
  /*
   有多种情况可使实际读到的字节数少于要求读的字节数。
      1. 读普通文件时，在读到要求字节数之前已到达了文件尾端。
      2. 当从终端设备读时，通常一次最多读一行。
      3. 当从网络读时，网络中的缓冲机制可能造成返回值小于所要求读的字节数。
      4. 当从管道或FIFO读时，如若管道包含的字节少于所需的数量，那么 read 将只返回实际可用的字节数。
      5. 当从某些面向记录的设备（如磁带）读时，一次最多返回一个记录。
      6. 当一信号造成中断，而已经读了部分数据量时。
  */
```

### 函数 `write`

```c
#include <unistd.h>
  ssize_t write(int fd, const void *buf, size_t nbytes);
       //返回值：若成功，返回已写的字节数；若出错，返回-1
  //对于普通文件，写操作从文件的当前偏移量处开始。如果在打开该文件时，指定了 O_APPEND 选项，则在每次写操作之前，将文件偏移量设置在文件的当前结尾处。在一次成功写之后，该文件偏移量增加实际写的字节数。
```

### 文件共享

内核使用3种数据结构表示打开文件，它们之间的关系决定了在文件共享方面一个进程对另一个进程可能产生的影响。

1. **描述符表** 。每个进程都有它独立的描述符表，它的表项是由进程打开的文件描述符来索引的。每个打开的描述符表项指向文件表中的一个表项。

2. **文件表** 。打开文件的集合是由一张文件表来表示的，**所有的进程共享这张表** 。

   * 文件状态标志（读、写等）。
   * 当前文件偏移量。
   * 指向该文件v节点表项的指针。

3. `v-node`  表。同文件表一样，**所有的进程共享这张 `v-node` 表** 。每个表项包含 stat 结构中的大多数信息。

   <img src="./image/unix-共享文件.png" alt="unix-共享文件"  />

   ​	<img src="./image/unix-打开同一个文件.png" alt="unix-打开同一个文件"  />

### 函数 `dup` 和 `dup2`

```c
#include <unistd.h>
  int dup(int fd);
  int dup2(int fd, int fd2);
        //两个函数的返回值：若成功，返回新的文件描述符；若出错，返回-1
  /*
   1. dup 返回的新文件描述符一定是当前可用文件描述符中的最小数值。
   2. dup2 可以用 fd2 参数指定新描述符的值。如果 fd2 已经打开，则先将其关闭。如若 fd 等于 fd2，则     dup2 返回 fd2，而不关闭它。
  */
```

<img src="./image/unix-dup.png" alt="unix-dup"  />

### 函数 `sync`、`fsync` 和 `fdatasync`

传统的 UNIX 系统实现在内核中设有缓冲区高速缓存或页高速缓存，大多数磁盘 I/O 都通过缓存区进行。当我们向文件写入数据时，内核通常先将数据复制到缓冲区中，然后排入队列，晚些时候再写入磁盘。这种方式被称为**延迟写** 。

```c
#include <unistd.h>

  int fsync(int fd);
  int fdatasync(int fd);
        //若成功，返回0；若出错，返回-1
  void sync(void);
/*
    1. sync 只是将所有修改过的块缓存排入写队列，然后就返回，它并不等待实际写磁盘操作结束。通常，称为 update 的系统守护进程周期性地调用 sync 函数。这就保证了定期冲洗内核的块缓存。
    2. fsync 函数只对由文件描述符fd指定的一个文件起作用，并且等待写磁盘操作结束才返回。
    3. fdatasync 函数类似于 fsync，但它只影响文件的数据部分。而除数据外，fsync 还会同步更新文件的属性。
*/
```

## 文件和目录

### 函数 `stat`、`fstat` 、`fstatat`和`lstat` 

```c
#include <sys/stat.h>

  int stat(const char *restrict pathname, struct stat *restrict buf);
  int fstat(int fd, struct stat *buf);
  int lstat(const chat *restrict pathname, struct stat *restrict buf);
  int fstatat(int fd, const char *restrict pathname, struct stat *restrict buf, int flag);
        //所有4个函数的返回值：若成功；返回0；若出错，返回-1
/*
  1. stat 函数将返回此命名文件有关的信息结构。
  2. fstat 函数获取得已在描述符 fd 上打开文件的有关信息。
  3. lstat 函数类似于 stat，但是当命名的文件是一个符号链接时，lstat 返回该符号链接的有关信息，而不是由该符号链接引用的文件的信息。
  4. fstat 函数为一个相对于当前打开目录（由 fd 参数指向）的路径名返回文件统计信息。
     flag 参数：
       AT_SYMLINK_NOFOLLOW 标志被设置时，fstatat 不会跟随符号链接，而是返回符号链接本身信息。
       否则，在默认情况下，返回的是符号链接所指向的实际文件的信息。
     fd 参数：
       当 fd 参数的值为 AT_FDCWD ，并且 pathname 参数是一个相对路径名，fstatat会计算相对于当前目录的pathname 参数。如果pathname 是一个绝对路径，fd 参数就会被忽略。
*/
```

### 文件类型

1. **普通文件** 。
2. **目录文件** 。这种文件包含了其他文件的名字以及指向与这些文件有关信息的指针。**对一个目录文件具有读权限的任一进程都可以读该目录的内容** 。
3. **块特殊文件** 。这种类型的文件提供对设备（如磁盘）带缓冲的访问，每次访问以固定长度为单位进行。
4. **字符特殊文件** 。这种类型的文件提供对河北不缓存的访问，每次访问长度可变。系统中的所有设备要么是字符特殊文件，要么是块特殊文件。
5. **FIFO** 。这种类型的文件用于进程间通信，有时也称为命名管道。
6. **套接字** 。这种类型的文件用于进程间的网络通信。套接字也可用于在一台宿主机上进程之间的非网络通信。
7. **符号链接** 。这种类型的文件指向另一个文件。

```c
/*
  stat 结构中的 st_mode
    S_ISREG()   普通文件
    S_ISDIR()   目录文件
    S_ISCHR()   字符特殊文件
    S_ISBLK()   块特殊文件
    S_ISFIFO()  管道或FIFO
    S_ISLNK()   符号链接
    S_ISSOCK()  套接字
  stat 结构
    S_TYPEISMQ()   消息队列
    S_TYPEISSEM()  信号量
    S_TYPEISSHM()  共享存储对象
*/

//eg
#include <stdio.h>
#include <sys/stat.h>
#include <stdlib.h>

int main(int argc, char *argv[])
{
    int i;
    struct stat buf;
    char *ptr;
    for (i = 1; i < argc; i++) {
        if (lstat(argv[i], &buf) < 0) {
            printf("lstat error");
            continue;
        }
    }
    if (S_ISREG(buf.st_mode)){
        ptr = "regular";
    } else if (S_ISDIR(buf.st_mode)) {
        ptr = "directory";
    } else if (S_ISCHR(buf.st_mode)) {
        ptr = "character special";
    } else if (S_ISBLK(buf.st_mode)) {
        ptr = "block special";
    } else if (S_ISFIFO(buf.st_mode)) {
        ptr = "fifo"
    } else if (S_ISLNK(buf.st_mode)) {
        ptr = "symbolic link";
    } else if (S_ISSOCK(buf.st_mode)) {
        ptr = "socket";
    } else {
        ptr = "unknown mode";
    }
    printf("%s\n",ptr);
    exit(0);
}
```

### 设置用户ID和设置组ID。

1. 实际用户ID和实际组ID标识我们究竟是谁。这两个字段在登录时取自口令文件中的登录项。

2. 有效用户ID、有效组ID以及附属组ID决定了我们的文件访问权限。

3. 保持的设置用户ID和保持的设置组ID在执行一个程序时包含了有效用户ID和有效组ID的副本。

### 文件访问权限

st_mode 值也包含了对文件的访问权限位。

```c
  S_IRWXU  //读写执行
  S_IRUSR  //用户读
  S_IWUSR  //用户写
  S_IXUSR  //用户执行
 
  S_IRWXG
  S_IRGRP
  S_IWGRP
  S_IXGRP

  S_IRWXO 
  S_IROTH
  S_IWOTH
  S_IXOTH
    
  S_ISUID //执行时设置用户ID
  S_ISGID //执行时设置组ID
  S_ISVTX //保存正文（粘着位）
```

1. 对于目录的读权限和执行权限的意义是不相同的。读权限允许我们读目录，获得在该目录中所有文件名的列表。当一个目录是我们要访问文件的路径名的一个组成部分时，对该目录的执行权限使我们可通过该目录（也就是搜索该目录，寻找一个特定的文件名）。

   例如，为了打开文件 `/usr/include/stdio.h` ，需要对目录 `/`，`/usr`，`/usr/include` 具有执行权限。然后，需要具有对文件本身的适当权限。

2. 对于一个文件的读权限决定了我们是否能够打开现有文件进行读操作。

3. 对于一个文件的写权限决定了我们是否能够打开现有文件进行写操作。

4. 为了在一个目录中创建一个新文件，必须对该目录具有写权限和执行权限。

5. 为了删除一个现有文件，必须对包含该文件的目录具有写权限和执行权限。对该文件本身则不需要有读、写权限。

6. 如果用 7 个 exec 函数中的任何一个执行某个文件，都必须对该文件具有执行权限。该文件还必须是一个普通文件。

### 新文件和目录的所有权

1. 新文件的用户ID设置为进程的有效用户ID。
2. 关于组ID允许实现选择下列之一作为新文件的组ID：
   * 新文件的组ID可以是进程的有效组ID。
   * 新文件的组ID可以是它所在目录的组ID。

### 函数 access 和 faccessat

```c
#include <unistd.h>
  int access(const char *pathname, int mode);
  int faccessat(int fd, const char *pathname, int mode, int flag);
        //两个函数的返回值：若成功，返回0；若出错，返回-1
  /*
  1. 按照实际用户ID和实际组ID进行访问权限测试的。
  2. mode 参数
       F_OK 测试文件是否存在
       R_OK 测试读权限
       W_OK 测试写权限
       X_OK 测试执行权限
   3. flag 设置为 AT_EACCESS，访问检查用的是调用进程的有效用户ID和有效组ID，而不是实际用户ID和实际组ID。
  */

//例子
#include <unistd.h>
#include <stdio.h>
#include <stdlib.h>
#include <fcntl.h>

int main(int argc, char *argv[])
{
    if (argc != 2) {
        printf("usage:a.out <pathname>\n");
        exit(EXIT_FAILURE);
    }
    if (access(argv[1],R_OK) < 0) {
        printf("access error for %s\n",argv[1]);
        exit(EXIT_FAILURE);
    } else {
         printf("read access Ok\n");
    }
    if (open(argv[1],O_RDONLY) < 0) {
        printf("open error for %s\n",argv[1]);
        exit(EXIT_FAILURE);
    } else {
         printf("open for reading Ok\n");
    }
    exit(EXIT_SUCCESS);
}
```

### 函数 umask

`umask` 函数为进程设置文件模式创建屏蔽字，并返回之前的值。

命令行查看`umask -S` 格式化的屏蔽字。

```c
#include <sys/stat.h>
  mode_t umask(mode_t cmask);
        //返回值：之前的文件模式创建屏蔽字

//eg
#include <sys/stat.h>
#include <stdio.h>
#include <stdio.h>
#include <unistd.h>
#include <stdlib.h>
#include <fcntl.h>

#define RWRWRW (S_IRUSR|S_IWUSR|S_IRGRP|S_IWGRP|S_IROTH|S_IWOTH)

int main(void)
{
    umask(0);
    if (creat("foo",RWRWRW) < 0) {
        printf("creat error for foo");
        exit(EXIT_FAILURE);
    }
    umask(S_IRUSR|S_IWUSR);
    if (creat("bar",RWRWRW) < 0) {
        printf("creat error for bar");
        exit(EXIT_FAILURE);
    }
    exit(EXIT_SUCCESS);
}
/*
  machunyudeMacBook-Pro:example machunyu$ ls -l foo bar 
  ----rw-rw-  1 machunyu  staff  0  1 27 11:29 bar
  -rw-rw-rw-  1 machunyu  staff  0  1 27 11:29 foo
*/
```

### 函数 chmod 、fchmod 和 fchmodat

为了改变一个文件的权限位，进程的有效用户ID必须等于文件的所有者ID。或者该进程必须具有超级用户权限。

```c
#include <sys/stat.h>
  int chmod(const char *pathname, mode_t mode);
  int fchmod(int fd, mode_t mode);
  int fchmodat(int fd, const char *pathname, mode_t mode, int flag);
        //3个函数返回值：若成功，返回0；若出错，返回-1
/*
 1. chmod 函数在指定的文件上进行操作。
 2. fchmod 函数则对已打开的文件进行操作。
 3. fchmodat 当设置了 AT_SYMLINK_NOFOLLOW 标志时，fchmodat 并不会跟随符号链接。
*/

//eg
#include <stdio.h>
#include <stdlib.h>
#include <sys/stat.h>
#include <unistd.h>

int main()
{
    struct stat buf;
    if (stat("bar",&buf) < 0) {
        printf("stat error for bar");
        exit(EXIT_FAILURE);
    }
    if (chmod("bar",(buf.st_mode | S_IRWXG)) < 0) {
        printf("chmod error for bar");
        exit(EXIT_FAILURE);
    }
    exit(EXIT_SUCCESS);
}
```

### 粘着位

`S_ISVTX`  允许针对目录设置粘着位。如果对一个目录设置了粘着位，只有对该目录具有写权限的用户并且满足下列条件之一，才能删除或重命名该目录下的文件。

* 拥有此文件
* 拥有此目录
* 是超级用户

`/tmp` 和 `/var/tmp` 是设置粘着位的典型候选者。任何用户都可在这两个目录中创建文件，任一用户对这两个目录的权限通常都是读、写和执行。但是用户不应能删除或重命名属于其他人的文件，为此在这两个目录的文件模式中都设置了粘着位。

### 函数 chown、fchown、fchownat 和 lchown

只有超级用户才能更改一个文件的所有者。

```c
#include <unistd.h>

  int chown(const char *pathname, uid_t owner, gid_t group);
  int fchown(int fd, uid_t owner, gid_t group);
  int fchownat(int fd, const char *pathname, uid_t owner, gid_t group, int flag);
  int lchown(const char *pathname, uid_t owner, gid_t group);
          //4个函数的返回值：若成功，返回0；若出错，返回-1
```

### 文件截断

```c
#include <unistd.h>
  int truncate(const char *pathname, off_t length);
  int ftruncate(int fd, off_t length);
          //两个函数的返回值：若成功，返回0；若出错，返回-1
  //这两个函数将一个现有文件长度截断位 length。
```

### 函数 link、linkat、unlink、unlinkat 和 remove

```c
#include <unistd.h>
  int link(const char *existingpath, const char *newpath);
  int linkat(int efd, const char *existingpath, int nfd, const char *newpath, int flag);
          //两个函数的返回值：若成功，返回0；若出错，返回-1

  int unlink(const char *pathname);
  int unlinkat(int fd, const char *pathname, int flag);
          //两个函数的返回值：若成功，返回0；若出错，返回-1
  /*
   只有当链接计数达到0时，该文件的内容才可能被删除。另一个条件也会阻止删除文件的内容：只要有进程打开了该文件，其内容也不能删除。关闭一个文件时，内核首先检查打开该文件的进程个数；如果这个计数达到0，内核再去检查其链接计数；如果计数也是0，那么就删除该文件的内容。
  */

#include <stdio.h>
  int remove(const char *pathname);
          //返回值：若成功，返回0；若出错，返回-1
```

### 函数 rename 和 renameat

```c
#include <stdio.h>
  int rename(const char *oldname, const char *newname);
  int renameat(int oldfd, const char *oldname, int newfd, const char *newname);
            //两个函数的返回值：若成功，返回0；若出错，返回-1
```

## 标准I/O库

### 流和FILE对象

标准 I/O 文件流可用于单字节或多字节字符集。**流的定向** 决定了所读、写的字符是单字节还是多字节。当一个流最初被创建时，它并没有定向。如若在未定向的流上使用一个多字节 I/O 函数，则将该流的定向设置为宽定向的。若在未定向的流上使用一个单字节 I/O 函数，则将该流的定向设为字节定向的。

```c
#include <stdio.h>
#include <wchar.h>

  int fwide(FIFE *fp,int mode);
        //返回值：若流是宽定向的，返回正值，若流是字节定向的，返回负值；若流是未定向的，返回0。
  /*
  mode 参数
    1. 如若 mode 参数值为负，fwide 将试图使指定的流是字节定向的。
    2. 如若 mode 参数值为正，fwide 将试图使指定的流是宽定向的。
    3. 如若 mode 参数值为0，fwide 将不试图设置流的定向，但返回标识该流定向的值。
    
  并不改变已定向流的定向。
  */
```

### 标准输入、输出、错误

```c
#include <stdio.h>
  stdin
  stdout
  stderr
```

### 缓冲

标准I/O 提供了以下3中类型的缓冲。

1. 全缓冲。在这种情况下，在填满标准 I/O 缓冲区后才进行实际 I/O 操作，对于驻留在磁盘上的文件通常是由标准 I/O 库实施全缓冲的。在一个流上执行第一次 I/O 操作时，相关标准 I/O 函数通常调用 malloc 获取需要使用的缓冲区。
2. 行缓冲。在这种情况下，当在输入和输出中遇到换行符时，标准 I/O 库执行 I/O 操作。这允许我们一次输出一个字符，但只有在写了一行之后才进行实际 I/O 操作。当流涉及一个终端时，通常使用行缓冲。
   * 固定长度。
   * 换行符。
3. 不带缓冲。标准 I/O 库不对字符进行缓冲存储。
   * 标准错误流 stderr 通常是不带缓冲的。

ISO C 要求下列缓冲特征。

1. 当且仅当标准输入和标准输出并不指向交互式设备时，它们才是全缓冲的。
2. 标准错误绝不会是全缓冲的。
3. 对于指向交互式设备时作一般系统默认
   * 标准错误时不带缓冲的。
   * 若是指向终端设备的流，则是行缓冲的；否则是全缓冲的。

```c
//更改系统默认的缓冲类型
#include <stdio.h>
  void setbuf(FILE *restrict fp, char *restrict buf);
  int setvbuf(FILE *restrict fp, char *restrict buf, int mode, size_t size);
       //返回值：若成功，返回0，若出错，返回非0
/*
  1. setbuf 函数打开或关闭缓冲机制，为了带缓冲I/O，参数 buf 必须指向一个长度为 BUFSIZ 的缓冲区。通常在此之后该流就是全缓冲的，但是如果该流与一个终端设备相关，那么某些系统也可以将其设置为行缓冲的。关闭缓冲区为NULL。
  2. setvbuf 函数中 mode 参数
     _IOFBF 全缓冲
     _IOLBF 行缓冲
     _IONBF 不带缓存
  3. setvbuf 函数，如果指定一个不带缓冲的流，则忽略 buf 和 size 参数，如果指定全缓冲或行缓冲，则 buf 和 size 可选择地指定一个缓冲区及其长度。如果该流是带缓冲的，而 buf 是NULL，则标准 I/O 库将自动地为该流分配适当长度的缓冲区。适当长度指的是又常量 BUFSIZ 所指定的值。
  4. 如果在一个函数内分配一个局部变量类型的标准 I/O 缓冲区，则从该函数返回之前，必须关闭该流。另外，某些实现将缓冲区的一部分用于存放它自己的管理操作信息，所以可以存放在缓冲区中的实际数据字节数少于 size。一般而言，应由系统选择缓冲区的长度，并自动分配缓冲区。在这种情况下关闭此流时，标准 I/O 库将自动释放缓冲区。
*/

//强制冲洗一个流
#include <stdio.h>
  int fflush(FILE *fp);
       //返回值：若成功，返货0，若出错，返回EOF
  /*
    如果 fp 是 NULL，则此函数将导致所有输出流被冲洗。
  */
```

### 打开流

```c
#include <stdio.h>

  FILE *fopen(const char *restrict pathname, const char *restrict type);
  FILE *freopen(const char *restrict pathname, const char *restrict type, FILE *restrict fp);
  FILE *fdopen(int fd, const char *type);
            //3个函数的返回值，若成功，返回文件指针；若出错，返回NULL。
  /*
   1. fopen 函数打开路径名为 pathname 的一个指定的文件。
   2. freopen 函数在一个指定的流上打开一个指定的文件，如若该流已经打开，则先关闭该流。若该流已经定向，则使用 freopen 清除该定向。此函数一般用于将一个指定的文件打开为一个预定义的流：标准输入、标注输出和标准错误。
   3. fdopen 函数取一个已有的文件描述符，并使一个标准的I/O流与该描述符相结合。此函数常用于由创建管道和网络通信管道函数返回的描述符。因为这些特殊类型的文件不能用标准 I/O 函数 fopen 打开。
   4. type 类型
     r 读
     w 把文件截断至0长，或为写而创建
     a 追加
     r+ 读写打开
     w+ 把文件截断至0长，或为读和写而打开
     a+ 为在文件尾读和写而打开
  */

//eg. freopen 函数
#include <stdio.h>
int main()
{
    FILE *fp;
    printf("该文本重定向到 stdout\n");
    fp = freopen("file.txt","w+",stdout);
    printf("该文本重定向到 file.txt\n");//写入到 stdout，然后重定向到文件中。
    fclose(fp);
    return 0;
}
```

### 关闭流

```c
#include <stdio.h>
  int fclose(FILE *fp);
            //返回值：若成功，返回0；若出错，返回EOF
/*
  1. 在该文件被关闭之前，冲洗缓冲中的输出数据，缓冲区中的任何输入数据被丢弃。如果标准 I/O 库已经为该流自动分配了一个缓冲区，则释放此缓冲区。
  2. 当一个进程正常终止时（直接调用 exit，或从 main 返回），则所有带未写缓冲数据的标准 I/O 流都被冲洗，所有打开的标准I/O流都被关闭。
*/
```

### 读写流

```c
//输入函数，以下3个函数可用于一次读一个字符
  #include <stdio.h>
    int getc(FILE *fp);
    int fgetc(FILE *fp);
    int getchar(void);
              //3个函数的返回值：若成功，返回下一个字符，若已到达文件尾端或出错，返回EOF。
    //getchar 等同于 getc(stdin); 

//判断是否成功
  #include <stdio.h>
    int ferror(FILE *fp);
    int feof(FILE *fp);
              //两个函数返回值：若条件为真，返回非0，否则返回0
    void clearerr(FILE *fp);
    /*
      在大多数实现中，为每个流在FILE对象中维护了两个标志：
        出错标志
        文件结束标志
      调用 clearerr 可以清除这两个标志。
    */

//将字符再压送回流中。
  #include <stdio.h>
    int ungetc(int c, FILE *fp);
              //返回值：若成功，返回c；若出错，返回 EOF

//写函数
  #include <stdio.h>
    int putc(int c, FILE *fp);
    int fputc(int c, FILE *fp);
    int putchar(int c);
              //3个函数返回值：若成功；返回c；若出错，返回EOF
    //putchar(c) 等同于 putc(c, stdout);
```

### 每次一行I/O

```c
#include <stdio.h>
  char *fgets(char *restrict buf, int n,FILE *restrict fp);
  char *gets(char *buf);//（不推荐使用）
              //两个函数返回值：若成功，返回buf；若已到达文件尾端或出错，返回NULL。
/*
  fgets，
    必须指定缓冲的长度n，此函数一直读到下一个换行符为止，但是不超过 n-1 个字符，读入的字符被送入缓冲区。
    该缓冲区以 null 字节结尾。
    若该行包括最后一个换行符的字符数超过 n-1，则 fgets 只返回一个不完整的行。但是，缓冲区总是以 null 字节结尾。对 fgets 的下次调用回继续该行读。
    
*/

//fgets
  #include <stdio.h>

  int main()
  {
      char str[30];
      printf("输入一个字符串：");
      fgets(str,7,stdin);
      printf("%s\n",str);
      fgets(str,7,stdin);
      printf("%s\n",str);
      return 0;
  }
/*
  machunyudeMacBook-Pro:example machunyu$ ./5_3_prit 
  输入一个字符串：i love you
  i love
   you
*/

#include <stdio.h>
  int fputs(const char *restrict str, FILLE *restrict fp);
  int puts(const char *str);
              //两个函数返回值：若成功，返回非负值，若出错，返回EOF。
/*
  区别
    1. puts() 只能向标准输出流输出，而 fputs() 可以向任何流输出。
    2. 使用 puts() 时，系统会在自动在其后添加换行符；而使用 fputs()时，系统不会自动添加换行符号。
*/

//eg。复制输入流到输出流
#include <stdio.h>
#include <stdlib.h>

#define MAXLINE 30

int main()
{
    char buf[MAXLINE];
    while (fgets(buf, MAXLINE, stdin) != NULL) {
        if (fputs(buf, stdout) == EOF) {
            printf("output error");
            exit(EXIT_FAILURE);
        }
    }
    if (ferror(stdin)) {
        printf("input error");
        exit(EXIT_FAILURE);
    }
    exit(EXIT_SUCCESS);
}
```

### 二进制I/O

```c
#include <stdio.h>
  size_t fread(void *restrict ptr, size_t size, size_t nobj, FILE *restrict fp);
  size_t fwrite(const void *restrict ptr, size_t size, size_t nobj, FILE *restrict fp);
              //两个函数的返回值：读或写的对象数
  //buffer为接收数据的地址，size为一个单元的大小，count为单元个数，stream为文件流。

/*
 eg：用法
   1. 读或写一个二进制数组。例如，为了将一个浮点数组的第 2～5 个元素写至一个文件上。
    float data[10];
    if (fwrite(&data[2],sizeof(float),4,fp) != 4) {
      err_sys("fwrite error");
    }
   2. 读或写一个结构
     struct {
       short count;
       long total;
       char name[NAMESIZE];
     } item;
     if (fwrite(&item,sizeof(item),1,fp) != 1) {
       err_sys("fwrite error");
     }
*/ 
```

### 定位流

对于一个二进制流文件，其文件位置指示器是从文件起始位置开始度量，并以**字节为度量单位** 的。

对于文本文件，它们的文件当前位置可能不以简单的字节偏移量来度量。

```c
#include <stdio.h>
  long ftell(FILE *fp);
              //返回值：若成功，返回当前文件位置指示；若出错，返回 -1L
  int fseek(FILE *fp, long offset, int whence);
              //返回值：若成功，返回0；若出错，返回-1
  void rewind(FILE *fp);
/*
  whence
    SEEK_SET 表示文件的开始位置
    SEEK_CUR 表示从当前文件位置开始
    SEEK_END 表示从文件尾端开始

  rewind 函数可以将一个流设置到文件的起始位置
*/

  off_t ftello(FILE *fp);
              //返回值：若成功，返回当前文件位置；若出错，返回 (off_t)-1
  int fseeko(FILE *fp, off_t offset, int whence);
              //返回值：若成功，返回0；若出错，返回-1

  int fgetpos(FILE *restrict fp, fpos_t *restrict pos);
  int fsetpos(FILE *fp, const fpos_t *pos);
              //两个函数返回值：若成功，返回0；若出错，返回非0
  /*
    fgetpos 将文件位置指示器的当前值存入由pos指向的对象中。以后调用 fsetpos 时，可以使用此值将流重新定位至该位置。
  */

//eg:获取文件总长度
#include <stdio.h>

int main()
{
    FILE *fp;
    long len;

    fp = fopen("file.txt","r");
    if (fp == NULL) {
        perror("打开文件错误");
        return -1;
    }
    fseek(fp,0,SEEK_END);
    len = ftell(fp);
    fclose(fp);
    printf("file.txt 的总大小=%ld 字节\n",len);
    return 0;
}
```

