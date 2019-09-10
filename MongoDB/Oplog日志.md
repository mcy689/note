## Oplog 日志

1. oplog（操作日志）是一个特殊的[固定集合](<https://docs.mongodb.com/manual/reference/glossary/#term-capped-collection>)，它保存了修改存储在数据库中数据的所有操作的滚动记录。
2. MongoDB在[主要成员](https://docs.mongodb.com/manual/reference/glossary/#term-primary)上应用数据库操作，然后在主要成员的oplog上记录操作。然后，[辅助](https://docs.mongodb.com/manual/reference/glossary/#term-secondary)成员在异步过程中复制并应用这些操作。所有副本集成员都在集合中包含oplog的副本 [`local.oplog.rs`](https://docs.mongodb.com/manual/reference/local-database/#local.oplog.rs)，这允许它们维护数据库的当前状态。
3. 为了便于复制，所有副本集成员都会向所有其他成员发送心跳（ping）。任何辅助成员都可以从任何其他成员导入 oplog 条目。
4. oplog中的每个操作都是幂等的。也就是说，无论是对目标数据集应用一次还是多次，oplog操作都会产生相同的结果。

## [oplog 大小](<https://docs.mongodb.com/manual/core/replica-set-oplog/>)

## 需要更大的oplog场景

如果您可以预测副本集的工作负载类似于以下模式之一，那么您可能希望创建一个大于默认值的oplog。相反，如果您的应用程序主要使用最少量的写入操作执行读取，则较小的oplog可能就足够了。

1. **一次更新多个文件** ：oplog 必须将多个更新转换为单个操作，以保持[幂等性](https://docs.mongodb.com/manual/reference/glossary/#term-idempotent)。这可以使用大量的oplog空间，而不会相应增加数据大小或磁盘使用。
2. **删除等于与插入相同的数据量** ：如果删除的数据大致与插入的数据大致相同，则数据库在磁盘使用中不会显着增长，但操作日志的大小可能非常大。
3. **大量的更新**：如果工作负载的很大一部分是不会增加文档大小的更新，则数据库会记录大量操作，但不会更改磁盘上的数据量。

## Oplog 状态

1. [`rs.printReplicationInfo()`](https://docs.mongodb.com/manual/reference/method/rs.printReplicationInfo/#rs.printReplicationInfo) 要查看oplog状态（包括操作的大小和时间范围）
2. 使用 [`db.getReplicationInfo()`](https://docs.mongodb.com/manual/reference/method/db.getReplicationInfo/#db.getReplicationInfo)辅助成员和 [复制状态](https://docs.mongodb.com/manual/reference/method/db.getReplicationInfo/) 输出来评估当前的复制状态，并确定是否存在任何意外的复制延迟。

## 副本集修改 Oplog

1. 如果副本集执行身份验证，则必须以具有修改本地数据库权限的用户身份进行身份验证，例如`clusterManager` 或`clusterAdmin` 角色。

2. 查看当前副本集的 oplog 大小

   ```shell
   #该maxSize字段显示集合大小（以字节为单位）。
   use local
   db.oplog.rs.stats().maxSize
   ```

3. 更改副本集成员的oplog的大小

   ```shell
   #将所需大小（以兆字节为单位）作为size参数传递。指定的大小必须大于990或等于990兆字节。
   use local
   db.adminCommand({replSetResizeOplog:1,size:1000})
   ```

4. 减小oplog的大小**不会** 自动回收分配给原始oplog大小的磁盘空间。

   ```shell
   use local
   db.runCommand({"compact":"oplog.rs"})
   ```

   **对于集合中的 `compact` 命令需要的特殊权限，详细参看[官网文档](https://docs.mongodb.com/manual/reference/command/compact/#compact-authentication)**

