# MySQL

## MySQL的逻辑架构图

![丁奇](./image/丁奇.jpeg)

## 日志系统

### redo.log

1. MySQL 的 WAL 技术，全称 write-Ahead Logging，它的关键点就是先写日志，再写磁盘。
2. **描述** 当有一条记录需要更新的时候，InnoDB 引擎就会先把记录写

