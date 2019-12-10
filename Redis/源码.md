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

##  链表

```c
typedef struct listNode{
    //前置节点
    struct listNode *prev;
    //后置节点
    struct listNode *next;
    //当前节点
    void *value;
} listNode;

/*
 * 双端链表迭代器
 */
typedef struct listIter {

    // 当前迭代到的节点
    listNode *next;

    // 迭代的方向
    int direction;

} listIter;

typedef struct list{
    listNode *head;
    listNode *tail;
    // 节点值复制函数
    void *(*dup)(void *ptr);
    // 节点值释放函数
    void (*free)(void *ptr);
    // 节点值对比函数
    int (*match)(void *ptr, void *key);
    unsigned long len;
} list;
```

头，尾添加

```c
/*
 * 将一个包含有给定值指针 value 的新节点添加到链表的表头
 * 如果为新节点分配内存出错，那么不执行任何动作，仅返回 NULL
 * 如果执行成功，返回传入的链表指针
 * T = O(1)
 */
list *listAddNodeHead(list *list, void *value)
{
    listNode *node;

    // 为节点分配内存
    if ((node = zmalloc(sizeof(*node))) == NULL)
        return NULL;

    // 保存值指针
    node->value = value;

    // 添加节点到空链表
    if (list->len == 0) {
        list->head = list->tail = node;
        node->prev = node->next = NULL;
    // 添加节点到非空链表
    } else {
        node->prev = NULL;
        node->next = list->head;
        list->head->prev = node;
        list->head = node;
    } 

    // 更新链表节点数
    list->len++;

    return list;
}

/*
 * 将一个包含有给定值指针 value 的新节点添加到链表的表尾
 * 如果为新节点分配内存出错，那么不执行任何动作，仅返回 NULL
 * 如果执行成功，返回传入的链表指针
 * T = O(1)
 */
list *listAddNodeTail(list *list, void *value)
{
    listNode *node;

    // 为新节点分配内存
    if ((node = zmalloc(sizeof(*node))) == NULL)
        return NULL;

    // 保存值指针
    node->value = value;

    // 目标链表为空
    if (list->len == 0) {
        list->head = list->tail = node;
        node->prev = node->next = NULL;
    // 目标链表非空
    } else {
        node->prev = list->tail;
        node->next = NULL;
        list->tail->next = node;
        list->tail = node;
    }

    // 更新链表节点数
    list->len++;
    return list;
}
```

## 字典

