## 简介

protobuf 即 Google Protocol Buffer，是一种轻便高效的结构化数据存储格式，与语言、平台无关，可扩展可序列化。protobuf 性能和效率大幅度优于 JSON、XML 等其他的结构化数据格式。protobuf 是以二进制方式存储的，占用空间小，但也带来了可读性差的缺点。

Protobuf 在 `.proto` 定义需要处理的结构化数据，可以通过 `protoc` 工具，将 `.proto` 文件转换为 C、C++、Golang、Java、Python 等多种语言的代码。

## 说明

[1,2] 表示能取到这个数。表示：`1,2` 。(1,3) 表示不能取到这个数。表示：`2`

## 语法规则

假设定义个搜索请求的消息格式：1. 每个搜索请求都有一个查询字符串，2. 你感兴趣的查询结果所在的页数，3. 每页的结果数量。将下面的结构保存为`.proto` 结尾的文件。

```protobuf
message SearchRequest {
  required string query = 1;
  optional int32 page_number = 2;
  optional int32 result_per_page = 3;
}
```

### 分配标识符

1. 每个字段都有一个唯一的数字标识符。
2. 最小的标识符为1。最大 2^29-1 or 536,870,911。不可以使用 [19000,19999] （这段标识符是协议本身保留使用）。
3. 这些标识符是用来在消息的二进制格式中识别各个字段的，一旦开始使用就不能够再改变。所以应该为那些频繁出现的消息元素保留 [1,15]之内的标识号。切记：要为将来有可能添加的、频繁出现的标识号预留一些标识号。

### 修饰字段规则

1. `required` ，表示是一个必须字段。**`proto2` 支持修饰符，`proto3` 不支持了** 。

2. `optional`：表示是一个可选字段。**`proto2` 支持修饰符，`proto3` 不支持了** 。

3. `repeated` 可以重复任意多次（包括0次）。重复的值的顺序会被保留。

   由于一些历史原因，基本数值类型的repeated的字段并没有被尽可能地高效编码。在新的代码中，用户应该使用特殊选项[packed=true]来保证更高效的编码。

   ```protobuf
   repeated int32 samples = 4 [packed = true];
   ```

   ```go
   //例子
   /*
     message Student {
       string name = 1;
       bool male = 2;
       repeated int32 scores = 3;
     }
   */
   
   //go 中的结构体
   test := Student{
   		Name: "geektutu",
   		Male:  true,
   		Scores: []int32{98, 85, 88},
   }
   ```

### 添加注释

```protobuf
/* SearchRequest represents a search query, with pagination options to
 * indicate which results to include in the response. */

message SearchRequest {
  required string query = 1;
  optional int32 page_number = 2;  // Which page number do we want?
  optional int32 result_per_page = 3;  // Number of results to return per page.
}
```

## 从`.proto` 文件生成了什么？

当你在.proto上运行协议缓冲区编译器时，编译器会用你选择的语言生成你需要的代码，以处理你在文件中描述的消息类型，包括获取和设置字段值，将你的消息序列化到输出流中，以及从输入流中解析你的消息。

* 对于 Go，编译器会生成一个 .pb.go 文件，为你的文件中的每个消息类型提供一个类型。

## 标量数值类型

| .proto type | 备注                                                         | go type  |
| ----------- | :----------------------------------------------------------- | -------- |
| double      |                                                              | *float64 |
| float       |                                                              | *float32 |
| int32       | 使用可变长编码方式。编码负数时不够高效——如果你的字段可能含有负数，那么请使用sint32。 | *int32   |
| int64       | 使用可变长编码方式。编码负数时不够高效——如果你的字段可能含有负数，那么请使用sint64。 | *int64   |
| uint32      |                                                              | *uint32  |
| uint64      |                                                              | *uint64  |
| sint32      | 使用可变长编码方式。有符号的整型值。编码时比通常的int32高效  | *int32   |
| sint64      | 使用可变长编码方式。有符号的整型值。编码时比通常的int64高效  | *int64   |
| fixed32     | 总是4个字节。如果数值总是比总是比228大的话，这个类型会比uint32高效 | *uint32  |
| fixed64     | 总是8个字节。如果数值总是比总是比256大的话，这个类型会比uint64高效 | *uint64  |
| sfixed32    | 总是4个字节                                                  | *int32   |
| sfixed64    | 总是8个字节                                                  | *int64   |
| bool        |                                                              | *bool    |
| string      | 一个字符串必须始终包含UTF-8编码或7位ASCII文本                | *string  |
| bytes       | 可以包含任何任意的字节序列                                   | []byte   |

## Optional的字段和默认值

消息描述中的元素可以被标记为可选的。一个格式良好的消息可能包含也可能不包含可选元素。当一条消息被解析时，如果它不包含一个可选元素，解析对象中的相应字段就会被设置为该字段的默认值。默认值可以作为消息描述的一部分来指定。例如，假设你想为SearchRequest 的 result_per_page 值提供一个10的默认值。

```protobuf
optional int32 result_per_page = 3 [default = 10];
```

如果没有为`optional`的元素指定默认值，就会使用与特定类型相关的默认值：对`string`来说，默认值是空字符串。对`bool`来说，默认值是false。对数值类型来说，默认值是0。对枚举来说，默认值是枚举类型定义中的第一个值。

## 使用

### 安装

```html
//mac
  查找    brew search protobuf
  安装    brew install protobuf
  检查安装 protoc --version
```

### go 的依赖包

```shell
go get -u github.com/golang/protobuf/protoc-gen-go
```

