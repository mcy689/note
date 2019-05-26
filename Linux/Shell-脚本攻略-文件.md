# 文件

* `/dev/zero` 是一个字符设备，它会不断返回0值字节`\0`

## 生成任意大小的文件

```shell
dd if=/dev/zero of=junk.data bs=1M count=1 #生成一个1M的文件
```

