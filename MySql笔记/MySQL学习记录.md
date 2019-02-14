# MySQL

## MySQL的逻辑架构图

![丁奇](./image/丁奇.jpeg)

## 日志系统

### redo log（InnoDB特有的日志）

1. MySQL 的 WAL 技术，全称 write-Ahead Logging，它的关键点就是先写日志，再写磁盘。
2. **描述** 当有一条记录需要更新的时候，InnoDB 引擎就会先把记录写到 `redo log` 中，并更新内存，这个时候更新就算完成了。同时，InnoDB 引擎会在适当的时候，将这个操作记录更新到磁盘里面，而这个更新往往是在系统比较空闲的时候做。
3. InnoDB 的 `redo log` 是固定大小的，比如可以配置一组4个文件，每个文件的大小是1GB。一共可以记录4GB的操作。从头开始写，写到末尾就有回到开头循环写。
4. 有了 `redo log` ，InnoDB 就可以保证即使数据库发生异常重启，之前提交的记录都不会丢失，这个能力称为 `crash-safe`。

### binlog（Sever 层的日志）

### 区别

1. `redo log` 是 InnoDB 引擎特有的；`bin log` 是MySQL的Server层实现的，所有引擎都可以使用。
2. `redo log` 是物理日志，记录的是“在某个数据页上做了什么修改”；`bin log`  是逻辑日志，记录的是这个语句的原始逻辑，比如“给ID=2这一行的 c 字段加1”。
3. `redo log` 是循环写的，空间固定会用完；`bin log` 是可以追加写入的。“追加写”是指 `bin log` 文件写到一定大小后会切换到下一个，并不会覆盖以前的日志。

### 配置

1. `redo log` 用于保证 crash-safe 能力。 `innodb_flush_log_at_trx_commit` 这个参数设置成1的时候，表示每次事务的 `redo log` 都直接持久化到磁盘。这样可以保证 MySQL 异常重启之后数据不丢失。

2. `sync_binlog` 这个参数设置成1的时候，表示每次事务的 `bin log` 持久化到磁盘。这样可以保证 MySQL 异常重启之后 `bin log`  不丢失。

