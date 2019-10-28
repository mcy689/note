# 数据结构

## 字符串

### 定义

```c
//类型别名，用于指向 sdshdr 的 buf 属性
typedef char *sds;

struct sdshdr
{
    //buf 中已占用空间的长度
    int len;
    //buf 中剩余可用的长度
    int free;
    //字符串实际保存空间
    char buf[];
};
```

### sdshdr 大小

```c
sizeof(struct sdshdr); //8 sizeof(int) + sizeof(int) + 0
```

1. 在gcc中，当我们创建长度为零的数组时，它被视为不完整类型的数组，这就是gcc将其大小报告为“ 0”字节的原因。该技术被称为**Stuct Hack**。
2. 当在结构内部创建零长度的数组时，它必须是（并且仅是）结构的最后一个成员。

### sds 和 sdshdr 相互转化

1. sds 到 sdshdr 转化

   ```c
   //通过sdshdr 取 buf 地址，强制转成 char * ，也就是sds
   struct sdshdr *sh;
   sds mystring = (char*)sh->buf
   ```

2. sdshdr 到 sds 转化

   ```c
   static inline size_t sdslen(const sds s) {
       //当我们获得sds 的时候，想知道预分配长度，直接将指针向左偏移 struct 大小，就是sdshdr的地址，强制转换类型后，就额可以取len。
       struct sdshdr *sh = (void*)(s-(sizeof(struct sdshdr)));
       return sh->len;
   }
   ```

### 惰性空间释放

```c
void sdsclear(sds s) {
    // 取出 sdshdr
    struct sdshdr *sh = (void*) (s-(sizeof(struct sdshdr)));
    // 重新计算属性
    sh->free += sh->len;
    sh->len = 0;
    // 将结束符放到最前面（相当于惰性地删除 buf 中的内容）
    sh->buf[0] = '\0';
}
```

