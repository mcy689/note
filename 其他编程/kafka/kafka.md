## 基础操作

### 主题管理

1. 创建主题

   ```shell
   #创建一个有3个分区，2个副本，分别分布在3个节点上
   kafka-topics.sh --create --zookeeper server-1:2181 server-2:2181 server-3:2181 --replication-factor 2 --partitions 3 --topic test-topic
   # partitions 参数用于设置主题分区数，该配置为必传参数。kafka通过分区策略，将一个主题的消息分散到多个分区并保存到不同的代理上。
   # replication-factor 参数用来设置主题副本数，该配置为必传参数，副本会被分布在不同的节点上，副本数不能超过节点数，否则创建主题会失败。
   ```

2. 删除主题

   >执行 kafka-topics.sh 脚本删除
   >
   >需要保证在启动 Kafka时所加载的 `server.properties` 文件中配置 `delete.topic.enable = true`,该配置默认为 `false`。否则执行该脚本并未真正删除主题。

   ```shell
   kafka-topics.sh --delete --zookeeper server-1:2181 server-2:2181 server-3:2181 --topic  test-topic
   ```

3. 查看主题

   ```shell
   #查看所有主题
   kafka-topics.sh --list --zookeeper server-1:2181 server-2:2181 server-3:2181
   #查看某个特定主题信息
   kafka-topics.sh --describe --zookeeper server-1:2181 server-2:2181 server-3:2181
   ```

4. 修改主题

   ```shell
   #增加分区
   kafka-topics.sh --alter --zookeeper server-1:2181 server-2:2181 server-3:2181 --partitions 4 --topic test-topic
   ```

