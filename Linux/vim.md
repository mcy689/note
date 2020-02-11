# Vim

## 开启行号显示及全局设置

* 复制vim的配置文件

  ```
  cp /etc/vimrc /etc/vimrc.bak
  ```

* 修改配置文件( 并保存 )

  ```
  vim /etc/vimrc
  进入insert模式下, 在最后新起一行新增  set nu!
  ```

* 其他配置项

  ```html
  set nocompatible            "去掉有关vi一致性模式，避免以前版本的bug和局限    
  set nu!                     "显示行号
  set guifont=Luxi/ Mono/ 9   " 设置字体，字体名称和字号
  filetype on                 "检测文件的类型
  set history=1000            "记录历史的行数
  set background=dark         "背景使用黑色
  syntax on                   "语法高亮度显示
  set autoindent           "vim使用自动对齐，也就是把当前行的对齐格式应用到下一行(自动缩进）
  set tabstop=4            "设置tab键为4个空格，
  set shiftwidth =4        "设置当行之间交错时使用4个空格     
  set ai!                  "设置自动缩进 
  set incsearch            "在程序中查询一单词，自动匹配单词的位置；如查询desk单词，当输到/d时，会自动找到第一个d开头的单词，当输入到/de时，会自动找到第一个以ds开头的单词，以此类推，进行查找；当找到要匹配的单词时，别忘记回车
  ```


## 命令

```shell
#打开一个文件并进行定位
	vim +5 test.c
#如果希望在进入 vi 之后光标处于文件最末行
	vim + test.c
#d0 命令：该命令删除从光标前一个字符开始到行首的内容。
#dd 命令：该命令删除光标所在的整行。在 dd 前可加上一个数字 n，表示删除当前行及其后 n-1 行的内容。
#y0 复制从光标前一个字符开始到行首的内容。
#yy 命令：复制光标所在的整行。在 yy 前可加一个数字 n，表示复制当前行及其后 n-1 行的内容。
#p 命令：粘贴命令，粘贴当前缓冲区中的内容。
#u   撤销上一步的操作
#Ctrl+r 恢复上一步被撤销的操作
```

